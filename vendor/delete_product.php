<?php
// vendor/delete_product.php
include '../db_connect.php';
session_start();

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $vendor_id = $_SESSION['user_id'];

    // Verify Ownership
    $check = $conn->query("SELECT id FROM crops WHERE id='$id' AND farmer_id='$vendor_id'");
    
    if ($check->num_rows > 0) {
        // Delete
        if ($conn->query("DELETE FROM crops WHERE id='$id'")) {
            header("Location: products.php?msg=Product deleted successfully");
            exit();
        } else {
            header("Location: products.php?msg=Error deleting product");
            exit();
        }
    } else {
        header("Location: products.php?msg=Access denied");
        exit();
    }
} else {
    header("Location: products.php");
    exit();
}
