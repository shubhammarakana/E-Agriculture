<?php
include 'db_connect.php';

$sql = "ALTER TABLE orders 
        ADD COLUMN buyer_address TEXT AFTER buyer_name,
        ADD COLUMN buyer_phone VARCHAR(20) AFTER buyer_address";

if ($conn->query($sql) === TRUE) {
    echo "Database updated successfully. Added buyer_address and buyer_phone to orders table.";
} else {
    echo "Error updating database: " . $conn->error;
}

$conn->close();
?>
