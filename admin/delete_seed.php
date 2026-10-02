<?php
// admin/delete_seed.php
include '../db_connect.php';
session_start();

// Role Check: Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Admin can delete any seed
    if ($conn->query("DELETE FROM seeds WHERE id='$id'")) {
        header("Location: manage_seeds.php?msg=deleted");
    } else {
        header("Location: manage_seeds.php?msg=error");
    }

} else {
    header("Location: manage_seeds.php");
}
exit();
?>
