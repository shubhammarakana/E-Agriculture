<?php
// save_review.php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $order_id = $_POST['order_id'];
    $rating = $_POST['rating'];
    $comment = $conn->real_escape_string($_POST['comment']);

    // Mock Insert - Assuming a 'reviews' table exists from Module 3 (Farmer Module)
    // We'd link it there. For now, just a placeholder success.
    // $sql = "INSERT INTO reviews (user_id, order_id, rating, comment) VALUES ...";

    // Just redirect
    header("Location: my_orders.php?msg=Review Submitted");
} else {
    header("Location: dashboard.php");
}
?>