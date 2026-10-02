<?php
include 'db_connect.php';
$result = $conn->query("SHOW COLUMNS FROM users");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . "\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>