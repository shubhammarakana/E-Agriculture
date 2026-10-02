<?php
include 'db_connect.php';

$sqls = [
    "ALTER TABLE users ADD COLUMN status ENUM('Active', 'Pending', 'Blocked') DEFAULT 'Active'",
    "ALTER TABLE users ADD COLUMN verification_status ENUM('Verified', 'Unverified', 'Pending') DEFAULT 'Unverified'",
    "ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP"
];

foreach ($sqls as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Column added successfully or already exists.<br>";
    } else {
        echo "Error or Column Exists: " . $conn->error . "<br>";
    }
}
?>
