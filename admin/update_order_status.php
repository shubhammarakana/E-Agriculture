<?php
// update_order_status.php
include '../db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $farmer_id = $_SESSION['user_id'];

    // Security check: ensure order belongs to this farmer OR user is admin
    if ($_SESSION['role'] === 'admin') {
        $check_sql = "SELECT id FROM orders WHERE id='$order_id'";
    } else {
        $check_sql = "SELECT id FROM orders WHERE id='$order_id' AND farmer_id='$farmer_id'";
    }

    if ($conn->query($check_sql)->num_rows > 0) {
        $sql = "UPDATE orders SET status='$status' WHERE id='$order_id'";
        if ($conn->query($sql) === TRUE) {
            // Logic to add notification to buyer could go here
            header("Location: order_details.php?id=$order_id&msg=Status Updated");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        die("Unauthorized access.");
    }
} else {
    header("Location: incoming_orders.php");
    exit();
}
?>