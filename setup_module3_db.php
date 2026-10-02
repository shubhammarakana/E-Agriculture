<?php
include 'db_connect.php';

// Function to run query
function runQuery($conn, $sql, $msg) {
    if ($conn->query($sql) === TRUE) {
        echo "$msg: Success<br>";
    } else {
        echo "$msg: Error - " . $conn->error . "<br>";
    }
}

// 1. Orders
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT(6) UNSIGNED NOT NULL,
    farmer_id INT(6) UNSIGNED NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Accepted', 'Packed', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
runQuery($conn, $sql, "Create Orders Table");

// 2. Order Items
$sql = "CREATE TABLE IF NOT EXISTS order_items (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT(6) UNSIGNED NOT NULL,
    crop_id INT(6) UNSIGNED NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    price_per_unit DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (crop_id) REFERENCES crops(id)
)";
runQuery($conn, $sql, "Create Order Items Table");

// 3. Farmer Profiles
$sql = "CREATE TABLE IF NOT EXISTS farmer_profiles (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    bio TEXT,
    verification_status ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending',
    verification_doc VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
runQuery($conn, $sql, "Create Farmer Profiles Table");

// 4. Reviews
$sql = "CREATE TABLE IF NOT EXISTS reviews (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT(6) UNSIGNED NOT NULL,
    buyer_id INT(6) UNSIGNED NOT NULL,
    rating INT(1) NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
runQuery($conn, $sql, "Create Reviews Table");

// 5. Notifications
$sql = "CREATE TABLE IF NOT EXISTS notifications (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('order', 'payment', 'system', 'alert') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
runQuery($conn, $sql, "Create Notifications Table");

// 6. Disease Reports
$sql = "CREATE TABLE IF NOT EXISTS disease_reports (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT(6) UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    diagnosis VARCHAR(255),
    severity VARCHAR(50),
    treatment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
runQuery($conn, $sql, "Create Disease Reports Table");

// 7. Crop Analytics
$sql = "CREATE TABLE IF NOT EXISTS crop_analytics (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    crop_name VARCHAR(100) NOT NULL,
    region VARCHAR(100),
    demand_score INT(3),
    price_trend ENUM('Up', 'Down', 'Stable'),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
runQuery($conn, $sql, "Create Analytics Table");

echo "Module 3 Database Setup Complete.";
?>
