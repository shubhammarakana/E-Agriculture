<?php
include 'db_connect.php';
$conn->query("UPDATE users SET status='Pending' WHERE id=107");
echo "Updated to Pending";
?>
