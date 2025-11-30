<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
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
     * Send LPK verification email to institution owner
     *
     * @param object $user User entity
     * @param object $institution VocationalTrainingInstitution entity
     * @param string $token Verification token
     * @param string $tempPassword Temporary password
     * @return bool Success status
     */
    public function sendLpkVerificationEmail($user, $institution, $token, $tempPassword)
    {
        try {
            $verificationLink = Router::url([
                'controller' => 'Lpk',
                'action' => 'verifyEmail',
                $token
            ], true);
            
            $email = new Email('default');
            $email->setFrom(['sriechoentjoro@gmail.com' => 'TMM System Admin'])
                ->setTo($user->email)
                ->setSubject('Verify Your LPK Account - TMM System')
                ->setEmailFormat('both')
                ->setViewVars([
                    'institutionName' => $institution->name,
                    'registrationNumber' => isset($institution->registration_number) ? $institution->registration_number : 'N/A',
                    'verificationLink' => $verificationLink,
                    'email' => $user->email,
                    'tempPassword' => $tempPassword,
                    'expiresIn' => '24 hours'
                ])
                ->setTemplate('lpk_verification')
                ->setLayout('email_branded');
            
            $result = $email->send();
            
            // Log email
            $this->logEmail([
                'recipient_email' => $user->email,
                'recipient_name' => $institution->name,
                'subject' => 'Verify Your LPK Account - TMM System',
                'email_type' => 'lpk_verification',
                'stakeholder_type' => 'lpk',
                'stakeholder_id' => $institution->id,
                'user_id' => $user->id,
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s')
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send LPK verification email: ' . $e->getMessage());
            
            // Log failed attempt
            $this->logEmail([
                'recipient_email' => $user->email,
                'recipient_name' => $institution->name,
                'subject' => 'Verify Your LPK Account - TMM System',
                'email_type' => 'lpk_verification',
                'stakeholder_type' => 'lpk',
                'stakeholder_id' => $institution->id,
                'user_id' => $user->id,
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Send admin notification about new LPK registration
     *
     * @param object $institution VocationalTrainingInstitution entity
     * @param object $user User entity
     * @return bool Success status
     */
    public function sendAdminLpkNotification($institution, $user)
    {
        try {
            $editLink = Router::url([
                'prefix' => 'admin',
                'controller' => 'Lpk',
                'action' => 'edit',
                $institution->id
            ], true);
            
            // Get all admin emails
            $UsersTable = TableRegistry::getTableLocator()->get('Users');
            $admins = $UsersTable->find()
                ->where(['role' => 'admin', 'status' => 'active'])
                ->all();
            
            foreach ($admins as $admin) {
                $email = new Email('default');
                $email->setFrom(['sriechoentjoro@gmail.com' => 'TMM System'])
                    ->setTo($admin->email)
                    ->setSubject('New LPK Registration - Pending Verification')
                    ->setEmailFormat('both')
                    ->setViewVars([
                        'adminName' => $admin->fullname,
                        'institutionName' => $institution->name,
                        'email' => $user->email,
                        'registrationNumber' => isset($institution->registration_number) ? $institution->registration_number : 'N/A',
                        'editLink' => $editLink,
                        'status' => 'Pending Verification'
                    ])
                    ->setTemplate('admin_lpk_notification')
                    ->setLayout('email_branded')
                    ->send();
                
                // Log email
                $this->logEmail([
                    'recipient_email' => $admin->email,
                    'recipient_name' => $admin->fullname,
                    'subject' => 'New LPK Registration - Pending Verification',
                    'email_type' => 'admin_notification',
                    'stakeholder_type' => 'lpk',
                    'stakeholder_id' => $institution->id,
                    'user_id' => $admin->id,
                    'status' => 'sent',
                    'sent_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send admin LPK notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Special Skill institution verification email
     *
     * @param object $user User entity
     * @param object $institution SpecialSkillSupportInstitution entity
     * @param string $token Verification token
     * @param string $tempPassword Temporary password
     * @return bool Success status
     */
    public function sendSpecialSkillVerificationEmail($user, $institution, $token, $tempPassword)
    {
        try {
            $verificationLink = Router::url([
                'controller' => 'SpecialSkill',
                'action' => 'verifyEmail',
                $token
            ], true);
            
            $email = new Email('default');
            $email->setFrom(['sriechoentjoro@gmail.com' => 'TMM System Admin'])
                ->setTo($user->email)
                ->setSubject('Verify Your Special Skill Institution Account - TMM System')
                ->setEmailFormat('both')
                ->setViewVars([
                    'institutionName' => $institution->name,
                    'verificationLink' => $verificationLink,
                    'email' => $user->email,
                    'tempPassword' => $tempPassword,
                    'expiresIn' => '24 hours'
                ])
                ->setTemplate('special_skill_verification')
                ->setLayout('email_branded');
            
            $result = $email->send();
            
            // Log email
            $this->logEmail([
                'recipient_email' => $user->email,
                'recipient_name' => $institution->name,
                'subject' => 'Verify Your Special Skill Institution Account - TMM System',
                'email_type' => 'special_skill_verification',
                'stakeholder_type' => 'special_skill',
                'stakeholder_id' => $institution->id,
                'user_id' => $user->id,
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s')
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send Special Skill verification email: ' . $e->getMessage());
            
            // Log failed attempt
            $this->logEmail([
                'recipient_email' => $user->email,
                'recipient_name' => $institution->name,
                'subject' => 'Verify Your Special Skill Institution Account - TMM System',
                'email_type' => 'special_skill_verification',
                'stakeholder_type' => 'special_skill',
                'stakeholder_id' => $institution->id,
                'user_id' => $user->id,
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Send verification confirmation email after successful verification
     *
     * @param object $user User entity
     * @param object $institution Institution entity
     * @param string $institutionType 'lpk' or 'special_skill'
     * @return bool Success status
     */
    public function sendVerificationConfirmationEmail($user, $institution, $institutionType)
    {
        try {
            $profileLink = Router::url([
                'controller' => $institutionType === 'lpk' ? 'Lpk' : 'SpecialSkill',
                'action' => 'profile'
            ], true);
            
            $email = new Email('default');
            $email->setFrom(['sriechoentjoro@gmail.com' => 'TMM System Admin'])
                ->setTo($user->email)
                ->setSubject('Email Verified Successfully - TMM System')
                ->setEmailFormat('both')
                ->setViewVars([
                    'institutionName' => $institution->name,
                    'email' => $user->email,
                    'profileLink' => $profileLink,
                    'institutionType' => $institutionType
                ])
                ->setTemplate('verification_confirmation')
                ->setLayout('email_branded')
                ->send();
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to send verification confirmation email: ' . $e->getMessage());
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
