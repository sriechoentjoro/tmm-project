<?php
/**
 * Test OPcache Status and InventoriesTable Loading
 * Access via: http://localhost/asahi_v3/test_opcache_cleared.php
 */

echo "<h1>OPcache & InventoriesTable Test</h1>";

// Test 1: OPcache Status
echo "<h2>1. OPcache Status</h2>";
if (function_exists('opcache_get_status')) {
    $status = opcache_get_status();
    echo "<p style='color: green;'>✓ OPcache is ENABLED</p>";
    echo "<p>Cached scripts: " . $status['opcache_statistics']['num_cached_scripts'] . "</p>";
    echo "<p>Cache hits: " . $status['opcache_statistics']['hits'] . "</p>";
    echo "<p>Cache misses: " . $status['opcache_statistics']['misses'] . "</p>";
} else {
    echo "<p style='color: orange;'>OPcache is NOT enabled (this is okay)</p>";
}

// Test 2: Load CakePHP and test InventoriesTable
echo "<h2>2. Loading InventoriesTable</h2>";

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/bootstrap.php';

use Cake\ORM\TableRegistry;

try {
    
    // Clear any cached instances
    TableRegistry::getTableLocator()->clear();
    
    // Get Inventories table
    $inventories = TableRegistry::getTableLocator()->get('Inventories');
    
    echo "<p style='color: green;'>✓ Table Class: <strong>" . get_class($inventories) . "</strong></p>";
    
    // Check if it's Auto-Table or proper class
    if (get_class($inventories) === 'Cake\ORM\Table') {
        echo "<p style='color: red; font-size: 20px;'>✗ ERROR: Still using Auto-Table! (Generic Cake\ORM\Table)</p>";
        echo "<p>This means CakePHP couldn't find App\Model\Table\InventoriesTable</p>";
    } elseif (get_class($inventories) === 'App\Model\Table\InventoriesTable') {
        echo "<p style='color: green; font-size: 20px;'>✓ SUCCESS: Using proper InventoriesTable class!</p>";
    }
    
    // Test 3: Check associations
    echo "<h2>3. Testing Associations</h2>";
    
    $associations = $inventories->associations();
    echo "<p>Total associations found: <strong>" . count($associations) . "</strong></p>";
    
    echo "<ul>";
    foreach ($associations as $assoc) {
        echo "<li style='color: green;'>✓ " . $assoc->getName() . " (" . $assoc->type() . ")</li>";
    }
    echo "</ul>";
    
    // Test 4: Specifically check Storages and Uoms
    echo "<h2>4. Critical Associations Check</h2>";
    
    try {
        $storagesAssoc = $inventories->getAssociation('Storages');
        echo "<p style='color: green; font-size: 18px;'>✓ Storages association EXISTS!</p>";
        echo "<p>Type: " . $storagesAssoc->type() . ", Foreign Key: " . $storagesAssoc->getForeignKey() . "</p>";
    } catch (\Exception $e) {
        echo "<p style='color: red; font-size: 18px;'>✗ Storages association MISSING!</p>";
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
    
    try {
        $uomsAssoc = $inventories->getAssociation('Uoms');
        echo "<p style='color: green; font-size: 18px;'>✓ Uoms association EXISTS!</p>";
        echo "<p>Type: " . $uomsAssoc->type() . ", Foreign Key: " . $uomsAssoc->getForeignKey() . "</p>";
    } catch (\Exception $e) {
        echo "<p style='color: red; font-size: 18px;'>✗ Uoms association MISSING!</p>";
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
    
    // Test 5: File verification
    echo "<h2>5. File Verification</h2>";
    $reflection = new ReflectionClass($inventories);
    $filePath = $reflection->getFileName();
    echo "<p>Loading from: <code>" . $filePath . "</code></p>";
    
    $fileContent = file_get_contents($filePath);
    if (strpos($fileContent, "belongsTo('Storages')") !== false) {
        echo "<p style='color: green;'>✓ File contains belongsTo('Storages')</p>";
    } else {
        echo "<p style='color: red;'>✗ File MISSING belongsTo('Storages')</p>";
    }
    
    if (strpos($fileContent, "belongsTo('Uoms')") !== false) {
        echo "<p style='color: green;'>✓ File contains belongsTo('Uoms')</p>";
    } else {
        echo "<p style='color: red;'>✗ File MISSING belongsTo('Uoms')</p>";
    }
    
    // Test 6: Try a simple query
    echo "<h2>6. Database Query Test</h2>";
    
    try {
        $query = $inventories->find()
            ->contain(['Storages', 'Uoms'])
            ->limit(1);
        
        $result = $query->first();
        
        if ($result) {
            echo "<p style='color: green; font-size: 18px;'>✓ Query with contain(['Storages', 'Uoms']) WORKS!</p>";
            echo "<p>Successfully loaded inventory record with associations</p>";
        } else {
            echo "<p style='color: orange;'>No records found in database (but query syntax is valid)</p>";
        }
    } catch (\Exception $e) {
        echo "<p style='color: red; font-size: 18px;'>✗ Query FAILED!</p>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<h2>Summary</h2>";
    echo "<p style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<strong>If you see all green checkmarks above, the issue is FIXED!</strong><br>";
    echo "You can now access: <a href='/asahi_v3/inventories'>http://localhost/asahi_v3/inventories</a>";
    echo "</p>";
    
} catch (\Exception $e) {
    echo "<p style='color: red; font-size: 20px;'>✗ FATAL ERROR</p>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
