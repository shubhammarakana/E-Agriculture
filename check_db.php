<?php
include 'db_connect.php';
$result = $conn->query("DESCRIBE chemicals");
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
