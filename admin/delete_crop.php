<?php
// delete_crop.php
include '../db_connect.php';
session_start();

// Check if user is logged in and is a farmer or admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['farmer', 'admin'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check ownership if needed (skipping for demo simplicity)

    // Safe Cleanup: Remove from Cart and Wishlist first
    $conn->query("DELETE FROM cart WHERE crop_id='$id'");
    $conn->query("DELETE FROM wishlist WHERE crop_id='$id'");

    // Attempt to delete from Crops
    try {
        $sql = "DELETE FROM crops WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            header("Location: manage_crops.php?msg=deleted");
        }
    } catch (mysqli_sql_exception $e) {
        // Check for Foreign Key Constraint (likely order_items)
        if ($e->getCode() == 1451) {
            header("Location: manage_crops.php?msg=error_dependency");
        } else {
            echo "Error deleting record: " . $e->getMessage();
        }
    }
} else {
    header("Location: manage_crops.php");
}
?>