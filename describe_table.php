<?php
include 'db_connect.php';
$table = 'order_items';
$result = $conn->query("DESCRIBE $table");
if ($result) {
    echo "Columns in $table:<br>";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
