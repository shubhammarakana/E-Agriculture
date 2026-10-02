<?php
include 'db_connect.php';

$sql = "SELECT count(*) as count FROM crops";
$result = $conn->query($sql);
$count = $result->fetch_assoc()['count'];
echo "Total Crops: " . $count . "<br>";

if ($count > 0) {
    $sql = "SELECT id, name, price, image_path FROM crops LIMIT 5";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Price: " . $row['price'] . " | Image: " . $row['image_path'] . "<br>";
    }
}
?>