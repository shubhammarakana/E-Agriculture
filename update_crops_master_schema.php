<?php
include 'db_connect.php';

// Create master_crops table
$sql = "CREATE TABLE IF NOT EXISTS master_crops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    local_name VARCHAR(100),
    category ENUM('Cereal', 'Vegetable', 'Fruit', 'Pulse', 'Oilseed', 'Other') DEFAULT 'Other',
    image VARCHAR(255),
    season ENUM('Kharif', 'Rabi', 'Zaid', 'All-Season') DEFAULT 'All-Season',
    growth_duration INT COMMENT 'Days',
    soil_type VARCHAR(255),
    water_req ENUM('Low', 'Medium', 'High'),
    description TEXT,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    is_ai_enabled BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'master_crops' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}
?>
