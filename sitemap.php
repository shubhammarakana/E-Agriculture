<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Site Map | AgriAI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .sitemap-container { max-width: 1000px; margin: 40px auto; padding: 20px; }
        .module-section { margin-bottom: 40px; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .module-title { font-size: 1.5rem; color: var(--primary); margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .link-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
        .link-card { display: block; padding: 15px; background: #f9f9f9; border-radius: 8px; text-decoration: none; color: #333; transition: all 0.3s; border: 1px solid #eee; }
        .link-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); background: var(--primary); color: white; }
        .link-card i { margin-right: 8px; }
    </style>
</head>
<body>
    <header>
        <div class="container header-container">
            <div class="logo">
                <i class="fa-solid fa-sitemap"></i>
                <span>Site<span class="highlight">Map</span></span>
            </div>
            <a href="index.php" class="btn btn-primary">Back to Home</a>
        </div>
    </header>

    <div class="sitemap-container">
        
        <!-- Authentication -->
        <div class="module-section">
            <h3 class="module-title"><i class="fas fa-lock"></i> Authentication & Landing</h3>
            <div class="link-grid">
                <a href="index.php" class="link-card"><i class="fas fa-home"></i> Home Page</a>
                <a href="login.php" class="link-card"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="register.php" class="link-card"><i class="fas fa-user-plus"></i> Register</a>
                <a href="forgot_password.php" class="link-card"><i class="fas fa-key"></i> Forgot Password</a>
                <a href="logout.php" class="link-card"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Farmer Module -->
        <div class="module-section">
            <h3 class="module-title"><i class="fas fa-tractor"></i> Farmer Module</h3>
            <div class="link-grid">
                <a href="farmer_dashboard.php" class="link-card"><i class="fas fa-th-large"></i> Dashboard</a>
                <a href="farmer_profile.php" class="link-card"><i class="fas fa-user"></i> Profile</a>
                <a href="add_crop.php" class="link-card"><i class="fas fa-plus"></i> Add Crop</a>
                <a href="manage_crops.php" class="link-card"><i class="fas fa-list"></i> Manage Crops</a>
                <a href="ai_price.php" class="link-card"><i class="fas fa-robot"></i> Price Predictor</a>
                <a href="incoming_orders.php" class="link-card"><i class="fas fa-shopping-basket"></i> Incoming Orders</a>
                <a href="sales_history.php" class="link-card"><i class="fas fa-history"></i> Sales History</a>
                <a href="earnings.php" class="link-card"><i class="fas fa-wallet"></i> Earnings</a>
                <a href="disease_detect.php" class="link-card"><i class="fas fa-leaf"></i> Disease Detect</a>
                <a href="crop_analytics.php" class="link-card"><i class="fas fa-chart-line"></i> Analytics</a>
                <a href="reviews.php" class="link-card"><i class="fas fa-star"></i> Reviews</a>
                <a href="notifications.php" class="link-card"><i class="fas fa-bell"></i> Notifications</a>
            </div>
        </div>

        <!-- Buyer Module -->
        <div class="module-section">
            <h3 class="module-title"><i class="fas fa-shopping-cart"></i> Buyer Module</h3>
            <div class="link-grid">
                <a href="buyer_dashboard.php" class="link-card"><i class="fas fa-th-large"></i> Dashboard</a>
                <a href="buyer_profile.php" class="link-card"><i class="fas fa-user"></i> Profile</a>
                <a href="shop_crops.php" class="link-card"><i class="fas fa-store"></i> Shop Crops</a>
                <a href="cart.php" class="link-card"><i class="fas fa-shopping-cart"></i> Cart</a>
                <a href="wishlist.php" class="link-card"><i class="fas fa-heart"></i> Wishlist</a>
                <a href="my_orders.php" class="link-card"><i class="fas fa-box"></i> My Orders</a>
                <a href="purchase_history.php" class="link-card"><i class="fas fa-history"></i> Purchase History</a>
                <a href="buyer_notifications.php" class="link-card"><i class="fas fa-bell"></i> Notifications</a>
            </div>
        </div>

    </div>

</body>
</html>
