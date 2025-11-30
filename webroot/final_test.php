<?php
/**
 * FINAL COMPREHENSIVE TEST
 * Run via browser: http://localhost/asahi_v3/final_test.php
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Final Diagnostic Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Final Diagnostic Test - InventoriesTable Loading</h1>
    
    <?php
    // Load CakePHP
    require dirname(__DIR__) . '/vendor/autoload.php';
    require dirname(__DIR__) . '/config/bootstrap.php';
    
    use Cake\ORM\TableRegistry;
    use Cake\Datasource\ConnectionManager;
    
    echo "<h2>Step 1: File System Check</h2>";
    $filePath = dirname(__DIR__) . '/src/Model/Table/InventoriesTable.php';
    if (file_exists($filePath)) {
        echo "<p class='success'>✓ InventoriesTable.php EXISTS</p>";
        echo "<p>Location: <code>$filePath</code></p>";
        
        // Check file content
        $content = file_get_contents($filePath);
        if (strpos($content, 'class InventoriesTable extends Table') !== false) {
            echo "<p class='success'>✓ Class definition found</p>";
        }
        if (strpos($content, "belongsTo('Storages')") !== false) {
            echo "<p class='success'>✓ Storages association in file</p>";
        }
        if (strpos($content, "belongsTo('Uoms')") !== false) {
            echo "<p class='success'>✓ Uoms association in file</p>";
        }
    } else {
        echo "<p class='error'>✗ InventoriesTable.php NOT FOUND!</p>";
    }
    
    echo "<h2>Step 2: Class Loading Test</h2>";
    if (class_exists('App\\Model\\Table\\InventoriesTable')) {
        echo "<p class='success'>✓ Class can be found by PHP autoloader</p>";
        
        try {
            $reflection = new ReflectionClass('App\\Model\\Table\\InventoriesTable');
            echo "<p>Class file: <code>" . $reflection->getFileName() . "</code></p>";
            
            $methods = $reflection->getMethods();
            echo "<p>Class has " . count($methods) . " methods</p>";
            
            // Check for initialize method
            if ($reflection->hasMethod('initialize')) {
                echo "<p class='success'>✓ initialize() method exists</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Reflection error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='error'>✗ Class App\\Model\\Table\\InventoriesTable NOT found by autoloader!</p>";
        echo "<p class='info'>Running composer dump-autoload might help</p>";
    }
    
    echo "<h2>Step 3: TableRegistry Test (THE CRITICAL TEST)</h2>";
    
    try {
        // Clear any cached instances
        TableRegistry::getTableLocator()->clear();
        
        echo "<p class='info'>Attempting to load 'Inventories' via TableRegistry...</p>";
        
        // Method 1: Standard way (what CakePHP controller uses)
        $inventories = TableRegistry::getTableLocator()->get('Inventories');
        
        $className = get_class($inventories);
        echo "<p>Loaded class: <strong>$className</strong></p>";
        
        if ($className === 'Cake\\ORM\\Table') {
            echo "<p class='error'>✗ PROBLEM: Using generic Auto-Table!</p>";
            echo "<p>CakePHP created a temporary Table because it couldn't find InventoriesTable class</p>";
            
            echo "<h3>Debugging: Why can't CakePHP find it?</h3>";
            
            // Check table locator configuration
            $config = TableRegistry::getTableLocator()->exists('Inventories') ? 
                      TableRegistry::getTableLocator()->get('Inventories')->getRegistryAlias() : 'Not registered';
            echo "<p>Registry alias: $config</p>";
            
            // Try explicit className
            echo "<p class='info'>Trying with explicit className...</p>";
            TableRegistry::getTableLocator()->clear();
            
            $inventories2 = TableRegistry::getTableLocator()->get('Inventories', [
                'className' => 'App\\Model\\Table\\InventoriesTable'
            ]);
            
            $className2 = get_class($inventories2);
            echo "<p>With explicit className: <strong>$className2</strong></p>";
            
            if ($className2 === 'App\\Model\\Table\\InventoriesTable') {
                echo "<p class='success'>✓ WORKS with explicit className!</p>";
                echo "<p class='error'>This means CakePHP conventions aren't being followed properly</p>";
                
                // Check associations with explicit class
                echo "<h4>Associations (with explicit className):</h4>";
                $assocs = $inventories2->associations();
                echo "<p>Count: " . count($assocs) . "</p>";
                foreach ($assocs as $assoc) {
                    echo "<p>- " . $assoc->getName() . " (" . $assoc->type() . ")</p>";
                }
            }
            
        } elseif ($className === 'App\\Model\\Table\\InventoriesTable') {
            echo "<p class='success'>✓ ✓ ✓ SUCCESS! Proper InventoriesTable loaded!</p>";
            
            echo "<h3>Associations Check:</h3>";
            $assocs = $inventories->associations();
            echo "<p>Total: " . count($assocs) . "</p>";
            foreach ($assocs as $assoc) {
                echo "<p class='success'>✓ " . $assoc->getName() . " (" . $assoc->type() . ")</p>";
            }
            
            // Try the actual query that was failing
            echo "<h3>Database Query Test (THE REAL TEST):</h3>";
            try {
                $query = $inventories->find()
                    ->contain(['Storages', 'Uoms'])
                    ->limit(1);
                $result = $query->first();
                
                echo "<p class='success'>✓ ✓ ✓ Query with contain(['Storages', 'Uoms']) WORKS!</p>";
                echo "<p class='success' style='font-size: 20px;'>THE ISSUE IS FIXED!</p>";
                echo "<p>You can now access: <a href='/asahi_v3/inventories'>/asahi_v3/inventories</a></p>";
                
            } catch (Exception $e) {
                echo "<p class='error'>✗ Query failed: " . $e->getMessage() . "</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ ERROR: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
    
    echo "<h2>Step 4: Database Connection Test</h2>";
    try {
        $connection = ConnectionManager::get('default');
        echo "<p class='success'>✓ Database connection 'default' works</p>";
        
        // Test if inventories table exists
        $tables = $connection->getSchemaCollection()->listTables();
        if (in_array('inventories', $tables)) {
            echo "<p class='success'>✓ 'inventories' table exists in database</p>";
        } else {
            echo "<p class='error'>✗ 'inventories' table NOT in database!</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
    }
    ?>
    
    <hr>
    <h2>Summary</h2>
    <p>If you see "THE ISSUE IS FIXED!" above, the problem is resolved and you can use /asahi_v3/inventories</p>
    <p>If you still see "Auto-Table" error, please share this entire page output for debugging.</p>
    
</body>
</html>
