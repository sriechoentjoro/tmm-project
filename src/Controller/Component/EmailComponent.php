<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

/**
 * Email Component
 * 
 * Handles sending templated emails with variable replacement
 */
class EmailComponent extends Component
{
    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'transport' => 'default',
        'from' => ['noreply@tmm-system.com' => 'TMM System'],
        'testMode' => false,
        'testEmail' => null,
    ];

    /**
     * Send email using template
     *
     * @param string $templateKey Template key to use
     * @param string|array $to Recipient email address(es)
     * @param array $data Data for variable replacement
     * @param bool $testMode If true, send to test email instead
     * @return bool Success status
     */
    public function sendTemplate($templateKey, $to, $data = [], $testMode = false)
    {
        try {
            // Load email template
            $EmailTemplates = TableRegistry::getTableLocator()->get('EmailTemplates');
            $template = $EmailTemplates->getTemplate($templateKey);

            if (!$template) {
                Log::error("Email template not found: {$templateKey}");
                return false;
            }

            // Render subject and body
            $subject = $template->renderSubject($data);
            $bodyHtml = $template->render($data, true);
            $bodyText = $template->render($data, false);

            // Override recipient if in test mode
            if ($testMode && $this->getConfig('testEmail')) {
                $originalTo = is_array($to) ? implode(', ', $to) : $to;
                $to = $this->getConfig('testEmail');
                $subject = "[TEST] {$subject} (Original: {$originalTo})";
            }

            // Send email
            $email = new Email($this->getConfig('transport'));
            $email->setFrom($this->getConfig('from'))
                ->setTo($to)
                ->setSubject($subject)
                ->setEmailFormat('both');

            if (!empty($bodyHtml)) {
                $email->setViewVars(['content' => $bodyHtml]);
            }

            if (!empty($bodyText)) {
                $email->setViewVars(['textContent' => $bodyText]);
            }

            // Set HTML and text content directly
            $email->setHtml($bodyHtml);
            if (!empty($bodyText)) {
                $email->setText($bodyText);
            }

            $result = $email->send();

            // Log email
            $this->logEmail($templateKey, $to, $subject, $bodyHtml, 'sent');

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
            $this->logEmail($templateKey, $to, $subject ?? '', '', 'failed', $e->getMessage());
            return false;
        }
    }

    /**
     * Log email sending attempt
     *
     * @param string $templateKey Template key
     * @param string|array $to Recipient(s)
     * @param string $subject Email subject
     * @param string $body Email body
     * @param string $status Status (sent/failed)
     * @param string|null $errorMessage Error message if failed
     * @return void
     */
    protected function logEmail($templateKey, $to, $subject, $body, $status = 'sent', $errorMessage = null)
    {
        try {
            $EmailLogs = TableRegistry::getTableLocator()->get('EmailLogs');
            
            $log = $EmailLogs->newEntity([
                'template_key' => $templateKey,
                'recipient_email' => is_array($to) ? implode(', ', $to) : $to,
                'subject' => $subject,
                'body' => $body,
                'status' => $status,
                'error_message' => $errorMessage,
                'sent_at' => $status === 'sent' ? new \DateTime() : null,
            ]);

            $EmailLogs->save($log);
        } catch (\Exception $e) {
            Log::error("Failed to log email: " . $e->getMessage());
        }
    }

    /**
     * Send registration email to institution
     *
     * @param object $institution Institution entity
     * @param string $registrationUrl Full registration URL with token
     * @return bool Success status
     */
    public function sendRegistrationEmail($institution, $registrationUrl)
    {
        $data = [
            'institution_name' => $institution->name ?? $institution->company_name,
            'username' => $institution->username,
            'email' => $institution->email,
            'registration_url' => $registrationUrl,
            'expiry_date' => $institution->token_expires_at ? $institution->token_expires_at->format('Y-m-d H:i:s') : 'N/A',
        ];

        return $this->sendTemplate('institution_registration', $institution->email, $data);
    }

    /**
     * Send test registration email
     *
     * @param string $testEmail Test email address
     * @param string $institutionName Sample institution name
     * @return bool Success status
     */
    public function sendTestRegistrationEmail($testEmail, $institutionName = 'Sample Institution')
    {
        $data = [
            'institution_name' => $institutionName,
            'username' => 'sample_user',
            'email' => $testEmail,
            'registration_url' => 'http://localhost/tmm/institution-registration/complete/SAMPLE_TOKEN_123456',
            'expiry_date' => (new \DateTime('+48 hours'))->format('Y-m-d H:i:s'),
        ];

        return $this->sendTemplate('institution_registration', $testEmail, $data);
    }
}
