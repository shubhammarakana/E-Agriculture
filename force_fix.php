<?php
include 'db_connect.php';
$id = 107;
echo "Target ID: $id\n";
$conn->query("UPDATE users SET status='Pending' WHERE id=$id");
echo "Rows affected: " . $conn->affected_rows . "\n";

$res = $conn->query("SELECT status FROM users WHERE id=$id");
$row = $res->fetch_assoc();
echo "New Status: " . $row['status'] . "\n";
?>
