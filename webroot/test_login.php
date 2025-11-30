<?php
require __DIR__ . '/../vendor/autoload.php';

use Cake\Auth\DefaultPasswordHasher;

// Connect to database
$pdo = new PDO('mysql:host=localhost;dbname=system_authentication_authorization', 'root', '62xe6zyr');

// Get admin user
$stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ? LIMIT 1');
$stmt->execute(['admin']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "=== Admin User Found ===\n";
    echo "ID: " . $user['id'] . "\n";
    echo "Username: " . $user['username'] . "\n";
    echo "Password Hash (first 60 chars): " . substr($user['password'], 0, 60) . "\n\n";
    
    // Test password verification
    $hasher = new DefaultPasswordHasher();
    $testPassword = 'password123';
    
    echo "=== Password Verification Test ===\n";
    echo "Testing password: '$testPassword'\n";
    
    $result = $hasher->check($testPassword, $user['password']);
    
    if ($result) {
        echo "✓ SUCCESS - Password 'password123' matches!\n";
        echo "You should be able to login with:\n";
        echo "  Username: admin\n";
        echo "  Password: password123\n";
    } else {
        echo "✗ FAILED - Password does not match\n";
        echo "Generating new hash...\n";
        
        $newHash = $hasher->hash($testPassword);
        echo "New hash: $newHash\n\n";
        
        // Update the database
        $updateStmt = $pdo->prepare('UPDATE users SET password = ? WHERE username = ?');
        $updateStmt->execute([$newHash, 'admin']);
        
        echo "✓ Password updated in database. Try logging in now.\n";
    }
} else {
    echo "ERROR: Admin user not found in database!\n";
}
