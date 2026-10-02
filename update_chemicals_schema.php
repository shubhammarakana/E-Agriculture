<?php
include 'db_connect.php';

// Add new columns to chemicals table
$columns = [
    "active_ingredient VARCHAR(255)",
    "brand VARCHAR(100)",
    "pack_size VARCHAR(50)",
    "crop_suitability TEXT",
    "safety_label VARCHAR(50)",
    "status ENUM('Active', 'Inactive') DEFAULT 'Active'"
];

foreach ($columns as $col) {
    try {
        $sql = "ALTER TABLE chemicals ADD COLUMN $col";
        if ($conn->query($sql) === TRUE) {
            echo "Column added: $col <br>";
        } else {
            // Ignore if exists, or show error
            echo "Skipped/Error (might exist): $col - " . $conn->error . "<br>";
        }
    } catch (Exception $e) {
        echo "Exception for $col: " . $e->getMessage() . "<br>";
    }
}

echo "Schema update complete.";
?>
