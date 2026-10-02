<?php
include 'db_connect.php';

$sql = "ALTER TABLE users ADD COLUMN address TEXT DEFAULT NULL";

if ($conn->query($sql) === TRUE) {
    echo "Column 'address' created successfully";
} else {
    echo "Error creating column: " . $conn->error;
}

$conn->close();
?>
