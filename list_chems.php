<?php
include 'db_connect.php';
$res = $conn->query("SELECT id, name FROM chemicals LIMIT 5");
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " - " . $row['name'] . "\n";
}
?>
