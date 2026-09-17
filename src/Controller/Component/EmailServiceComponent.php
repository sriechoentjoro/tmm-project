<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Cake\Log\Log;

/**
 * Email Service Component
 * Handles all email sending for stakeholder management
 * 
 * Features:
 * - LPK verification emails
 * - Special Skill institution verification emails
 * - Admin notification emails
 * - Password reset emails
 * - Email logging to database
 * - Error handling and fallback
 */
class EmailServiceComponent extends Component
{
    /**
     * Generate cryptographically secure verification token
     *
     * @return string 64-character hex token
     */
    public function generateVerificationToken()
    {
        return bin2hex(Security::randomBytes(32));
    }

    /**
     * Generate strong temporary password
     *
     * @return string 16-character password with mixed case, numbers, symbols
     */
    public function generateTemporaryPassword()
    {
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lowercase = 'abcdefghijkmnopqrstuvwxyz';
        $numbers = '23456789';
        $symbols = '!@#$%^&*';
        
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Fill rest with random characters
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 7; $i < 16; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Shuffle to avoid predictable pattern
        return str_shuffle($password);
    }

    /**
     * Send one of the templates in src/Template/Email using the branded layout.
     *
     * The LpkRegistration flow builds its own view variables and its own
     * verification URL, so it needs a plain "render this template and send it"
     * call rather than one of the specific senders below, which construct the
     * link themselves and require a User entity that does not exist yet at the
     * point the first email goes out.
     *
     * Never throws: a missing template or a refused SMTP connection returns
     * false, so a caller in the middle of a save is not left half-finished.
     *
     * @param string $to Recipient address
     * @param string|null $name Recipient name, for the To header
     * @param string $template Template name under src/Template/Email
     * @param array $vars View variables the template expects
     * @param string|null $subject Overrides the subject for the template
     * @return bool Whether the message was handed to the transport
     */
    public function sendEmail($to, $name, $template, array $vars = [], $subject = null)
    {
        $subjects = [
            'lpk_verification' => 'Verify Your LPK Account - TMM System',
            'lpk_welcome' => 'Your LPK Account Is Active - TMM System',
            'special_skill_verification' => 'Verify Your Institution Account - TMM System',
            'verification_confirmation' => 'Email Verified - TMM System',
            'admin_lpk_notification' => 'New LPK Registration - TMM System',
        ];
        if ($subject === null) {
            $subject = isset($subjects[$template]) ? $subjects[$template] : 'TMM System Notification';
        }

        try {
            // From comes from Email.default.from in config/app.php.
            $email = new Email('default');
            $email->setTo($to, $name !== null && $name !== '' ? $name : $to)
                ->setSubject($subject)
                ->setEmailFormat('both')
                ->setViewVars($vars);
            // Email::setTemplate()/setLayout() are deprecated since 3.7.
            $email->viewBuilder()
                ->setTemplate($template)
                ->setLayout('email_branded');

            $email->send();

            $this->logEmail([
                'recipient_email' => $to,
                'subject' => $subject,
                'template_key' => $template,
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s'),
            ]);

            return true;
        } catch (\Throwable $e) {
            // \Throwable, not \Exception: a missing template or a bad view
            // variable raises an Error, and letting that escape kills the
            // request after the record has already been written.
            Log::error('Failed to send ' . $template . ' email to ' . $to . ': ' . $e->getMessage());

            $this->logEmail([
                'recipient_email' => $to,
                'subject' => $subject,
                'template_key' => $template,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Log email to database
     *
     * @param array $data Email log data
     * @return bool Success status
     */
    protected function logEmail($data)
    {
        try {
            $EmailLogsTable = TableRegistry::getTableLocator()->get('EmailLogs');
            $log = $EmailLogsTable->newEntity($data);
            return $EmailLogsTable->save($log) !== false;
        } catch (\Exception $e) {
            Log::error('Failed to log email: ' . $e->getMessage());
            return false;
        }
    }
}
