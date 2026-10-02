<?php
include 'db_connect.php';
$res = $conn->query("SELECT id, name, image_path FROM chemicals");
while ($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Path: " . $row['image_path'] . "\n";
}
?>