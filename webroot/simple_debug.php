<?php
// webroot/simple_debug.php
// Access via: http://localhost/tmm/simple_debug.php

// Database credentials (default XAMPP)
$host = 'localhost';
$db   = 'cms_tmm_stakeholders'; // Adjust if your DB name is different
$user = 'root';
$pass = '62xe6zyr';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// Handle Reset
if (isset($_POST['reset']) && isset($_POST['username'])) {
    $stmt = $pdo->prepare("UPDATE vocational_training_institutions SET registration_token = 'WIZARD_TEST_TOKEN_123', token_expires_at = DATE_ADD(NOW(), INTERVAL 48 HOUR), is_registered = 0 WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    echo "<div style='background:lightgreen;padding:10px'>Token Reset Successfully!</div>";
}

// Fetch Data
$stmt = $pdo->query("SELECT id, name, username, email, registration_token, is_registered FROM vocational_training_institutions");
$institutions = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head><title>Simple Debugger</title></head>
<body>
    <h1>Database Inspector (No Framework)</h1>
    
    <?php if (empty($institutions)): ?>
        <h2 style="color:red">No Institutions Found!</h2>
        <p>Run this SQL in phpMyAdmin:</p>
        <pre style="background:#eee;padding:10px">
INSERT INTO vocational_training_institutions (
    name, email, username, registration_token, token_expires_at, is_registered, created, modified
) VALUES (
    'Test Institution', 'test@example.com', 'testwizard', 'WIZARD_TEST_TOKEN_123', 
    DATE_ADD(NOW(), INTERVAL 48 HOUR), 0, NOW(), NOW()
);
        </pre>
    <?php else: ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Token</th>
                <th>Registered?</th>
                <th>Action</th>
            </tr>
            <?php foreach ($institutions as $inst): ?>
            <tr>
                <td><?= htmlspecialchars($inst['name']) ?></td>
                <td><?= htmlspecialchars($inst['username']) ?></td>
                <td><?= htmlspecialchars($inst['registration_token'] ?? 'NULL') ?></td>
                <td><?= $inst['is_registered'] ? 'YES' : 'NO' ?></td>
                <td>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($inst['username']) ?>">
                        <button type="submit" name="reset">RESET TOKEN</button>
                    </form>
                    
                    <?php if ($inst['registration_token']): ?>
                        <br>
                        <a href="/tmm/institution-registration/complete/<?= htmlspecialchars($inst['registration_token']) ?>" target="_blank">
                            Open Wizard
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
