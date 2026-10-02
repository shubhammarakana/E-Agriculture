<?php
include 'db_connect.php';
$res = $conn->query("SELECT id, fullname, role, status FROM users WHERE role='vendor'");
echo "Total Vendors: " . $res->num_rows . "\n";
while ($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Name: " . $row['fullname'] . " | Status: '" . $row['status'] . "'\n";
}
?>
