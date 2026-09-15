<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Mailer\Email;
use App\Model\Table\EmailTemplatesTable;
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

            // The bodies reach the message through view variables and the
            // db_template views, which echo them unchanged.
            //
            // This used to end with $email->setHtml($bodyHtml) and
            // $email->setText($bodyText). Cake\Mailer\Email has neither method,
            // and no __call to absorb them, so every call raised
            // "Call to undefined method" - an Error, which the catch below does
            // not catch because Error is not an Exception. So no email built
            // from the email_templates table has ever been sent, and the fatal
            // took the request with it rather than being logged as a failure.
            //
            // Nothing else in the class was wrong: the template was found, the
            // subject and bodies were rendered, and then it died on the line
            // that was supposed to hand them over.
            $email->setViewVars([
                'content' => $bodyHtml,
                'textContent' => $bodyText !== '' ? $bodyText : strip_tags($bodyHtml),
            ]);
            $email->viewBuilder()->setTemplate('db_template');

            // An author who pastes a whole <html> document gets it sent as it
            // is; anything shorter is a message, and the branded letterhead
            // goes around it. EmailTemplatesTable::wrapsInLayout() asks the
            // question, and the editor's preview asks it the same way, so what
            // the editor shows is what goes out.
            if (EmailTemplatesTable::wrapsInLayout($bodyHtml)) {
                $email->viewBuilder()->setLayout('email_branded');
            } else {
                $email->viewBuilder()->disableAutoLayout();
            }

            $email->send();

            // Log email
            $this->logEmail($templateKey, $to, $subject, $bodyHtml, 'sent');

            return true;

        } catch (\Throwable $e) {
            // \Throwable, not \Exception. The undefined-method call above raised
            // an Error, which is not an Exception, so it sailed straight past
            // this catch and killed the request - and nothing was logged,
            // because logging happens here. A missing template or a bad view
            // variable raises an Error the same way.
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
