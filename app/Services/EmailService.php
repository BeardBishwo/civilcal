<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\EmailThread;
use Exception;

class EmailService
{
    private $emailTemplateModel;
    private $emailThreadModel;
    private $emailManager;

    public function __construct()
    {
        $this->emailTemplateModel = new EmailTemplate();
        $this->emailThreadModel = new EmailThread();
        $this->emailManager = new \App\Services\EmailManager();
    }

    /**
     * Send an email using a template
     */
    public function sendEmailUsingTemplate($templateId, $recipient, $variables = [])
    {
        try {
            // Get the template
            $template = $this->emailTemplateModel->find($templateId);
            
            if (!$template) {
                throw new Exception("Email template not found: {$templateId}");
            }

            // Process the template with variables
            $processedContent = $this->emailTemplateModel->processTemplate($templateId, $variables);
            
            if ($processedContent === false) {
                throw new Exception("Failed to process template: {$templateId}");
            }

            // Send the email
            $result = $this->sendEmail(
                $recipient,
                $processedContent['subject'],
                $processedContent['content']
            );

            return [
                'success' => true,
                'message' => 'Email sent successfully',
                'result' => $result
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email sending failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send a direct email
     */
    public function sendEmail($to, $subject, $body, $from = null, $replyTo = null)
    {
        try {
            // Validate required parameters
            if (empty($to) || empty($subject) || empty($body)) {
                throw new Exception("Missing required email parameters");
            }

            // Validate email address
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid recipient email: {$to}");
            }

            // Delegate to EmailManager to use SMTP
            $mailSent = $this->emailManager->sendEmail($to, $subject, $body, $from, $replyTo);

            if ($mailSent) {
                return [
                    'success' => true,
                    'message' => 'Email sent successfully via SMTP'
                ];
            } else {
                throw new Exception("EmailManager failed to send email");
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email sending failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create a new email thread
     */
    public function createEmailThread($data)
    {
        try {
            // Validate required data
            $requiredFields = ['from_email', 'from_name', 'subject', 'message'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Missing required field: {$field}");
                }
            }

            // Validate email address
            if (!filter_var($data['from_email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email address: {$data['from_email']}");
            }

            // Create the thread using the model
            $result = $this->emailThreadModel->create($data);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Email thread created successfully'
                ];
            } else {
                throw new Exception("Failed to create email thread");
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email thread creation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Add response to an email thread
     */
    public function addResponseToThread($threadId, $userId, $message, $isInternalNote = false)
    {
        try {
            $result = $this->emailThreadModel->addResponseToThread($threadId, $userId, $message, $isInternalNote);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Response added to thread successfully'
                ];
            } else {
                throw new Exception("Failed to add response to thread: {$threadId}");
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Adding response to thread failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all email threads with optional filters
     */
    public function getEmailThreads($filters = [], $page = 1, $perPage = 20)
    {
        try {
            return $this->emailThreadModel->getThreadsWithFilters($filters, $page, $perPage);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve email threads: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get email thread by ID
     */
    public function getEmailThreadById($id)
    {
        try {
            return $this->emailThreadModel->getThreadById($id);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve email thread: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Update email thread status
     */
    public function updateEmailThread($id, $data)
    {
        try {
            $result = $this->emailThreadModel->update($id, $data);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Email thread updated successfully'
                ];
            } else {
                throw new Exception("Failed to update email thread: {$id}");
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email thread update failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get email template by ID
     */
    public function getEmailTemplate($id)
    {
        try {
            return $this->emailTemplateModel->find($id);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve email template: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get email template by name
     */
    public function getEmailTemplateByName($name)
    {
        try {
            return $this->emailTemplateModel->findByName($name);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve email template: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Create a new email template
     */
    public function createEmailTemplate($data)
    {
        try {
            // Validate required fields
            $validation = $this->emailTemplateModel->validate($data);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $validation['errors']),
                    'errors' => $validation['errors']
                ];
            }

            // Create the template
            $result = $this->emailTemplateModel->create($validation['data']);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Email template created successfully',
                    'template_id' => $result
                ];
            } else {
                throw new Exception("Failed to create email template");
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email template creation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update an existing email template
     */
    public function updateEmailTemplate($id, $data)
    {
        try {
            // Validate data
            $validation = $this->emailTemplateModel->validate($data);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $validation['errors']),
                    'errors' => $validation['errors']
                ];
            }

            // Update the template
            $result = $this->emailTemplateModel->update($id, $validation['data']);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Email template updated successfully'
                ];
            } else {
                throw new Exception("Failed to update email template: {$id}");
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email template update failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all email templates with optional filters
     */
    public function getAllEmailTemplates($filters = [], $page = 1, $perPage = 20)
    {
        try {
            return $this->emailTemplateModel->getAll($filters, $page, $perPage);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve email templates: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Send bulk emails
     */
    public function sendBulkEmails($recipients, $subject, $body, $templateId = null, $variables = [])
    {
        try {
            $results = [
                'sent' => 0,
                'failed' => 0,
                'results' => []
            ];

            foreach ($recipients as $index => $recipient) {
                $emailSubject = $subject;
                $emailBody = $body;
                
                // If template is provided, process it with variables for this recipient
                if ($templateId) {
                    $recipientVariables = isset($variables[$index]) ? $variables[$index] : [];
                    $processed = $this->emailTemplateModel->processTemplate($templateId, $recipientVariables);
                    
                    if ($processed !== false) {
                        $emailSubject = $processed['subject'];
                        $emailBody = $processed['content'];
                    }
                }

                $result = $this->sendEmail($recipient, $emailSubject, $emailBody);
                $results['results'][] = [
                    'recipient' => $recipient,
                    'result' => $result
                ];

                if ($result['success']) {
                    $results['sent']++;
                } else {
                    $results['failed']++;
                }
            }

            $results['success'] = true;
            $results['message'] = "Bulk email operation completed. Sent: {$results['sent']}, Failed: {$results['failed']}";

            return $results;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Bulk email operation failed: ' . $e->getMessage(),
                'sent' => 0,
                'failed' => 0,
                'results' => []
            ];
        }
    }

    /**
     * Get email statistics
     */
    public function getEmailStats()
    {
        try {
            $templateStats = $this->emailTemplateModel->getStats();
            $threadCount = $this->emailThreadModel->getTotalThreadCount();
            
            return [
                'success' => true,
                'stats' => [
                    'total_templates' => $templateStats['total_templates'] ?? 0,
                    'active_templates' => $templateStats['active_templates'] ?? 0,
                    'inactive_templates' => $templateStats['inactive_templates'] ?? 0,
                    'total_threads' => $threadCount,
                    'templates_by_type' => $this->emailTemplateModel->getTemplateTypes()
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve email stats: ' . $e->getMessage(),
                'stats' => []
            ];
        }
    }

    /**
     * Validate email address
     */
    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}