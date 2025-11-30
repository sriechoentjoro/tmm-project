<?php
/**
 * Email Test Script
 * 
 * Tests the email sending functionality for institution registration
 * 
 * Usage: 
 * 1. Make sure database schema is applied
 * 2. Configure email in config/app.php
 * 3. Run: php bin/cake.php test_email
 */

namespace App\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

class TestEmailShell extends Shell
{
    /**
     * Main execution method
     */
    public function main()
    {
        $this->out('==============================================');
        $this->out('Institution Registration Email Test');
        $this->out('==============================================');
        $this->out('');

        // Test 1: Check email template exists
        $this->out('[1/4] Checking email template...');
        $EmailTemplates = TableRegistry::getTableLocator()->get('EmailTemplates');
        $template = $EmailTemplates->getTemplate('institution_registration');
        
        if ($template) {
            $this->success('✓ Email template found: ' . $template->subject);
        } else {
            $this->err('✗ Email template not found. Please run the schema SQL first.');
            return;
        }

        // Test 2: Test template rendering
        $this->out('[2/4] Testing template rendering...');
        $testData = [
            'institution_name' => 'Test Institution',
            'username' => 'test_user',
            'email' => 'sriechoentjoro@gmail.com',
            'registration_url' => 'http://localhost/tmm/institution-registration/complete/TEST_TOKEN_123',
            'expiry_date' => (new \DateTime('+48 hours'))->format('Y-m-d H:i:s'),
        ];
        
        $renderedSubject = $template->renderSubject($testData);
        $renderedBody = $template->render($testData, true);
        
        $this->out('Subject: ' . $renderedSubject);
        $this->success('✓ Template rendered successfully');

        // Test 3: Send test email
        $this->out('[3/4] Sending test email to sriechoentjoro@gmail.com...');
        
        try {
            $email = new \Cake\Mailer\Email('default');
            $email->setFrom(['noreply@tmm-system.com' => 'TMM System'])
                ->setTo('sriechoentjoro@gmail.com')
                ->setSubject('[TEST] ' . $renderedSubject)
                ->setEmailFormat('both')
                ->setHtml($renderedBody)
                ->setText($template->render($testData, false));
            
            $result = $email->send();
            $this->success('✓ Test email sent successfully!');
            $this->out('');
            $this->out('Please check sriechoentjoro@gmail.com inbox');
            
        } catch (\Exception $e) {
            $this->err('✗ Failed to send email: ' . $e->getMessage());
            $this->out('');
            $this->out('Common issues:');
            $this->out('1. Gmail App Password not configured');
            $this->out('2. SMTP settings incorrect in config/app.php');
            $this->out('3. Firewall blocking SMTP port 587');
            return;
        }

        // Test 4: Log email
        $this->out('[4/4] Logging email...');
        try {
            $EmailLogs = TableRegistry::getTableLocator()->get('EmailLogs');
            $log = $EmailLogs->newEntity([
                'template_key' => 'institution_registration',
                'recipient_email' => 'sriechoentjoro@gmail.com',
                'subject' => $renderedSubject,
                'body' => $renderedBody,
                'status' => 'sent',
                'sent_at' => new \DateTime(),
            ]);
            
            if ($EmailLogs->save($log)) {
                $this->success('✓ Email logged successfully');
            }
        } catch (\Exception $e) {
            $this->warn('⚠ Could not log email: ' . $e->getMessage());
        }

        $this->out('');
        $this->out('==============================================');
        $this->success('All tests completed!');
        $this->out('==============================================');
    }
}
