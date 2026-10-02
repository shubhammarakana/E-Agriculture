<?php
// dev_login.php
session_start();
include 'db_connect.php';

// Hardcoded ID for development - This matches the farmer found earlier (ID 10)
$farmer_id = 10;

$sql = "SELECT * FROM users WHERE id = '$farmer_id' AND role = 'farmer'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Set Session Variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['fullname']; // Use 'fullname' as per schema
    $_SESSION['email'] = $user['email'];
    $_SESSION['profile_image'] = $user['profile_image'];

    // Redirect to Dashboard
    header("Location: farmer/dashboard.php");
    exit();
} else {
    echo "Error: Test user not found. Please check database.";
}
?>