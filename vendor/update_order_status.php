<?php
// vendor/update_order_status.php
include '../db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && $_SESSION['role'] == 'vendor') {
    $order_id = intval($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);
    $vendor_id = $_SESSION['user_id'];

    // Security check: ensure order belongs to this vendor
    $check_sql = "SELECT id FROM orders WHERE id='$order_id' AND farmer_id='$vendor_id'";

    if ($conn->query($check_sql)->num_rows > 0) {
        $sql = "UPDATE orders SET status='$status' WHERE id='$order_id'";
        if ($conn->query($sql) === TRUE) {
            header("Location: order_details.php?id=$order_id&msg=Status Updated Successfully");
            exit();
        } else {
            header("Location: order_details.php?id=$order_id&msg=Error Updating Status");
            exit();
        }
    } else {
        die("Unauthorized access.");
    }
} else {
    header("Location: orders.php");
    exit();
}
?>
