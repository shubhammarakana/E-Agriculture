<?php
// admin/delete_chemical.php
include '../db_connect.php';
session_start();

// Role Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Check if chemical has sales/orders (pseudo-check, schema might vary)
    // Assuming 'order_items' table links to chemicals via product_id if type is chemical?
    // For now, simple delete or soft delete.
    // If strict FK, this might fail. We'll try.
    
    // Check if image exists to unlink
    $res = $conn->query("SELECT image_path FROM chemicals WHERE id='$id'");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $img = $row['image_path'];
        
        // Delete from DB
        $sql = "DELETE FROM chemicals WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            // Unlink image if not default and exists
            if (!empty($img) && file_exists("../" . $img) && strpos($img, 'default') === false) {
                 unlink("../" . $img);
            }
            header("Location: manage_chemicals.php?msg=deleted");
            exit();
        } else {
            // Ensure error message is clean
             $err = urlencode("Error: " . $conn->error);
             header("Location: manage_chemicals.php?msg=$err");
             exit();
        }
    } else {
        header("Location: manage_chemicals.php?msg=not_found");
        exit();
    }
}
header("Location: manage_chemicals.php");
?>
