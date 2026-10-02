<?php
include 'db_connect.php';
$t = 'chemical_cart';
echo "--- Table: $t ---\n";
$result = $conn->query("DESCRIBE $t");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error or table not found.\n";
}
?>