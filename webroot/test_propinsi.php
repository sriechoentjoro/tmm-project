<?php
// Test query MasterPropinsis
define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src');
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', ROOT . DS . WEBROOT_DIR . DS);

require ROOT . DS . 'vendor' . DS . 'autoload.php';
require ROOT . DS . 'config' . DS . 'bootstrap.php';

use Cake\ORM\TableRegistry;

echo "<h2>Testing MasterPropinsis Data</h2>";

try {
    $table = TableRegistry::getTableLocator()->get('MasterPropinsis');
    $count = $table->find()->count();
    
    echo "<p>Total MasterPropinsis records: <strong>" . $count . "</strong></p>";
    
    if ($count > 0) {
        $list = $table->find('list', ['limit' => 10])->toArray();
        echo "<h3>First 10 records (as list):</h3>";
        echo "<pre>";
        print_r($list);
        echo "</pre>";
        
        $all = $table->find('all', ['limit' => 5])->toArray();
        echo "<h3>First 5 records (full data):</h3>";
        echo "<pre>";
        foreach ($all as $row) {
            echo "ID: " . $row->id . " - Title: " . $row->title . "\n";
        }
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

