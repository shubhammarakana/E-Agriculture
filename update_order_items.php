<?php
include 'db_connect.php';

// Add seed_id column
$sql = "ALTER TABLE order_items ADD COLUMN seed_id INT NULL DEFAULT NULL AFTER chemical_id";
if ($conn->query($sql)) {
    echo "Success: seed_id column added to order_items table.\n";
} else {
    echo "Error adding column: " . $conn->error . "\n";
}

// Add foreign key
$sql_fk = "ALTER TABLE order_items ADD CONSTRAINT fk_order_items_seed FOREIGN KEY (seed_id) REFERENCES seeds(id) ON DELETE SET NULL";
if ($conn->query($sql_fk)) {
    echo "Success: Foreign key constraint added.\n";
} else {
    echo "Error adding schema constraint: " . $conn->error . "\n";
}
?>
