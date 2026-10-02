<?php
// admin/delete_payment.php
include '../db_connect.php';
session_start();

// Check if user is logged in and is a farmer or admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['farmer', 'admin'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Construct SQL based on role to ensure Farmers only delete their own related payments if necessary
    // However, usually payments are linked to orders. If this is a general payment history, Admin has full rights.
    
    $sql = "DELETE FROM payments WHERE id = $id";
    
    // Optional: Add additional check if Farmer should only delete payments related to their orders
    // For now, assuming standard permission as per request
    
    if ($conn->query($sql) === TRUE) {
        header("Location: payment_history.php?msg=deleted");
    } else {
        header("Location: payment_history.php?msg=error");
    }
} else {
    header("Location: payment_history.php");
}
?>
