<?php
// Check all master location tables structure
$host = 'localhost';
$username = 'root';
$password = '';
$db = 'cms_masters';

$conn = new mysqli($host, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tables = ['master_propinsis', 'master_kabupatens', 'master_kecamatans', 'master_kelurahans'];

foreach ($tables as $table) {
    echo "<h2>Table: $table</h2>";
    
    $sql = "DESCRIBE $table";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' cellpadding='8'>";
        echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
        while($row = $result->fetch_assoc()) {
            $highlight = (strpos($row['Field'], '_id') !== false && $row['Field'] !== 'id') ? ' style="background-color: #ffff99;"' : '';
            echo "<tr$highlight>";
            echo "<td><strong>" . $row['Field'] . "</strong></td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    } else {
        echo "<p>Table not found or no columns</p>";
    }
}

$conn->close();
?>