<?php
include 'db_connect.php';

$conn->query("DROP TABLE IF EXISTS seeds");

// Create without FK first to ensure it works
$sql = "CREATE TABLE seeds (
    id int(11) NOT NULL AUTO_INCREMENT,
    seller_id int(11) NOT NULL,
    name varchar(255) NOT NULL,
    category varchar(100) NOT NULL COMMENT 'Hybrid, Organic, Desi, Imported',
    crop_type varchar(100) NOT NULL COMMENT 'Wheat, Rice, Cotton, etc',
    price decimal(10,2) NOT NULL,
    quantity int(11) NOT NULL COMMENT 'Available Stock',
    weight varchar(50) NOT NULL COMMENT 'Pack Size e.g. 1kg',
    description text DEFAULT NULL,
    image_path varchar(255) DEFAULT NULL,
    status enum('Active','Inactive') DEFAULT 'Active',
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (id),
    KEY seller_idx (seller_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) === TRUE) {
    echo "Table 'seeds' created successfully (No FK)";
} else {
    echo "Error creating table: " . $conn->error;
}
?>
