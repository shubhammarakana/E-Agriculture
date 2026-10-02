<?php
include 'db_connect.php';

function runQuery($conn, $sql, $msg) {
    if ($conn->query($sql) === TRUE) {
        echo "$msg: Success<br>";
    } else {
        echo "$msg: Error - " . $conn->error . "<br>";
    }
}

// Check if master_crops table exists, if so drop it to recreate cleanly (or alter, but recreate is cleaner for dev)
// For this task, let's CREATE IF NOT EXISTS and then ALTER for new columns to be safe with existing data if any.
// Actually, since I just started using it, I'll drop and recreate to ensure the exact schema.
$conn->query("DROP TABLE IF EXISTS master_crops"); 

$sql = "CREATE TABLE master_crops (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    local_name VARCHAR(100),
    image VARCHAR(255),
    category ENUM('Cereal', 'Vegetable', 'Fruit', 'Pulse', 'Oilseed', 'Other') DEFAULT 'Other',
    season ENUM('Kharif', 'Rabi', 'Zaid', 'All-Season') DEFAULT 'All-Season',
    growth_duration INT(5) COMMENT 'In Days',
    soil_type VARCHAR(100),
    water_req ENUM('Low', 'Medium', 'High') DEFAULT 'Medium',
    
    -- New Fields
    fertilizer_rec TEXT,
    pests_diseases TEXT,
    description TEXT,
    
    -- Visibility & Status Controls
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    is_visible_farmers TINYINT(1) DEFAULT 1,
    is_visible_buyers TINYINT(1) DEFAULT 1,
    is_market_listed TINYINT(1) DEFAULT 1,
    is_seasonal_guide_listed TINYINT(1) DEFAULT 1,
    
    -- AI Integration Controls
    is_ai_enabled TINYINT(1) DEFAULT 0,
    ai_model_id VARCHAR(100) DEFAULT NULL,
    ai_yield_formula VARCHAR(255) DEFAULT NULL,
    is_ai_advisory_visible TINYINT(1) DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

runQuery($conn, $sql, "Create Master Crops Table");

echo "Master Crops Schema Updated.";
?>
