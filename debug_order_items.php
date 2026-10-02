<?php
include 'db_connect.php';
$result = $conn->query("SHOW COLUMNS FROM order_items");
if ($result) {
    echo "Columns in order_items:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
