<?php
// Suppress warnings
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
$_SERVER['HTTP_HOST'] = 'localhost'; // Fix for db_connect
include 'db_connect.php';

$output = "";
$tables = ['chemical_orders', 'chemical_order_items'];

foreach ($tables as $t) {
    $output .= "--- Table: $t ---\n";
    $result = $conn->query("DESCRIBE $t");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $output .= $row['Field'] . " | " . $row['Type'] . "\n";
        }
    } else {
        $output .= "Error: " . $conn->error . "\n";
    }
    $output .= "\n";
}

file_put_contents('schema_dump.txt', $output);
echo "Done.";
?>