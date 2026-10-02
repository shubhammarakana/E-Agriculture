<?php
// admin/delete_order.php
include '../db_connect.php';
session_start();

// Check if user is logged in and is a farmer or admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['farmer', 'admin'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Safety check: specific farmers can only delete their own orders (if schema supports it)
    // For now assuming Admin can delete any, Farmer can delete own
    
    $sql = "DELETE FROM orders WHERE id = $id";
    if ($_SESSION['role'] !== 'admin') {
         $farmer_id = $_SESSION['user_id'];
         $sql .= " AND farmer_id = $farmer_id";
    }

    if ($conn->query($sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            header("Location: incoming_orders.php?msg=deleted");
        } else {
             // Order not found or permission denied
             header("Location: incoming_orders.php?msg=error");
        }
    } else {
        header("Location: incoming_orders.php?msg=error");
    }
} else {
    header("Location: incoming_orders.php");
}
?>
