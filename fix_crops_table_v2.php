<?php
// fix_crops_table_v2.php
include 'db_connect.php';

// Add description column if it doesn't exist
$sql = "ALTER TABLE crops ADD COLUMN description TEXT AFTER name";

if ($conn->query($sql) === TRUE) {
    echo "Column 'description' added successfully to 'crops' table.";
} else {
    echo "Error adding column: " . $conn->error;
}
?>
