<?php
include 'db_connect.php';

$sqls = [
    "CREATE TABLE IF NOT EXISTS wishlist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_wish (user_id, product_id)
    )",
    "CREATE TABLE IF NOT EXISTS chemical_orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        buyer_id INT NOT NULL,
        seller_id INT NOT NULL,
        total_amount DECIMAL(10,2),
        payment_status ENUM('Pending', 'Paid', 'Failed') DEFAULT 'Pending',
        delivery_status ENUM('Processing', 'Shipped', 'Delivered') DEFAULT 'Processing',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS chemical_order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2),
        FOREIGN KEY (order_id) REFERENCES chemical_orders(id) ON DELETE CASCADE
    )"
];

foreach ($sqls as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table created/checked successfully.<br>";
    } else {
        echo "Error: " . $conn->error . "<br>";
    }
}
?>
