<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Session;
use App\Models\CalculationHistory;
use App\Core\Database;

class HistoryController {
    
    public function index() {
        // Check if user is logged in
        $auth = new Auth();
        if (!$auth->check()) {
            return header('Location: /login');
        }

        $user = $auth->user();
        $history = CalculationHistory::getUserHistory($user->id);
        
        // Include the view
        include __DIR__ . '/../Views/user/history.php';
    }

    public function saveCalculation() {
        $auth = new Auth();
        if (!$auth->check()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $user = $auth->user();
        $data = $_POST ?? [];

        $historyModel = new CalculationHistory();
        $success = $historyModel->saveCalculation(
            $user->id, 
            $data['calculator_type'] ?? 'Unknown', 
            $data['inputs'] ?? [], 
            $data['results'] ?? [], 
            $data['title'] ?? null
        );
        
        header('Content-Type: application/json');
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Calculation saved']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save']);
        }
    }

    public function delete($id) {
        $auth = new Auth();
        if (!$auth->check()) {
            return header('Location: /login');
        }

        $user = $auth->user();
        $historyModel = new CalculationHistory();
        
        if ($historyModel->deleteCalculation($id, $user->id)) {
            Session::setFlash('success', 'Calculation deleted successfully.');
        } else {
            Session::setFlash('error', 'Calculation not found or access denied.');
        }

        return header('Location: /history');
    }

    public function toggleFavorite($id) {
        $auth = new Auth();
        if (!$auth->check()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false]);
            return;
        }

        $user = $auth->user();
        $historyModel = new CalculationHistory();
        $newStatus = $historyModel->toggleFavorite($id, $user->id);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'is_favorite' => $newStatus
        ]);
    }

    public function search() {
        $auth = new Auth();
        if (!$auth->check()) {
            return header('Location: /login');
        }

        $user = $auth->user();
        $searchTerm = $_GET['q'] ?? '';

        if (empty($searchTerm)) {
            return header('Location: /history');
        }

        $history = CalculationHistory::searchHistory($user->id, $searchTerm);

        // Include the view with search results
        include __DIR__ . '/../Views/user/history.php';
    }

    public function view($id) {
        $auth = new Auth();
        if (!$auth->check()) {
            return header('Location: /login');
        }

        $user = $auth->user();
        $historyModel = new CalculationHistory();
        $calculation = $historyModel->getById($id, $user->id);

        if (!$calculation) {
            Session::setFlash('error', 'Calculation not found.');
            return header('Location: /history');
        }

        // Include the view for individual calculation
        include __DIR__ . '/../Views/user/history-view.php';
    }

    public function stats() {
        $auth = new Auth();
        if (!$auth->check()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not logged in']);
            return;
        }

        $user = $auth->user();
        $historyModel = new CalculationHistory();
        $stats = $historyModel->getUserStats($user->id);
        
        header('Content-Type: application/json');
        echo json_encode($stats);
    }

    public function recent() {
        $auth = new Auth();
        if (!$auth->check()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not logged in']);
            return;
        }

        $user = $auth->user();
        $limit = $_GET['limit'] ?? 10;
        $historyModel = new CalculationHistory();
        $recent = $historyModel->getRecentCalculations($user->id, $limit);
        
        header('Content-Type: application/json');
        echo json_encode($recent);
    }

    public function byType($calculatorType) {
        $auth = new Auth();
        if (!$auth->check()) {
            return header('Location: /login');
        }

        $user = $auth->user();
        $historyModel = new CalculationHistory();
        $history = $historyModel->getByCalculatorType($user->id, $calculatorType);

        // Include the view filtered by calculator type
        include __DIR__ . '/../Views/user/history.php';
    }

    public function export() {
        $auth = new Auth();
        if (!$auth->check()) {
            return header('Location: /login');
        }

        $user = $auth->user();
        $format = $_GET['format'] ?? 'csv';
        
        $historyModel = new CalculationHistory();
        $history = $historyModel->getUserHistory($user->id, 1000); // Get all history
        
        switch ($format) {
            case 'csv':
                $this->exportAsCSV($history);
                break;
            case 'json':
                $this->exportAsJSON($history);
                break;
            default:
                Session::setFlash('error', 'Invalid export format.');
                return header('Location: /history');
        }
    }

    private function exportAsCSV($history) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="calculation_history.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV header
        fputcsv($output, [
            'Date', 'Calculator Type', 'Title', 'Inputs', 'Results', 'Tags', 'Favorite'
        ]);
        
        // Data rows
        foreach ($history as $row) {
            fputcsv($output, [
                $row['calculation_date'],
                $row['calculator_type'],
                $row['calculation_title'],
                json_encode($row['input_data']),
                json_encode($row['result_data']),
                $row['tags'],
                $row['is_favorite'] ? 'Yes' : 'No'
            ]);
        }
        
        fclose($output);
    }

    private function exportAsJSON($history) {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="calculation_history.json"');
        
        echo json_encode([
            'export_date' => date('Y-m-d H:i:s'),
            'total_calculations' => count($history),
            'calculations' => $history
        ], JSON_PRETTY_PRINT);
    }

    public function clearAll() {
        $auth = new Auth();
        if (!$auth->check()) {
            return header('Location: /login');
        }

        $user = $auth->user();
        $historyModel = new CalculationHistory();
        
        // This would need to be implemented in the model
        // $historyModel->clearUserHistory($user->id);
        
        Session::setFlash('success', 'History cleared successfully.');
        return header('Location: /history');
    }

    public function bulkDelete() {
        $auth = new Auth();
        if (!$auth->check()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $user = $auth->user();
        $ids = $_POST['ids'] ?? [];
        $historyModel = new CalculationHistory();
        
        $deleted = 0;
        foreach ($ids as $id) {
            if ($historyModel->deleteCalculation($id, $user->id)) {
                $deleted++;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'deleted' => $deleted,
            'message' => "Deleted $deleted calculations"
        ]);
    }

    public function bulkFavorite() {
        $auth = new Auth();
        if (!$auth->check()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }

        $user = $auth->user();
        $ids = $_POST['ids'] ?? [];
        $action = $_POST['action'] ?? 'add'; // 'add' or 'remove'
        $historyModel = new CalculationHistory();
        
        $updated = 0;
        foreach ($ids as $id) {
            if ($action === 'add') {
                // Set as favorite
                $db = Database::getInstance();
                $stmt = $db->getPdo()->prepare("
                    UPDATE calculation_history 
                    SET is_favorite = 1 
                    WHERE id = ? AND user_id = ?
                ");
                if ($stmt->execute([$id, $user->id])) {
                    $updated++;
                }
            } else {
                // Remove from favorites
                $db = Database::getInstance();
                $stmt = $db->getPdo()->prepare("
                    UPDATE calculation_history 
                    SET is_favorite = 0 
                    WHERE id = ? AND user_id = ?
                ");
                if ($stmt->execute([$id, $user->id])) {
                    $updated++;
                }
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'updated' => $updated,
            'message' => "Updated $updated calculations"
        ]);
    }
}
