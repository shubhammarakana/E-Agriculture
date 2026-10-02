<?php
include 'db_connect.php';

// Create disease_logs table if not exists (using TEXT instead of JSON for compatibility)
$sql = "CREATE TABLE IF NOT EXISTS disease_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT NOT NULL,
    crop_type VARCHAR(100),
    region VARCHAR(100),
    image_path VARCHAR(255),
    disease_detected VARCHAR(100),
    confidence_score DECIMAL(5,2),
    analysis_result TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'disease_logs' created or already exists.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}
?>