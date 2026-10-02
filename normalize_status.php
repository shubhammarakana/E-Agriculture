<?php
include 'db_connect.php';
$conn->query("ALTER TABLE chemicals MODIFY status ENUM('Active', 'Inactive') DEFAULT 'Active'");
$conn->query("UPDATE chemicals SET status = 'Active' WHERE status = 'active'");
$conn->query("UPDATE chemicals SET status = 'Inactive' WHERE status = 'inactive'");
echo "Status normalized";
?>