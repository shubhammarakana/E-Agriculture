<?php
// vendor/delete_seed.php
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
    $check = $conn->query("SELECT id FROM seeds WHERE id='$id' AND seller_id='$vendor_id'");
    
    if ($check->num_rows > 0) {
        if ($conn->query("DELETE FROM seeds WHERE id='$id'")) {
            header("Location: seeds.php?msg=Seed listing deleted successfully");
        } else {
            header("Location: seeds.php?msg=Error deleting seed");
        }
    } else {
        header("Location: seeds.php?msg=Access denied");
    }
} else {
    header("Location: seeds.php");
}
exit();
?>
