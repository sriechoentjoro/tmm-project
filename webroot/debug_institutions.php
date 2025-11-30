<?php
// webroot/debug_institutions.php
// Access via: http://localhost/tmm/debug_institutions.php

require dirname(__DIR__) . '/vendor/autoload.php';

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use App\Application;

// Manual Bootstrap
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src');
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', ROOT . DS . WEBROOT_DIR . DS);
define('CONFIG', ROOT . DS . 'config' . DS);
define('LOGS', ROOT . DS . 'logs' . DS);
define('TMP', ROOT . DS . 'tmp' . DS);
define('CACHE', TMP . 'cache' . DS);

$app = new Application(ROOT . '/config');
$app->bootstrap();

echo "<h1>Institution Debugger</h1>";

$institutions = TableRegistry::getTableLocator()->get('VocationalTrainingInstitutions');
$query = $institutions->find();
$count = $query->count();

echo "<p>Total Institutions Found: <strong>$count</strong></p>";

if ($count === 0) {
    echo "<h2 style='color:red'>No institutions found!</h2>";
    echo "<p>Please run the SQL below in phpMyAdmin to create one:</p>";
    echo "<textarea rows='10' cols='80'>
INSERT INTO vocational_training_institutions (
    name, email, username, registration_token, token_expires_at, is_registered, created, modified
) VALUES (
    'Test Institution', 'test@example.com', 'testwizard', 'WIZARD_TEST_TOKEN_123', 
    DATE_ADD(NOW(), INTERVAL 48 HOUR), 0, NOW(), NOW()
);
    </textarea>";
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Token</th><th>Expires</th><th>Registered?</th><th>Action</th></tr>";
    
    foreach ($query as $inst) {
        echo "<tr>";
        echo "<td>" . $inst->id . "</td>";
        echo "<td>" . $inst->name . "</td>";
        echo "<td>" . $inst->username . "</td>";
        echo "<td>" . $inst->email . "</td>";
        echo "<td>" . ($inst->registration_token ?: 'NULL') . "</td>";
        echo "<td>" . ($inst->token_expires_at ? $inst->token_expires_at->format('Y-m-d H:i') : 'NULL') . "</td>";
        echo "<td>" . ($inst->is_registered ? 'YES' : 'NO') . "</td>";
        
        // Fix link
        $token = 'WIZARD_TEST_TOKEN_123';
        echo "<td>";
        echo "<form method='POST'>";
        echo "<input type='hidden' name='id' value='" . $inst->id . "'>";
        echo "<button type='submit' name='reset'>RESET TOKEN</button>";
        echo "</form>";
        
        if ($inst->registration_token) {
             echo "<br><a href='/tmm/institution-registration/complete/" . $inst->registration_token . "' target='_blank'>Open Registration</a>";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Handle Reset
if (isset($_POST['reset']) && isset($_POST['id'])) {
    $inst = $institutions->get($_POST['id']);
    $inst->registration_token = 'WIZARD_TEST_TOKEN_123';
    $inst->token_expires_at = new \DateTime('+48 hours');
    $inst->is_registered = false;
    $institutions->save($inst);
    echo "<script>window.location.reload();</script>";
}
