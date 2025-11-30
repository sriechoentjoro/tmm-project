<?php
// Direct database query to check candidate_educations data
$host = 'localhost';
$username = 'root';
$password = '';
$db_masters = 'cms_masters';
$db_candidates = 'cms_lpk_candidates';

echo "<h2>Database Connection Check</h2>";

// Connect to cms_lpk_candidates
$conn_candidates = new mysqli($host, $username, $password, $db_candidates);
if ($conn_candidates->connect_error) {
    die("Connection failed (cms_lpk_candidates): " . $conn_candidates->connect_error);
}
echo "✓ Connected to $db_candidates<br><br>";

// Connect to cms_masters
$conn_masters = new mysqli($host, $username, $password, $db_masters);
if ($conn_masters->connect_error) {
    die("Connection failed (cms_masters): " . $conn_masters->connect_error);
}
echo "✓ Connected to $db_masters<br><br>";

echo "<h2>Sample Data from candidate_educations</h2>";
$sql = "SELECT id, candidate_id, master_strata_id, master_propinsi_id, master_kabupaten_id, college_name FROM candidate_educations LIMIT 5";
$result = $conn_candidates->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Candidate ID</th><th>Strata ID</th><th>Propinsi ID</th><th>Kabupaten ID</th><th>College Name</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['candidate_id'] . "</td>";
        echo "<td>" . (isset($row['master_strata_id']) ? $row['master_strata_id'] : 'NULL') . "</td>";
        echo "<td>" . (isset($row['master_propinsi_id']) ? $row['master_propinsi_id'] : 'NULL') . "</td>";
        echo "<td>" . (isset($row['master_kabupaten_id']) ? $row['master_kabupaten_id'] : 'NULL') . "</td>";
        echo "<td>" . (isset($row['college_name']) ? $row['college_name'] : 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
} else {
    echo "No data found in candidate_educations<br><br>";
}

echo "<h2>Master Tables Check</h2>";

// Check master_stratas
$sql = "SHOW TABLES LIKE 'master_stratas'";
$result = $conn_masters->query($sql);
if ($result->num_rows > 0) {
    $sql = "SELECT COUNT(*) as count FROM master_stratas";
    $result = $conn_masters->query($sql);
    $row = $result->fetch_assoc();
    echo "✓ master_stratas table exists with {$row['count']} rows<br>";
    
    // Show sample data
    $sql = "SELECT id, title FROM master_stratas LIMIT 5";
    $result = $conn_masters->query($sql);
    echo "<table border='1' cellpadding='10'><tr><th>ID</th><th>Title</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['title']}</td></tr>";
    }
    echo "</table><br>";
} else {
    echo "✗ master_stratas table NOT FOUND<br>";
}

// Check master_propinsis
$sql = "SHOW TABLES LIKE 'master_propinsis'";
$result = $conn_masters->query($sql);
if ($result->num_rows > 0) {
    $sql = "SELECT COUNT(*) as count FROM master_propinsis";
    $result = $conn_masters->query($sql);
    $row = $result->fetch_assoc();
    echo "✓ master_propinsis table exists with {$row['count']} rows<br>";
    
    // Show sample data
    $sql = "SELECT id, title FROM master_propinsis LIMIT 5";
    $result = $conn_masters->query($sql);
    echo "<table border='1' cellpadding='10'><tr><th>ID</th><th>Title</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['title']}</td></tr>";
    }
    echo "</table><br>";
} else {
    echo "✗ master_propinsis table NOT FOUND<br>";
}

// Check master_kabupatens
$sql = "SHOW TABLES LIKE 'master_kabupatens'";
$result = $conn_masters->query($sql);
if ($result->num_rows > 0) {
    $sql = "SELECT COUNT(*) as count FROM master_kabupatens";
    $result = $conn_masters->query($sql);
    $row = $result->fetch_assoc();
    echo "✓ master_kabupatens table exists with {$row['count']} rows<br>";
    
    // Show sample data
    $sql = "SELECT id, title FROM master_kabupatens LIMIT 5";
    $result = $conn_masters->query($sql);
    echo "<table border='1' cellpadding='10'><tr><th>ID</th><th>Title</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['title']}</td></tr>";
    }
    echo "</table><br>";
} else {
    echo "✗ master_kabupatens table NOT FOUND<br>";
}

$conn_candidates->close();
$conn_masters->close();

echo "<h2>Summary</h2>";
echo "If master_strata_id, master_propinsi_id, and master_kabupaten_id are NULL or 0 in candidate_educations, ";
echo "then the issue is with the DATA, not the code configuration.<br>";
echo "Solution: Update existing records or insert proper foreign key values when adding new records.";
?>