<?php
include 'db_connect.php';
$table = 'orders';
echo "<h2>Columns in $table</h2>";
$result = $conn->query("DESCRIBE $table");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
