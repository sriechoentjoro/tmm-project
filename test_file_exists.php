<?php
// Quick test to check apprentices data
$mysqli = new mysqli('localhost', 'root', 'root', 'cms_tmm_apprentices');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "=== Testing apprentices with order ID 4 ===\n\n";

// Count total
$result = $mysqli->query("SELECT COUNT(*) as total FROM apprentices WHERE apprentice_order_id = 4");
$row = $result->fetch_assoc();
echo "Total apprentices with order_id=4: " . $row['total'] . "\n\n";

// Count with photos
$result = $mysqli->query("SELECT COUNT(*) as total FROM apprentices WHERE apprentice_order_id = 4 AND image_photo IS NOT NULL AND image_photo != ''");
$row = $result->fetch_assoc();
echo "Apprentices WITH photos: " . $row['total'] . "\n\n";

// Count without photos
$result = $mysqli->query("SELECT COUNT(*) as total FROM apprentices WHERE apprentice_order_id = 4 AND (image_photo IS NULL OR image_photo = '')");
$row = $result->fetch_assoc();
echo "Apprentices WITHOUT photos: " . $row['total'] . "\n\n";

// Show sample records
echo "=== Sample records ===\n";
$result = $mysqli->query("SELECT id, name, image_photo FROM apprentices WHERE apprentice_order_id = 4 LIMIT 5");
while ($row = $result->fetch_assoc()) {
    $photo_status = ($row['image_photo'] && $row['image_photo'] != '') ? 'HAS PHOTO' : 'NO PHOTO';
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Photo: " . ($row['image_photo'] ?: 'NULL/EMPTY') . " | Status: $photo_status\n";
}

$mysqli->close();
