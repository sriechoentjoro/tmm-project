<?php
/**
 * Direct require test - bypass autoloader
 * Access: http://localhost/asahi_v3/direct_require_test.php
 */
?>
<!DOCTYPE html>
<html>
<head><title>Direct Require Test</title></head>
<body>
<h1>Direct Require Test</h1>

<?php
echo "<h2>Test 1: Direct file require</h2>";
$filePath = __DIR__ . '/../src/Model/Table/InventoriesTable.php';
echo "<p>File path: <code>$filePath</code></p>";
echo "<p>File exists: " . (file_exists($filePath) ? 'YES' : 'NO') . "</p>";

if (file_exists($filePath)) {
    echo "<p>Trying to require file directly...</p>";
    
    // First require dependencies
    require __DIR__ . '/../vendor/autoload.php';
    
    try {
        // This should work if the file is valid PHP
        require_once $filePath;
        
        echo "<p style='color: green;'>✓ File required successfully!</p>";
        
        // Now try to instantiate
        if (class_exists('App\\Model\\Table\\InventoriesTable', false)) {
            echo "<p style='color: green;'>✓ Class exists after require!</p>";
            
            try {
                $table = new \App\Model\Table\InventoriesTable();
                echo "<p style='color: green;'>✓ ✓ ✓ Can instantiate InventoriesTable!</p>";
                echo "<p>Class: " . get_class($table) . "</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>✗ Cannot instantiate: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Class NOT found even after require!</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Error requiring file: " . htmlspecialchars($e->getMessage()) . "</p>";
    } catch (ParseError $e) {
        echo "<p style='color: red;'>✗ Parse error in file: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "<h2>Test 2: Check composer autoload files</h2>";

$classmap = __DIR__ . '/../vendor/composer/autoload_classmap.php';
if (file_exists($classmap)) {
    $classes = require $classmap;
    echo "<p>Classmap has " . count($classes) . " entries</p>";
    
    if (isset($classes['App\\Model\\Table\\InventoriesTable'])) {
        echo "<p style='color: green;'>✓ InventoriesTable IS in classmap!</p>";
        echo "<p>Path: " . $classes['App\\Model\\Table\\InventoriesTable'] . "</p>";
    } else {
        echo "<p style='color: red;'>✗ InventoriesTable NOT in classmap!</p>";
        echo "<p>This means composer dump-autoload didn't find it.</p>";
        
        echo "<p>Searching for similar classes...</p>";
        foreach ($classes as $className => $path) {
            if (strpos($className, 'Table\\') !== false && strpos($className, 'Inventories') !== false) {
                echo "<p>Found: $className => $path</p>";
            }
        }
    }
}

echo "<h2>Test 3: PSR-4 Autoload</h2>";
$psr4File = __DIR__ . '/../vendor/composer/autoload_psr4.php';
if (file_exists($psr4File)) {
    $psr4 = require $psr4File;
    
    if (isset($psr4['App\\'])) {
        echo "<p style='color: green;'>✓ App\\ namespace registered</p>";
        echo "<p>Paths: " . implode(', ', $psr4['App\\']) . "</p>";
        
        // Check if the path exists
        foreach ($psr4['App\\'] as $path) {
            $fullPath = $path . 'Model/Table/InventoriesTable.php';
            echo "<p>Looking for: <code>$fullPath</code></p>";
            echo "<p>Exists: " . (file_exists($fullPath) ? 'YES ✓' : 'NO ✗') . "</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ App\\ namespace NOT registered!</p>";
    }
}

?>

</body>
</html>
