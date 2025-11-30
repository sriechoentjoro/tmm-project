<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Parse Test</h1>";

$file = __DIR__ . '/../src/Model/Table/InventoriesTable.php';

echo "<p>Testing: $file</p>";
echo "<p>Exists: " . (file_exists($file) ? 'YES' : 'NO') . "</p>";

if (file_exists($file)) {
    // Try to parse the file
    $code = file_get_contents($file);
    
    echo "<h2>File Analysis</h2>";
    echo "<p>File size: " . strlen($code) . " bytes</p>";
    echo "<p>Lines: " . substr_count($code, "\n") . "</p>";
    
    // Check for BOM
    $bom = substr($code, 0, 3);
    if ($bom === "\xEF\xBB\xBF") {
        echo "<p style='color:red;'>✗ UTF-8 BOM detected at start! This can cause issues.</p>";
    } else {
        echo "<p style='color:green;'>✓ No BOM</p>";
    }
    
    // Check first line
    $firstLine = strtok($code, "\n");
    echo "<p>First line: <code>" . htmlspecialchars($firstLine) . "</code></p>";
    
    if (trim($firstLine) !== '<?php') {
        echo "<p style='color:red;'>✗ First line is not exactly '&lt;?php'!</p>";
    } else {
        echo "<p style='color:green;'>✓ First line OK</p>";
    }
    
    // Try to tokenize
    echo "<h2>PHP Tokenization Test</h2>";
    $tokens = token_get_all($code);
    echo "<p>Tokens: " . count($tokens) . "</p>";
    
    // Look for class definition
    $foundClass = false;
    $foundNamespace = false;
    foreach ($tokens as $i => $token) {
        if (is_array($token)) {
            if ($token[0] === T_NAMESPACE) {
                $foundNamespace = true;
                // Get namespace name
                $ns = '';
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                        $ns .= $tokens[$j][1];
                    } elseif ($tokens[$j] === '\\') {
                        $ns .= '\\';
                    } elseif ($tokens[$j] === ';') {
                        break;
                    }
                }
                echo "<p style='color:green;'>✓ Namespace found: <strong>$ns</strong></p>";
            }
            
            if ($token[0] === T_CLASS) {
                $foundClass = true;
                // Get class name
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                        echo "<p style='color:green;'>✓ Class found: <strong>{$tokens[$j][1]}</strong></p>";
                        break;
                    }
                }
            }
        }
    }
    
    if (!$foundNamespace) {
        echo "<p style='color:red;'>✗ No namespace declaration found!</p>";
    }
    if (!$foundClass) {
        echo "<p style='color:red;'>✗ No class declaration found!</p>";
    }
    
    // Now try to actually require it
    echo "<h2>Require Test</h2>";
    
    // Load composer first
    require __DIR__ . '/../vendor/autoload.php';
    
    try {
        require_once $file;
        echo "<p style='color:green;'>✓ File required without errors</p>";
        
        // Check if class is now available
        if (class_exists('App\\Model\\Table\\InventoriesTable', false)) {
            echo "<p style='color:green;'>✓✓✓ CLASS FOUND!</p>";
            
            $reflection = new ReflectionClass('App\\Model\\Table\\InventoriesTable');
            echo "<p>Methods: " . count($reflection->getMethods()) . "</p>";
            
        } else {
            echo "<p style='color:red;'>✗ Class still not found after require!</p>";
            
            // List all declared classes that match
            $allClasses = get_declared_classes();
            foreach ($allClasses as $cls) {
                if (stripos($cls, 'Inventories') !== false) {
                    echo "<p>Found similar: $cls</p>";
                }
            }
        }
        
    } catch (ParseError $e) {
        echo "<p style='color:red;'>✗ PARSE ERROR: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>Line: " . $e->getLine() . "</p>";
    } catch (Exception $e) {
        echo "<p style='color:red;'>✗ ERROR: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
