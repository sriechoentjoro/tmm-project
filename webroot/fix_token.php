<?php
// Save this as fix_token.php in your webroot
// Run it by visiting http://localhost/tmm/fix_token.php

use Cake\ORM\TableRegistry;

// Adjust path to point to index.php in the same directory (webroot)
require 'index.php';

$institutions = TableRegistry::getTableLocator()->get('VocationalTrainingInstitutions');
$institution = $institutions->find()->where(['username' => 'testwizard'])->first();

if ($institution) {
    $institution->registration_token = 'WIZARD_TEST_TOKEN_123';
    $institution->token_expires_at = new \DateTime('+48 hours');
    $institution->is_registered = false;
    
    if ($institutions->save($institution)) {
        echo "<h1>SUCCESS! Token Reset.</h1>";
        echo "<p>Token set to: " . $institution->registration_token . "</p>";
        echo "<p><a href='http://localhost/tmm/institution-registration/complete/WIZARD_TEST_TOKEN_123'>Click here to complete registration</a></p>";
    } else {
        echo "<h1>Failed to save.</h1>";
        debug($institution->getErrors());
    }
} else {
    echo "<h1>Institution 'testwizard' not found.</h1>";
    echo "<p>Please run the setup SQL first.</p>";
}
