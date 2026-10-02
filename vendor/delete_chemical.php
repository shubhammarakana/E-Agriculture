<?php
// vendor/delete_chemical.php
include '../db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $vendor_id = $_SESSION['user_id'];

    // Verify Ownership
    $check = $conn->query("SELECT id FROM chemicals WHERE id='$id' AND seller_id='$vendor_id'");
    
    if ($check->num_rows > 0) {
        if ($conn->query("DELETE FROM chemicals WHERE id='$id'")) {
            header("Location: chemicals.php?msg=Chemical deleted successfully");
        } else {
            header("Location: chemicals.php?msg=Error deleting chemical");
        }
    } else {
        header("Location: chemicals.php?msg=Access denied");
    }
} else {
    header("Location: chemicals.php");
}
exit();
