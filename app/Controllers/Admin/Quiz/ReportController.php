<?php

namespace App\Controllers\Admin\Quiz;

use App\Core\Controller;
use App\Models\QuestionReport;
use App\Models\Question;

class ReportController extends Controller
{

    public function index()
    {
        try {
            $db = \App\Core\Database::getInstance();

            // Group by Question ID to handle duplicates
            $reports = $db->query("
                SELECT r.question_id, 
                       COUNT(r.id) as report_count,
                       MIN(r.created_at) as first_report_at,
                       MAX(r.created_at) as last_report_at,
                       MAX(r.issue_type) as primary_issue,
                       GROUP_CONCAT(DISTINCT r.description SEPARATOR ' || ') as all_descriptions,
                       GROUP_CONCAT(DISTINCT u.username ORDER BY r.created_at ASC SEPARATOR ', ') as reporters,
                       
                       q.content as question_json,
                       q.type as q_type,
                       q.difficulty_level,
                       sn_cat.title as category_title,
                       sn_course.title as course_title,

                       -- Priority Score Calculation (Consensus & Evidence Update - Phase 9)
                       (CASE WHEN MAX(r.issue_type) = 'wrong_answer' THEN 2 ELSE 0 END) +
                       (CASE WHEN COUNT(r.id) >= 3 THEN 5 ELSE 0 END) + -- Consensus Weight
                       (CASE WHEN MAX(r.screenshot) IS NOT NULL THEN 1 ELSE 0 END) + -- Evidence Weight
                       (CASE WHEN (
                           SELECT (SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) / COUNT(*)) * 100 
                           FROM question_reports qr 
                           WHERE qr.user_id = MIN(r.user_id)
                       ) >= 80 THEN 1 ELSE 0 END) as priority_score
                FROM question_reports r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN quiz_questions q ON r.question_id = q.id
                LEFT JOIN syllabus_nodes sn_cat ON q.category_id = sn_cat.id
                LEFT JOIN syllabus_nodes sn_course ON q.course_id = sn_course.id
                WHERE r.status = 'pending'
                GROUP BY r.question_id
                ORDER BY priority_score DESC, first_report_at ASC
            ")->fetchAll();

            // Format for View
            $formatted = [];
            foreach ($reports as $r) {
                // Determine First Reporter (the one at the top of the reporters list)
                $reporterList = explode(', ', $r['reporters']);
                $firstReporter = $reporterList[0] ?? 'Guest';

                // We need the first reporter's user_id for trust. 
                // Since our query doesn't give us the MIN(u.id), we'll derive it by fetching the report detail.
                $firstReport = $db->query("SELECT user_id FROM question_reports WHERE question_id = ? ORDER BY created_at ASC LIMIT 1", [$r['question_id']])->fetch();
                $firstReporterId = $firstReport['user_id'] ?? null;
                $firstReporterTrust = $firstReporterId ? $this->getReporterTrust($firstReporterId) : ['status' => 'Guest', 'class' => 'bg-slate-100 text-slate-500', 'score' => ''];

                $formatted[] = [
                    'question_id' => $r['question_id'],
                    'report_count' => $r['report_count'],
                    'issue_type' => $r['primary_issue'],
                    'descriptions' => array_unique(array_filter(explode(' || ', $r['all_descriptions']))),
                    'created_at' => $r['first_report_at'],
                    'reporters' => $r['reporters'],

                    // Question Data
                    'q_type' => $r['q_type'],
                    'difficulty_level' => $r['difficulty_level'],
                    'category_title' => $r['category_title'] ?? '-',
                    'course_title' => $r['course_title'] ?? '-',
                    'content' => json_decode($r['question_json'] ?? '{}', true) ?: ['text' => '<span class="text-red-500 font-bold">[DELETED QUESTION]</span>'],
                    'first_reporter' => $firstReporter,
                    'first_reporter_trust' => $firstReporterTrust,
                ];
            }

            // Fetch defaults for View (to pre-fill modals)
            $defaults = [
                'msg_first' => \App\Services\SettingsService::get('report_notification_first', ''),
                'msg_sub' => \App\Services\SettingsService::get('report_notification_subsequent', '')
            ];

            $this->view('admin/quiz/reports/index', [
                'page_title' => 'Issue Reports',
                'reports' => $formatted,
                'stats' => ['total' => count($formatted)],
                'defaults' => $defaults
            ]);
        } catch (\Exception $e) {
            // Log the error and show a friendly message
            error_log("Report Controller Error: " . $e->getMessage());
            $this->view('admin/quiz/reports/index', [
                'reports' => [],
                'count' => 0,
                'error' => 'Unable to load reports. Please check the database connection.',
                'defaults' => []
            ]);
        }
    }

    /**
     * Action: Mark as Resolved (e.g. after you fixed the typo)
     */
    public function resolve()
    {
        $questionId = $_POST['id'] ?? null;
        $reportId = $_POST['report_id'] ?? null;
        $replyMessage = $_POST['reply_message'] ?? null;

        $db = \App\Core\Database::getInstance();
        $settings = new \App\Services\SettingsService();
        $gamification = new \App\Services\GamificationService();
        $notification = new \App\Services\NotificationService();

        try {
            $db->beginTransaction();

            $query = "SELECT * FROM question_reports WHERE status = 'pending'";
            $params = [];

            if ($reportId) {
                $query .= " AND id = ?";
                $params[] = $reportId;
            } else {
                $query .= " AND question_id = ?";
                $params[] = $questionId;
            }
            $query .= " ORDER BY created_at ASC";

            $reports = $db->query($query, $params)->fetchAll();

            if (empty($reports)) {
                $db->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'No pending reports found']);
                return;
            }

            // Get Settings & Defaults
            $rewardFirst = (int) $settings::get('report_reward_coins', 0);
            $rewardSub = (int) $settings::get('report_reward_subsequent', 0);
            $notifTitle = $settings::get('report_notification_title', 'Report Resolved');
            $msgFirstTemplate = $settings::get('report_notification_first', 'Your eagle eyes helped us! You earned {coins} coins.');
            $msgSubTemplate = $settings::get('report_notification_subsequent', 'Thank you for reporting {issue}. You earned {coins} coins.');

            // If we are resolving a SPECIFIC report, we treat it as "First" for that user
            // Unless it's a bulk action, then only the absolute first gets the prize.
            $isFirstInBatch = true;

            foreach ($reports as $report) {
                $db->update('question_reports', ['status' => 'resolved'], "id = :id", ['id' => $report['id']]);

                $userId = $report['user_id'];
                if (!$userId) continue;

                // Determine reward
                // If resolving bulk, only the oldest report gets the "First" reward.
                // If resolving single, the user gets the "First" reward for their effort.
                $amount = ($isFirstInBatch || $reportId) ? $rewardFirst : $rewardSub;

                // Prepare Message with Placeholders
                $template = ($isFirstInBatch || $reportId) ? ($replyMessage ?: $msgFirstTemplate) : $msgSubTemplate;

                $message = str_replace(
                    ['{issue}', '{coins}'],
                    [$report['issue_type'] ?: 'an issue', $amount],
                    $template
                );

                // Grant Reward
                if ($amount > 0) {
                    $gamification->rewardReportVerified($userId, $amount, $report['id']);
                }

                // Send Notification
                $notification->send($userId, 'info', $notifTitle, $message, [
                    'icon' => '✅',
                    'action_url' => app_base_url('quiz/practice')
                ]);

                $isFirstInBatch = false;
            }

            $db->commit();
            echo json_encode(['status' => 'success', 'count' => count($reports)]);
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("Resolve Report Error: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Action: Ignore (If student was wrong)
     */
    public function ignore()
    {
        $questionId = $_POST['id'] ?? null;
        $reportId = $_POST['report_id'] ?? null;
        $replyMessage = $_POST['reply_message'] ?? null;

        $db = \App\Core\Database::getInstance();
        $notification = new \App\Services\NotificationService();

        try {
            $db->beginTransaction();

            $query = "SELECT * FROM question_reports WHERE status = 'pending'";
            $params = [];

            if ($reportId) {
                $query .= " AND id = ?";
                $params[] = $reportId;
            } else {
                $query .= " AND question_id = ?";
                $params[] = $questionId;
            }

            $reports = $db->query($query, $params)->fetchAll();

            foreach ($reports as $report) {
                $db->update('question_reports', ['status' => 'ignored'], "id = :id", ['id' => $report['id']]);

                // Prepare Message with Placeholders
                if (!empty($replyMessage) && !empty($report['user_id'])) {
                    $message = str_replace(
                        ['{issue}'],
                        [$report['issue_type'] ?: 'an issue'],
                        $replyMessage
                    );

                    $notification->send($report['user_id'], 'warning', 'Report Feedback', $message, [
                        'icon' => 'ℹ️'
                    ]);
                }
            }

            $db->commit();
            echo json_encode(['status' => 'success']);
        } catch (\Exception $e) {
            $db->rollBack();
            error_log("Ignore Report Error: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Server Error']);
        }
    }

    /**
     * AJAX Action: Get all reporters for a question
     */
    public function getReporters()
    {
        $questionId = $_GET['id'] ?? null;
        if (!$questionId) {
            echo json_encode([]);
            return;
        }

        $db = \App\Core\Database::getInstance();
        $reporters = $db->query("
            SELECT r.*, u.username, u.email
            FROM question_reports r
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.question_id = ? AND r.status = 'pending'
            ORDER BY r.created_at ASC
        ", [$questionId])->fetchAll();

        // Add trust score for each reporter
        foreach ($reporters as &$r) {
            if ($r['user_id']) {
                $r['trust'] = $this->getReporterTrust($r['user_id']);
            } else {
                $r['trust'] = ['status' => 'Guest', 'score' => 0, 'class' => 'bg-slate-100 text-slate-500'];
            }
        }

        echo json_encode($reporters);
    }

    /**
     * Internal: Calculate Reporter Karma
     */
    private function getReporterTrust($userId)
    {
        $db = \App\Core\Database::getInstance();
        $stats = $db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                SUM(CASE WHEN status = 'ignored' THEN 1 ELSE 0 END) as ignored
            FROM question_reports 
            WHERE user_id = ?
        ", [$userId])->fetch();

        $total = (int) $stats['total'];
        $resolved = (int) $stats['resolved'];
        $ignored = (int) $stats['ignored'];

        if ($total < 3) {
            return ['status' => 'Newbie', 'score' => $total, 'class' => 'bg-blue-50 text-blue-600'];
        }

        $rate = ($total > 0) ? ($resolved / $total) * 100 : 0;

        if ($rate >= 80) return ['status' => 'Trusted', 'score' => round($rate) . '%', 'class' => 'bg-emerald-50 text-emerald-600'];
        if ($rate >= 50) return ['status' => 'Neutral', 'score' => round($rate) . '%', 'class' => 'bg-amber-50 text-amber-600'];

        return ['status' => 'Suspicious', 'score' => round($rate) . '%', 'class' => 'bg-red-50 text-red-600'];
    }

    /**
     * Internal/Direct: Auto-resolve all pending reports for a question (Used by QuestionBank)
     */
    public function autoResolveForQuestion($questionId, $replyMessage = null)
    {
        $db = \App\Core\Database::getInstance();
        $settings = new \App\Services\SettingsService();
        $gamification = new \App\Services\GamificationService();
        $notification = new \App\Services\NotificationService();

        $reports = $db->query("
            SELECT * FROM question_reports 
            WHERE question_id = ? AND status = 'pending' 
            ORDER BY created_at ASC
        ", [$questionId])->fetchAll();

        if (empty($reports)) return 0;

        $rewardFirst = (int) $settings::get('report_reward_coins', 0);
        $rewardSub = (int) $settings::get('report_reward_subsequent', 0);
        $notifTitle = $settings::get('report_notification_title', 'Report Resolved');
        $msgFirstTemplate = $settings::get('report_notification_first', 'Resolved via system update! You earned {coins} coins.');
        $msgSubTemplate = $settings::get('report_notification_subsequent', 'Resolved via system update. You earned {coins} coins.');

        $isFirst = true;
        foreach ($reports as $report) {
            $db->update('question_reports', ['status' => 'resolved'], "id = :id", ['id' => $report['id']]);

            if ($report['user_id']) {
                $amount = $isFirst ? $rewardFirst : $rewardSub;
                $template = $isFirst ? ($replyMessage ?: $msgFirstTemplate) : $msgSubTemplate;

                $message = str_replace(
                    ['{issue}', '{coins}'],
                    [$report['issue_type'] ?: 'an issue', $amount],
                    $template
                );

                if ($amount > 0) {
                    $gamification->rewardReportVerified($report['user_id'], $amount, $report['id']);
                }

                $notification->send($report['user_id'], 'info', $notifTitle, $message, [
                    'icon' => '✅',
                    'action_url' => app_base_url('user/reports')
                ]);
            }
            $isFirst = false;
        }

        return count($reports);
    }
}
