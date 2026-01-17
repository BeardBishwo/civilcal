<?php

namespace App\Services;

use App\Core\Database;
use App\Services\EmailService;
use Exception;

class EmailQueueService
{
    private $db;
    private $emailService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->emailService = new EmailService();
    }

    /**
     * Push an email to the queue
     */
    public function push($recipientEmail, $recipientName, $subject, $body)
    {
        $stmt = $this->db->prepare("
            INSERT INTO email_queue (recipient_email, recipient_name, subject, body, status, created_at)
            VALUES (?, ?, ?, ?, 'pending', NOW())
        ");
        return $stmt->execute([$recipientEmail, $recipientName, $subject, $body]);
    }

    /**
     * Push bulk emails to the queue
     */
    public function pushBulk($recipients, $subject, $body)
    {
        // $recipients = [['email' => '...', 'name' => '...'], ...]
        if (empty($recipients)) return 0;

        $sql = "INSERT INTO email_queue (recipient_email, recipient_name, subject, body, status, created_at) VALUES ";
        $placeholders = [];
        $params = [];

        foreach ($recipients as $recipient) {
            $placeholders[] = "(?, ?, ?, ?, 'pending', NOW())";
            $params[] = $recipient['email'];
            $params[] = $recipient['name'] ?? null;
            $params[] = $subject;
            $params[] = $body;
        }

        $sql .= implode(', ', $placeholders);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return count($recipients);
    }

    /**
     * Process pending emails
     */
    public function process($limit = 50)
    {
        // 1. Fetch pending emails (locking rows if possible, but for simple LAMP, standard update is easier)
        // Mark them as 'processing' first to avoid double sending in concurrent runs
        // For simplicity in this environment, we'll fetch then update.

        $stmt = $this->db->prepare("SELECT * FROM email_queue WHERE status = 'pending' LIMIT ?");
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $jobs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($jobs)) return 0;

        $processed = 0;
        foreach ($jobs as $job) {
            // Update status to processing
            $this->updateStatus($job['id'], 'processing');

            try {
                // Send Email
                $result = $this->emailService->sendEmail(
                    $job['recipient_email'],
                    $job['subject'],
                    $job['body']
                );

                if ($result['success']) {
                    $this->updateStatus($job['id'], 'sent');
                    $processed++;
                } else {
                    $this->updateStatus($job['id'], 'failed', $result['message']);
                }
            } catch (Exception $e) {
                $this->updateStatus($job['id'], 'failed', $e->getMessage());
            }
        }

        return $processed;
    }

    private function updateStatus($id, $status, $error = null)
    {
        $sql = "UPDATE email_queue SET status = ?, updated_at = NOW()";
        $params = [$status];

        if ($error) {
            $sql .= ", error_message = ?";
            $params[] = $error;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }
}
