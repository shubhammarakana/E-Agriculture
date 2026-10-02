<?php
include 'db_connect.php';

try {
    $conn->query("ALTER TABLE users ADD COLUMN remember_token VARCHAR(255) DEFAULT NULL");
    echo "Added remember_token column successfully.";
} catch (Exception $e) {
    echo "Error (or column exists): " . $e->getMessage();
}
?>
