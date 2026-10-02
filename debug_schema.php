<?php
include 'db_connect.php';
$res = $conn->query("SHOW CREATE TABLE cart");
if ($res) {
    $row = $res->fetch_array();
    echo $row[1];
} else {
    echo "Error: " . $conn->error;
}
?>
