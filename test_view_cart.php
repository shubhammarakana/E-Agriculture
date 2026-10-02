<?php
// Test script for viewing cart.php
session_start();
// Simulate logged in user
$_SESSION['user_id'] = 1; 
$_SESSION['role'] = 'buyer';

// Capture output to prevent HTML dump
ob_start();
include 'cart.php';
ob_end_clean();

echo "View Cart simulation complete.\n";
?>
