<?php
include 'db_connect.php';
$tables = ['chemical_orders_v2', 'chemical_order_items_v2'];
foreach ($tables as $t) {
    echo "--- Table: $t ---\n";
    $result = $conn->query("DESCRIBE $t");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo $row['Field'] . " - " . $row['Type'] . "\n";
        }
    } else {
        echo "Error or table not found.\n";
    }
}
?>