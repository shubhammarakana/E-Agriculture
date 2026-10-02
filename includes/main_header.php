<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-Login via Cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    if (isset($conn)) {
        $token = $conn->real_escape_string($_COOKIE['remember_me']);
        $sql_remember = "SELECT id, fullname, role, profile_image, status FROM users WHERE remember_token='$token'";
        $res_remember = $conn->query($sql_remember);
        if ($res_remember && $res_remember->num_rows > 0) {
            $user = $res_remember->fetch_assoc();
            if ($user['status'] == 'Active' || $user['status'] == 'Verified') { // Basic active check
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['profile_image'] = $user['profile_image'];
            }
        }
    }
}

// Calculate Cart Count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    if (isset($conn)) {
        $uid_for_count = $_SESSION['user_id'];
        $count_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
        if ($stmt_count = $conn->prepare($count_sql)) {
            $stmt_count->bind_param("i", $uid_for_count);
            $stmt_count->execute();
            $stmt_count->bind_result($cart_count_val);
            $stmt_count->fetch();
            $cart_count = $cart_count_val ? (int)$cart_count_val : 0;
            $stmt_count->close();
        }
    }
} else {
    // Guest (Persistent DB Check)
    if (isset($conn)) {
        include_once __DIR__ . '/cart_helper.php';
        $guest_token = get_guest_token();

        $count_g = "SELECT SUM(quantity) as total FROM cart WHERE guest_token_id = ?";
        if ($stmt_g = $conn->prepare($count_g)) {
            $stmt_g->bind_param("s", $guest_token);
            $stmt_g->execute();
            $stmt_g->bind_result($g_total);
            $stmt_g->fetch();
            $cart_count = $g_total ? (int)$g_total : 0;
            $stmt_g->close();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'AgriAI - Future of Farming'; ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
    <!-- Always include dashboard CSS if logged in, or conditionally -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/dashboard.css">
    <?php endif; ?>

    <!-- Page Specific CSS -->
    <?php if (isset($extra_css))
        echo $extra_css; ?>

    <style>
        :root {
            --primary-green: #16a34a;
            --dark-green: #065f46;
            --accent-lime: #a3e635;
            --charcoal: #334155;
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }

        #main-header {
            position: sticky;
            top: 0;
            z-index: 2000;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--glass-border);
            padding: 0.8rem 0;
            transition: all 0.3s ease;
        }

        .header-container {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 2rem;
        }

        /* Left Zone: Logo */
        .logo-section {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .logo-section a {
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--dark-green);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo-section .tagline {
            font-size: 0.65rem;
            color: #64748b;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Center Zone: Search Bar */
        .search-zone {
            display: flex;
            justify-content: center;
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
        }

        .amazon-search-bar {
            display: flex;
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
        }

        .amazon-search-bar:focus-within {
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.2);
            border-color: var(--primary-green);
        }

        .search-category {
            background: #f8fafc;
            border: none;
            border-right: 1px solid #e2e8f0;
            padding: 0 1.2rem;
            font-size: 0.85rem;
            color: #475569;
            cursor: pointer;
            outline: none;
        }

        .search-input {
            flex: 1;
            border: none;
            padding: 0.7rem 1.2rem;
            font-size: 0.95rem;
            outline: none;
            background: transparent;
        } 

        .search-btn {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            border: none;
            padding: 0 1.5rem;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .search-btn:hover {
            opacity: 0.9;
        }

        /* Right Zone: Action Icons */
        .action-zone {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .icon-capsule {
            background: rgba(255, 255, 255, 0.5);
            padding: 8px 15px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.3s;
            position: relative;
        }

        .icon-capsule:hover {
            background: white;
            transform: translateY(-2px);
            color: var(--primary-green);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .icon-capsule i {
            font-size: 1.1rem;
        }

        .badge-count {
            position: absolute;
            top: -5px;
            right: 5px;
            background: #ef4444;
            color: white;
            font-size: 0.65rem;
            padding: 1px 5px;
            border-radius: 10px;
            min-width: 15px;
            text-align: center;
        }

        /* User Profile in Circle */
        .profile-trigger {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--primary-green);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .profile-trigger:hover {
            transform: scale(1.1);
        }

        .profile-trigger img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Navigation Links (Sub-menu style) */
        .nav-link-bar {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            display: flex;
            justify-content: center;
            padding: 15px 0;
            gap: 2rem;
        }

        .nav-link-bar a {
            font-size: 0.85rem;
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link-bar a:hover {
            color: var(--primary-green);
        }

        /* Mobile Menu Toggle */
        .mobile-hamburger {
            display: none;
            font-size: 1.5rem;
            color: var(--charcoal);
            cursor: pointer;
        }

        @media (max-width: 1024px) {
            .header-container {
                grid-template-columns: auto 1fr auto;
                gap: 1rem;
            }

            .logo-section .tagline,
            .icon-capsule span {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                grid-template-columns: 1fr auto auto;
            }

            .search-zone {
                display: none;
                /* Mobile search usually expands on tap or has separate bar */
            }

            .mobile-hamburger {
                display: block;
            }
        }
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmLogout(event) {
            event.preventDefault();
            fetch('<?php echo BASE_URL; ?>logout.php?ajax=true')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Logged Out Successfully',
                            text: 'See you again soon!',
                            icon: 'success',
                            confirmButtonColor: '#16a34a'
                        }).then(() => {
                            window.location.href = '<?php echo BASE_URL; ?>index.php';
                        });
                    }
                });
        }
    </script>
</head>

<body>

    <header id="main-header">
        <div class="container header-container">
            <!-- Left Zone: Logo & Tagline -->
            <div class="logo-section">
                <a href="<?php echo BASE_URL; ?>index.php">
                    <img src="<?php echo BASE_URL; ?>images/logo2.png" alt="AgriAI Logo" style="max-height: 110px; width: auto; object-fit: contain; clip-path: inset(0 0 25% 0); margin-bottom: -20px; margin-top: -10px;">
                </a>
            </div>

            <!-- Center Zone: Global Search -->
            <div class="search-zone">
                <form action="<?php echo BASE_URL; ?>search_results.php" method="GET" class="amazon-search-bar">
                    <select class="search-category" name="cat">
                        <option value="all">All</option>
                        <option value="crops">Crops</option>
                        <option value="chemicals">Chemicals</option>
                        <option value="seeds">Seeds</option>
                    </select>
                    <input type="text" name="q" class="search-input"
                        placeholder="Search for crops, seeds, fertilizers...">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Right Zone: Action Icons -->
            <div class="action-zone">
                <!-- Mandi Badge (Agri Feature) -->
                <a href="<?php echo BASE_URL; ?>market_trends.php" class="icon-capsule"
                    style="background: rgba(22, 163, 74, 0.1); border-color: rgba(22, 163, 74, 0.2); color: var(--primary-green);">
                    <i class="fas fa-chart-line"></i>
                    <span>Mandi Price</span>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>


                    
                    <!-- Cart Link (Not for Admins or Vendors) -->
                    <?php if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'vendor'): ?>
                        <a href="<?php echo BASE_URL; ?>cart.php" class="icon-capsule" style="background: var(--primary-green); color: white; border: none; margin-right: 15px;">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Cart</span>
                            <?php if ($cart_count > 0): ?>
                                <span class="badge-count" style="background: #ef4444; color: white;"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>

                    <!-- Profile Trigger -->
                    <div class="dropdown">
                        <div class="profile-trigger dropbtn">
                            <?php 
                                $profile_img_path = !empty($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'images/default-user.png';
                                // Fix for uploads/ vs images/ path consistency
                                if (strpos($profile_img_path, 'uploads/') === false && strpos($profile_img_path, 'images/') === false) {
                                    // If just filename, assume uploads/ and prepend BASE_URL
                                    $display_img = BASE_URL . 'uploads/' . $profile_img_path;
                                } else {
                                    // If it has a path, just accept it (relative to root)
                                    $display_img = (strpos($profile_img_path, 'http') === 0) ? $profile_img_path : BASE_URL . $profile_img_path;
                                }
                            ?>
                            <img src="<?php echo $display_img; ?>" alt="Profile">
                        </div>
                        <div class="dropdown-content">
                            <div style="padding: 10px; border-bottom: 1px solid #f1f5f9; margin-bottom: 5px;">
                                <small style="display:block; color: #94a3b8;">Welcome,</small>
                                <strong><?php echo $_SESSION['fullname'] ?? 'User'; ?></strong>
                            </div>
                            <a
                                href="<?php echo ($_SESSION['role'] == 'admin') ? BASE_URL . 'admin/dashboard.php' : (($_SESSION['role'] == 'farmer') ? BASE_URL . 'farmer/dashboard.php' : (($_SESSION['role'] == 'vendor') ? BASE_URL . 'vendor/dashboard.php' : BASE_URL . 'purchase_history.php')); ?>">
                                <i class="fas fa-th-large"></i> Dashboard
                            </a>
                            
                            <!-- Dynamic Profile Link -->
                            <?php 
                            $profile_link = BASE_URL . 'profile.php';
                            if($_SESSION['role'] === 'admin') $profile_link = BASE_URL . 'admin/profile.php';
                            if($_SESSION['role'] === 'vendor') $profile_link = BASE_URL . 'vendor/profile.php';
                            ?>
                            <a href="<?php echo $profile_link; ?>">
                                <i class="fas fa-user-circle"></i> My Profile
                            </a>

                            <a href="#" onclick="confirmLogout(event)" style="color: #ef4444;">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                <?php else: 
                    $curr = basename($_SERVER['PHP_SELF']);
                    $qs_auth = '';
                    if ($curr == 'checkout.php') {
                        $qs_auth = '?redirect=checkout.php';
                    } elseif ($curr == 'cart.php') {
                        $qs_auth = '?redirect=cart.php';
                    }
                ?>
                    <a href="<?php echo BASE_URL; ?>login.php<?php echo $qs_auth; ?>" class="icon-capsule">
                        <i class="fas fa-user"></i>
                        <span>Login / Register</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>cart.php" class="icon-capsule" style="background: #4f46e5; color: white; border: none; margin-left: 10px;">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Cart</span>
                        <?php if ($cart_count > 0): ?>
                            <span class="badge-count" style="background: #ef4444; color: white;"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <div class="mobile-hamburger" id="mobile-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </div>
    </header>

<style>
    /* Consolidated Sub-Nav Styles */
    .sub-nav {
         width: 100%;
         overflow-x: auto;
         overflow-y: hidden; /* Force hide vertical */
         -webkit-overflow-scrolling: touch;
         scrollbar-width: none; /* Firefox */
         margin: 0 auto;
    }
    .sub-nav::-webkit-scrollbar {
        display: none; /* Chrome/Safari */
    }
    .sub-nav ul {
        display: flex;
        flex-wrap: nowrap; /* Prevent wrapping */
        gap: 15px;
        padding: 5px 10px; /* Adjust padding */
        white-space: nowrap;
        list-style: none; /* moved from below */
        margin: 0;        /* moved from below */
        overflow-x: auto; /* ensure ul scrolls too if needed */
        scrollbar-width: none; 
    }
    .sub-nav ul::-webkit-scrollbar {
        display: none;
    }
    .sub-nav li {
        flex: 0 0 auto; 
    }
</style>

    <!-- Sub-Nav Link Bar -->
    <?php if (!isset($hide_sub_header)): ?>
    <div class="nav-link-bar">
        <a href="<?php echo BASE_URL; ?>index.php" style="color: #475569; text-decoration: none; padding: 0; background: none; font-weight: 500;">Home</a>
        <a href="<?php echo BASE_URL; ?>shop_crops.php"
            class="<?php echo (isset($page) && $page == 'shop_crops') ? 'active' : ''; ?>">Shop Crops</a>
        <?php if (!isset($page) || $page !== 'track_order'): ?>
        <a href="<?php echo BASE_URL; ?>chemical_store.php"
            class="<?php echo (isset($page) && $page == 'chemical_store') ? 'active' : ''; ?>">Chemical Shop</a>
        <a href="<?php echo BASE_URL; ?>seed_store.php"
            class="<?php echo (isset($page) && $page == 'seed_store') ? 'active' : ''; ?>">Seed Store</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay"
        style="display:none; position:fixed; top:70px; left:0; width:100%; height:calc(100vh - 70px); background:rgba(255,255,255,0.95); backdrop-filter:blur(10px); z-index:1500; padding:2rem;">
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            <a href="<?php echo BASE_URL; ?>index.php" style="font-size:1.2rem; font-weight:600; color:var(--charcoal); text-decoration:none;">Home</a>
            <a href="<?php echo BASE_URL; ?>shop_crops.php"
                style="font-size:1.2rem; font-weight:600; color:var(--charcoal); text-decoration:none;">🛒 Shop
                Crops</a>
            <?php if (!isset($page) || $page !== 'track_order'): ?>
            <a href="<?php echo BASE_URL; ?>chemical_store.php"
                style="font-size:1.2rem; font-weight:600; color:var(--charcoal); text-decoration:none;">🧪 Chemical
                Shop</a>
            <a href="<?php echo BASE_URL; ?>seed_store.php"
                style="font-size:1.2rem; font-weight:600; color:var(--charcoal); text-decoration:none;">🌱 Seed
                Shop</a>
            <?php endif; ?>
            <hr style="border:0; border-top:1px solid #eee;">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="<?php echo BASE_URL; ?>login.php"
                    style="font-size:1.2rem; font-weight:600; color:var(--primary-green); text-decoration:none;">Login / Register</a>
            <?php else: ?>
                <a href="<?php echo ($_SESSION['role'] == 'farmer') ? BASE_URL . 'farmer/dashboard.php' : (($_SESSION['role'] == 'admin') ? BASE_URL . 'admin/dashboard.php' : BASE_URL . 'buyer/dashboard.php'); ?>"
                    style="font-size:1.2rem; font-weight:600; color:var(--primary-green); text-decoration:none;">Dashboard</a>
                <a href="#" onclick="confirmLogout(event)"
                    style="font-size:1.2rem; font-weight:600; color:#ef4444; text-decoration:none;">Logout</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sub-Header Role-Based Navigation -->
    <?php if (isset($_SESSION['user_id']) && !isset($hide_sub_header)): ?>
        <?php if ($_SESSION['role'] == 'farmer'): ?>
            
            </div>
        <?php elseif ($_SESSION['role'] == 'buyer'): ?>
            <div class="sub-header">
                <div class="container">
                    <nav class="sub-nav">
                        <ul>
                            <li><a href="<?php echo BASE_URL; ?>index.php" class="<?php echo ($page == 'home') ? 'active' : ''; ?>">
                                <i class="fas fa-home"></i> Home</a></li>
                            <li><a href="<?php echo BASE_URL; ?>shop_crops.php" class="<?php echo ($page == 'shop') ? 'active' : ''; ?>">
                                <i class="fas fa-shopping-cart"></i> Crops</a></li>
                            <li><a href="<?php echo BASE_URL; ?>chemical_store.php" class="<?php echo ($page == 'chemical_store') ? 'active' : ''; ?>">
                                <i class="fas fa-flask"></i> Chemicals</a></li>
                            <li><a href="<?php echo BASE_URL; ?>seed_store.php" class="<?php echo ($page == 'seed_store') ? 'active' : ''; ?>">
                                <i class="fas fa-seedling"></i> Seeds</a></li>

                            <li><a href="<?php echo BASE_URL; ?>profile.php" class="<?php echo ($page == 'profile') ? 'active' : ''; ?>">
                                <i class="fas fa-user-circle"></i> Profile</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php elseif ($_SESSION['role'] == 'vendor'): ?>
            <div class="sub-header">
                <div class="container">
                    <nav class="sub-nav">
                        <ul>
                            <li><a href="<?php echo BASE_URL; ?>vendor/dashboard.php"
                                    class="<?php echo ($page == 'dashboard') ? 'active' : ''; ?>"><i class="fas fa-th-large"></i>
                                    Dashboard</a></li>
                            <li><a href="<?php echo BASE_URL; ?>vendor/products.php"
                                    class="<?php echo ($page == 'products') ? 'active' : ''; ?>"><i class="fas fa-box-open"></i>
                                    My Crops</a></li>
                            <li><a href="<?php echo BASE_URL; ?>vendor/add_product.php"
                                    class="<?php echo ($page == 'add_product') ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i>
                                    Add Crop</a></li>
                            <li><a href="<?php echo BASE_URL; ?>vendor/chemicals.php"
                                    class="<?php echo ($page == 'chemicals') ? 'active' : ''; ?>"><i class="fas fa-flask"></i>
                                    My Chemicals</a></li>
                            <li><a href="<?php echo BASE_URL; ?>vendor/add_chemical.php"
                                    class="<?php echo ($page == 'add_chemical') ? 'active' : ''; ?>"><i class="fas fa-plus-square"></i>
                                    Add Chemical</a></li>
                            <li><a href="<?php echo BASE_URL; ?>vendor/seeds.php"
                                    class="<?php echo ($page == 'seeds') ? 'active' : ''; ?>"><i class="fas fa-seedling"></i>
                                    My Seeds</a></li>
                            <li><a href="<?php echo BASE_URL; ?>vendor/add_seeds.php"
                                    class="<?php echo ($page == 'add_seeds') ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i>
                                    Add Seeds</a></li>
                            <li><a href="<?php echo BASE_URL; ?>vendor/orders.php"
                                    class="<?php echo ($page == 'orders') ? 'active' : ''; ?>"><i class="fas fa-shopping-bag"></i>
                                    Orders</a></li>
                            <li><a href="<?php echo BASE_URL; ?>vendor/earnings.php"
                                    class="<?php echo ($page == 'earnings') ? 'active' : ''; ?>"><i class="fas fa-wallet"></i>
                                    Earnings</a></li>
                            <li><a href="<?php echo BASE_URL; ?>vendor/profile.php"
                                    class="<?php echo ($page == 'profile') ? 'active' : ''; ?>"><i class="fas fa-user-circle"></i>
                                    Profile</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php elseif ($_SESSION['role'] == 'admin'): ?>
            <div class="sub-header">
                <div class="container">
                    <nav class="sub-nav">
                        <ul>


                            <li><a href="<?php echo BASE_URL; ?>admin/dashboard.php"
                                    class="<?php echo ($page == 'dashboard') ? 'active' : ''; ?>"><i class="fas fa-th-large"></i>
                                    Dashboard</a></li>
                            <li><a href="<?php echo BASE_URL; ?>admin/manage_crops.php"
                                    class="<?php echo ($page == 'manage_crops') ? 'active' : ''; ?>"><i class="fas fa-list"></i>
                                    Market Listings</a></li>
                            <li><a href="<?php echo BASE_URL; ?>admin/incoming_orders.php"
                                    class="<?php echo ($page == 'orders') ? 'active' : ''; ?>"><i
                                        class="fas fa-shopping-basket"></i> Market Orders</a></li>
                            <li><a href="<?php echo BASE_URL; ?>admin/payment_history.php"
                                    class="<?php echo ($page == 'payment_history') ? 'active' : ''; ?>"><i
                                        class="fas fa-history"></i> Payments</a></li>
                            <li><a href="<?php echo BASE_URL; ?>admin/earnings.php"
                                    class="<?php echo ($page == 'earnings') ? 'active' : ''; ?>"><i
                                        class="fas fa-wallet"></i> Earnings</a></li>
                             <li><a href="<?php echo BASE_URL; ?>admin/manage_chemicals.php"
                                    class="<?php echo ($page == 'manage_chemicals') ? 'active' : ''; ?>"><i
                                        class="fas fa-vial"></i> Chemicals</a></li>
                            <li><a href="<?php echo BASE_URL; ?>admin/add_crop.php"
                                    class="<?php echo ($page == 'add_crop') ? 'active' : ''; ?>"><i
                                        class="fas fa-plus-circle"></i> Add Listing</a></li>
                            <li><a href="<?php echo BASE_URL; ?>admin/add_chemical.php"
                                    class="<?php echo ($page == 'add_chemical') ? 'active' : ''; ?>"><i
                                        class="fas fa-flask"></i> Add Chemical</a></li>
                            <li><a href="<?php echo BASE_URL; ?>admin/add_seeds.php"
                                    class="<?php echo ($page == 'add_seeds') ? 'active' : ''; ?>"><i
                                        class="fas fa-seedling"></i> Add Seeds</a></li>
                            <li><a href="<?php echo BASE_URL; ?>admin/manage_seeds.php"
                                    class="<?php echo ($page == 'manage_seeds') ? 'active' : ''; ?>"><i
                                        class="fas fa-tasks"></i> Manage Seeds</a></li>
                            <li><a href="<?php echo BASE_URL; ?>admin/vendor_requests.php"
                                    class="<?php echo ($page == 'vendor_requests') ? 'active' : ''; ?>"><i
                                        class="fas fa-user-clock"></i> Vendor Req</a></li>
                            <li><a href="<?php echo BASE_URL; ?>admin/users.php"
                                    class="<?php echo ($page == 'users') ? 'active' : ''; ?>"><i class="fas fa-users"></i>
                                    Users</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <style>
        /* Shared Sub-Header Styles */
        .sub-header {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
            position: sticky;
            top: 75px; 
            z-index: 999;
        }

        .sub-nav a {
            color: #555;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 10px 16px;
            border-radius: 20px;
            transition: all 0.2s;
            display: inline-block; /* Ensure block model for padding */
        }

        .sub-nav a:hover,
        .sub-nav a.active {
            color: var(--primary-green);
            background: rgba(22, 163, 74, 0.1);
        }

        /* Existing Dropdown visibility logic */
        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background: white;
            min-width: 200px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 10px;
            z-index: 10000;
            top: 100%;
            border: 1px solid #f1f5f9;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            display: block;
            padding: 10px;
            color: #475569;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .dropdown-content a:hover {
            background: #f8fafc;
            color: var(--primary-green);
        }
    </style>

    <script>
        document.getElementById('mobile-toggle').addEventListener('click', function () {
            const overlay = document.getElementById('mobile-menu-overlay');
            const icon = this.querySelector('i');
            if (overlay.style.display === 'none') {
                overlay.style.display = 'block';
                icon.classList.replace('fa-bars', 'fa-times');
            } else {
                overlay.style.display = 'none';
                icon.classList.replace('fa-times', 'fa-bars');
            }
        });
    </script>