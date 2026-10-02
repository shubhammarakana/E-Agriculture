<?php
// logout.php
include 'db_connect.php';
session_start();

// Clear Remember Token in DB
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $conn->query("UPDATE users SET remember_token=NULL WHERE id='$uid'");
}

// Clear Cookie
setcookie('remember_me', '', time() - 3600, "/");

session_unset();
session_destroy();
if (isset($_GET['ajax'])) {
    echo json_encode(['status' => 'success']);
} else {
    header("Location: login.php?msg=logout");
}
exit();
?>
