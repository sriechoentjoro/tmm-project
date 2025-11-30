<?php
echo '<pre>';
echo 'DOCUMENT_ROOT: ' . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo 'REQUEST_URI: ' . $_SERVER['REQUEST_URI'] . "\n";
echo 'SCRIPT_FILENAME: ' . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo 'SCRIPT_NAME: ' . $_SERVER['SCRIPT_NAME'] . "\n";
echo 'PHP_SELF: ' . $_SERVER['PHP_SELF'] . "\n";
echo "\nFile check:\n";
echo 'CSS file exists: ' . (file_exists('d:/xampp/htdocs/asahi_v3/webroot/css/table-enhanced.css') ? 'YES' : 'NO') . "\n";
echo 'Relative check: ' . (file_exists('./css/table-enhanced.css') ? 'YES' : 'NO') . "\n";
echo 'Current dir: ' . getcwd() . "\n";
echo '</pre>';
