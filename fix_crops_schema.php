<?php
include 'db_connect.php';

// 1. Add Column
try {
    $conn->query("ALTER TABLE crops ADD COLUMN farmer_id INT");
    echo "Column 'farmer_id' added.<br>";
} catch (Exception $e) {
    echo "Column add skipped (maybe exists): " . $e->getMessage() . "<br>";
}

// 2. Update Data
try {
    // Set to first found farmer or 10
    $res = $conn->query("SELECT id FROM users WHERE role='farmer' LIMIT 1");
    if ($res->num_rows > 0) {
        $farmer_id = $res->fetch_assoc()['id'];
        $conn->query("UPDATE crops SET farmer_id = $farmer_id WHERE farmer_id IS NULL");
        echo "Updated existing crops with farmer_id = $farmer_id.<br>";
    }
} catch (Exception $e) {
    echo "Update failed: " . $e->getMessage() . "<br>";
}

// 3. Add Foreign Key
try {
    $conn->query("ALTER TABLE crops ADD FOREIGN KEY (farmer_id) REFERENCES users(id)");
    echo "Foreign Key added successfully.<br>";
} catch (Exception $e) {
    echo "FK add failed (types mismatch?): " . $e->getMessage() . "<br>";
}
?>
