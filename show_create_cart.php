<?php
include 'db_connect.php';
$result = $conn->query("SHOW CREATE TABLE cart");
if ($result) {
    $row = $result->fetch_assoc();
    echo $row['Create Table'];
} else {
    echo "Error: " . $conn->error;
}
?>
