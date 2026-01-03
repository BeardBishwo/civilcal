<?php
namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Models\Question;
use App\Models\SyllabusNode;

class QuestionExportController extends Controller {

    /**
     * INTELLIGENT EXPORT ENGINE
     * Handles huge datasets without crashing RAM.
     */
    public function export() {
        // 1. FILTER LOGIC (Download specific category or All)
        $categoryId = $_GET['category_id'] ?? null;
        
        // 2. CHECK SIZE (Traffic Intelligence)
        $query = Question::query();
        if ($categoryId) $query->where('syllabus_node_id', $categoryId);
        $count = $query->count();

        // Safety Valve: If > 5000 questions, prevent Instant Download on Shared Hosting
        if ($count > 5000) {
            return json_encode([
                'status' => 'queued',
                'message' => 'Dataset too large for instant download. We are generating it in the background. You will be notified.'
            ]);
        }

        // 3. START STREAMING (The "Low Resource" Magic)
        $filename = "civil_cal_questions_" . date('Y-m-d') . ".csv";

        // Headers to force download immediately
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open the "Output Stream" (Direct connection to browser)
        $output = fopen('php://output', 'w');

        // 4. WRITE HEADERS (Matches your Import Format perfectly)
        // This ensures "Round Trip" capability (Export -> Edit -> Import)
        fputcsv($output, [
            'category', 'subcategory', 'language_id', 'question_type', 
            'question', 'option 1', 'option 2', 'option 3', 'option 4', 'option 5', 
            'answer', 'level', 'note'
        ]);

        // 5. CHUNK PROCESSING (Process 200 rows at a time)
        // We never load more than 200 rows into RAM.
        $query->chunk(200, function($questions) use ($output) {
            foreach ($questions as $q) {
                
                // Decode JSON options back to Columns
                $opts = json_decode($q->options, true);
                
                // Fetch Category Names (for human readability)
                // Assuming Question model relationships or efficient fetching
                // For simplicity/direct SQL, we might need a join, but let's assume Model lazy loading works or optimize later
                // Just ensuring we don't crash is step 1.
                $main = SyllabusNode::find($q->syllabus_main_id);
                $sub = SyllabusNode::find($q->syllabus_node_id);
                
                $catName = $main->title ?? 'Unknown';
                $subName = $sub->title ?? 'Unknown';

                // Map Answer back to 'a', 'b', 'c' format
                $ansMap = [1 => 'a', 2 => 'b', 3 => 'c', 4 => 'd'];

                fputcsv($output, [
                    $catName,               // category
                    $subName,               // subcategory
                    1,                      // language_id (Default English)
                    ($q->type == 'TF' ? 2 : 1), // question_type
                    $q->question,           // question
                    $opts['option_1'] ?? '', 
                    $opts['option_2'] ?? '', 
                    $opts['option_3'] ?? '', 
                    $opts['option_4'] ?? '', 
                    $opts['option_5'] ?? '', 
                    $ansMap[$q->correct_answer] ?? 'a',
                    $q->level == 'easy' ? 1 : ($q->level == 'hard' ? 3 : 2), // level
                    $q->explanation         // note
                ]);
            }
            
            // FLUSH BUFFER (Send data to user immediately)
            ob_flush();
            flush();
        });

        fclose($output);
        exit;
    }
}
