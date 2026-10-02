<?php
include 'db_connect.php';

// Check if users table exists
$table_check = $conn->query("SHOW TABLES LIKE 'users'");
if ($table_check->num_rows == 0) {
    // Table doesn't exist, run create table from database.sql or manual
    echo "Users table missing. Please import database.sql first.";
} else {
    // Check for profile_image column
    $col_check = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
    if ($col_check->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255)");
        echo "Added profile_image column.<br>";
    } else {
        echo "profile_image column exists.<br>";
    }

    // Modify Role Enum if needed (older version might not have 'admin' if it wasn't there originally)
    // We can just try to modify it to be sure
    $conn->query("ALTER TABLE users MODIFY COLUMN role ENUM('farmer', 'buyer', 'admin') NOT NULL");
    echo "Role enum updated to include admin.<br>";
}

echo "Database setup complete.";
?>
