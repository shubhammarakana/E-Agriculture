<?php
include 'db_connect.php';
$result = $conn->query("SELECT id, fullname, email, role FROM users WHERE role='farmer' LIMIT 1");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Email: " . $row['email'] . "\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>