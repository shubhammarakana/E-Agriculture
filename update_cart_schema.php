<?php
include 'db_connect.php';

// Add seed_id column
$sql = "ALTER TABLE cart ADD COLUMN seed_id INT(11) DEFAULT NULL AFTER chemical_id";
if ($conn->query($sql) === TRUE) {
    echo "Column 'seed_id' added successfully";
} else {
    echo "Error adding column: " . $conn->error;
}

// Add FK if possible (optional, might fail like before, so maybe skip or try catch)
$sql_fk = "ALTER TABLE cart ADD CONSTRAINT fk_cart_seed FOREIGN KEY (seed_id) REFERENCES seeds(id) ON DELETE CASCADE";
if ($conn->query($sql_fk) === TRUE) {
    echo "\nFK constraint added successfully";
} else {
    echo "\nWarning: Could not add FK (might be minor): " . $conn->error;
}
?>
