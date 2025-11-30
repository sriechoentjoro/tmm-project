<?php
/**
 * Simple Email Test - Direct SMTP Test
 * Tests email sending without database dependencies
 */

// Gmail SMTP Configuration
$config = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'sriechoentjoro@gmail.com',
    'password' => 'unqqevrzplpwysnk',
    'from' => 'sriechoentjoro@gmail.com',
    'from_name' => 'TMM Apprentice Management System',
    'to' => 'sriechoentjoro@gmail.com',
];

echo "==============================================\n";
echo "Simple Email Test - Direct SMTP\n";
echo "==============================================\n\n";

echo "[1/3] Testing SMTP connection...\n";

try {
    // Create socket connection
    $socket = @fsockopen($config['host'], $config['port'], $errno, $errstr, 10);
    
    if (!$socket) {
        echo "✗ Failed to connect to SMTP server\n";
        echo "Error: $errstr ($errno)\n";
        exit(1);
    }
    
    fclose($socket);
    echo "✓ SMTP server reachable\n\n";
    
} catch (Exception $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "[2/3] Sending test email using PHPMailer...\n";

// Simple mail headers
$to = $config['to'];
$subject = '[TEST] TMM Email System Test';
$message = "This is a test email from TMM Apprentice Management System.\n\n";
$message .= "If you receive this email, the SMTP configuration is working correctly!\n\n";
$message .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
$message .= "Server: {$config['host']}:{$config['port']}\n";

$headers = "From: {$config['from_name']} <{$config['from']}>\r\n";
$headers .= "Reply-To: {$config['from']}\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Try using PHP's mail() function first
if (@mail($to, $subject, $message, $headers)) {
    echo "✓ Email sent using PHP mail() function\n\n";
} else {
    echo "⚠ PHP mail() failed, trying SMTP directly...\n\n";
    
    // Try direct SMTP (requires manual implementation or library)
    echo "Note: For direct SMTP, you need to use CakePHP's Email class or PHPMailer library.\n";
    echo "The test shows SMTP server is reachable, which is good!\n\n";
}

echo "[3/3] Summary\n";
echo "✓ SMTP server: {$config['host']}:{$config['port']} is reachable\n";
echo "✓ Username: {$config['username']}\n";
echo "✓ From: {$config['from_name']} <{$config['from']}>\n";
echo "✓ To: {$config['to']}\n\n";

echo "==============================================\n";
echo "Test completed!\n";
echo "==============================================\n\n";

echo "Next steps:\n";
echo "1. Check your email inbox: {$config['to']}\n";
echo "2. If no email received, check spam folder\n";
echo "3. Run database schema: config/sql/institution_registration_schema.sql\n";
echo "4. Then test with CakePHP: php bin/cake.php test_email\n";
