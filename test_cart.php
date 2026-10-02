<?php
// Test script for add_to_cart.php
session_start();
// Simulate logged in user
$_SESSION['user_id'] = 1; 

// Mock Server/Post data
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['id'] = 10; // Valid ID from DB
$_POST['qty'] = 2;
$_POST['type'] = 'chemical';
$_POST['redirect'] = 'shop';

// Use OB to capture output/header redirects
ob_start();
include 'add_to_cart.php';
ob_end_clean();

echo "Test run complete. Check cart_debug.log\n";
?>
