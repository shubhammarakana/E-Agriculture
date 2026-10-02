<?php
include 'db_connect.php';
$result = $conn->query("SELECT email, role FROM users");
while($row = $result->fetch_assoc()) {
    echo "Email: " . $row['email'] . " | Role: " . $row['role'] . "\n";
}
?>
