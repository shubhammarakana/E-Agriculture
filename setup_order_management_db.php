<?php
include 'db_connect.php';

function runQuery($conn, $sql, $msg) {
    if ($conn->query($sql) === TRUE) {
        echo "$msg: Success<br>";
    } else {
        echo "$msg: Error - " . $conn->error . "<br>";
    }
}

// Delivery Tracking Table
$sql = "CREATE TABLE IF NOT EXISTS delivery_tracking (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT(6) UNSIGNED NOT NULL,
    status ENUM('Pending', 'Accepted', 'Packed', 'Shipped', 'Delivered', 'Cancelled') NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_by VARCHAR(100) DEFAULT 'System',
    location VARCHAR(255) DEFAULT NULL,
    comments TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
)";
runQuery($conn, $sql, "Create Delivery Tracking Table");

echo "Order Management Database Setup Complete.";
?>
