
<?php
ob_start(); // Buffer output to prevent header errors
// checkout.php - PUBLIC REDESIGN
include 'db_connect.php';
session_start();

// Handle User & Session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$user_data = null;

if ($user_id) {
    $user_data = $conn->query("SELECT * FROM users WHERE id='$user_id'")->fetch_assoc();
} else {
    include_once 'includes/cart_helper.php';
    $guest_token = get_guest_token();
}
// Guest logic continues below without redirect

// Order Content Logic
$direct_buy = false;
$direct_product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;
$direct_qty = isset($_GET['qty']) ? (int) $_GET['qty'] : 1;
$items = [];

if ($direct_product_id) {
    $direct_buy = true;
    $type = isset($_GET['type']) ? $_GET['type'] : 'crop';
    
    if ($type == 'chemical') {
        $sql = "SELECT c.id, c.name, c.price, c.image_path, c.category, u.fullname as seller_name 
                FROM chemicals c 
                JOIN users u ON c.seller_id = u.id 
                WHERE c.id='$direct_product_id'";
        $farmer_label = 'Seller';
    } elseif ($type == 'seed') {
         $sql = "SELECT s.id, s.name, s.price, s.image_path, s.category, u.fullname as seller_name 
                FROM seeds s 
                JOIN users u ON s.seller_id = u.id 
                WHERE s.id='$direct_product_id'";
        $farmer_label = 'Seller';       
    } else {
        $sql = "SELECT c.id, c.name, c.price, c.image_path, c.category, u.fullname as farmer_name 
                FROM crops c 
                JOIN users u ON c.farmer_id = u.id 
                WHERE c.id='$direct_product_id'";
    }

    $prod = $conn->query($sql)->fetch_assoc();
    if (!$prod)
        die("Product not found");

    $items[] = [
        'id' => $prod['id'],
        'name' => $prod['name'],
        'price' => $prod['price'],
        'qty' => $direct_qty,
        'image' => !empty($prod['image_path']) ? $prod['image_path'] : ($type == 'seed' ? 'images/default-seed.png' : 'images/default-crop.png'),
        'farmer' => ($type == 'chemical' || $type == 'seed') ? $prod['seller_name'] : $prod['farmer_name'],
        'category' => $prod['category'] ?? ucfirst($type),
        'type' => $type
    ];
} else {
    // Unified Fetch for Cart Items (DB or Session)
    if ($user_id) {
        // --- LOGGED IN: Fetch from DB ---
        // Crops
        $sql_crops = "SELECT c.quantity as qty, cr.id, cr.name, cr.price, cr.image_path, cr.category, u.fullname as farmer_name 
                FROM cart c 
                JOIN crops cr ON c.crop_id = cr.id 
                JOIN users u ON cr.farmer_id = u.id 
                WHERE c.user_id='$user_id'";
        $res = $conn->query($sql_crops);
        while ($row = $res->fetch_assoc()) {
            $items[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'qty' => $row['qty'],
                'image' => !empty($row['image_path']) ? $row['image_path'] : 'images/default-crop.png',
                'farmer' => $row['farmer_name'],
                'category' => $row['category'] ?? 'Crop',
                'type' => 'crop'
            ];
        }

        // Chemicals
        $sql_chem = "SELECT c.quantity as qty, ch.id, ch.name, ch.price, ch.image_path, ch.category, u.fullname as seller_name 
                FROM cart c 
                JOIN chemicals ch ON c.chemical_id = ch.id 
                JOIN users u ON ch.seller_id = u.id 
                WHERE c.user_id='$user_id'";
        $res = $conn->query($sql_chem);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $items[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'qty' => $row['qty'],
                    'image' => !empty($row['image_path']) ? $row['image_path'] : 'images/default_chem.png',
                    'farmer' => $row['seller_name'],
                    'category' => $row['category'] ?? 'Chemical',
                    'type' => 'chemical'
                ];
            }
        }

        // Seeds
        $sql_seeds = "SELECT c.quantity as qty, s.id, s.name, s.price, s.image_path, s.category, u.fullname as seller_name 
                FROM cart c 
                JOIN seeds s ON c.seed_id = s.id 
                JOIN users u ON s.seller_id = u.id 
                WHERE c.user_id='$user_id'";
        $res = $conn->query($sql_seeds);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $items[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'qty' => $row['qty'],
                    'image' => !empty($row['image_path']) ? $row['image_path'] : 'images/default-seed.png',
                    'farmer' => $row['seller_name'],
                    'category' => $row['category'] ?? 'Seed',
                    'type' => 'seed'
                ];
            }
        }
    } else {
        // --- GUEST: Fetch from DB (Persistent) ---
        
        // Crops
        $sql_crops = "SELECT c.quantity as qty, cr.id, cr.name, cr.price, cr.image_path, cr.category, u.fullname as farmer_name 
                FROM cart c 
                JOIN crops cr ON c.crop_id = cr.id 
                JOIN users u ON cr.farmer_id = u.id 
                WHERE c.guest_token_id = ?";
        $stmt = $conn->prepare($sql_crops);
        $stmt->bind_param("s", $guest_token);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $items[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'qty' => $row['qty'],
                'image' => !empty($row['image_path']) ? $row['image_path'] : 'images/default-crop.png',
                'farmer' => $row['farmer_name'],
                'category' => $row['category'] ?? 'Crop',
                'type' => 'crop'
            ];
        }

        // Chemicals
        $sql_chem = "SELECT c.quantity as qty, ch.id, ch.name, ch.price, ch.image_path, ch.category, u.fullname as seller_name 
                FROM cart c 
                JOIN chemicals ch ON c.chemical_id = ch.id 
                JOIN users u ON ch.seller_id = u.id 
                WHERE c.guest_token_id = ?";
        $stmt = $conn->prepare($sql_chem);
        $stmt->bind_param("s", $guest_token);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $items[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'qty' => $row['qty'],
                    'image' => !empty($row['image_path']) ? $row['image_path'] : 'images/default_chem.png',
                    'farmer' => $row['seller_name'],
                    'category' => $row['category'] ?? 'Chemical',
                    'type' => 'chemical'
                ];
            }
        }

        // Seeds (Guest)
        $sql_seeds = "SELECT c.quantity as qty, s.id, s.name, s.price, s.image_path, s.category, u.fullname as seller_name 
                FROM cart c 
                JOIN seeds s ON c.seed_id = s.id 
                JOIN users u ON s.seller_id = u.id 
                WHERE c.guest_token_id = ?";
        $stmt = $conn->prepare($sql_seeds);
        $stmt->bind_param("s", $guest_token);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $items[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'qty' => $row['qty'],
                    'image' => !empty($row['image_path']) ? $row['image_path'] : 'images/default-seed.png',
                    'farmer' => $row['seller_name'],
                    'category' => $row['category'] ?? 'Seed',
                    'type' => 'seed'
                ];
            }
        }
    }
}

if (empty($items)) {
    header("Location: shop_crops.php");
    exit();
}

// Calculate Subtotal
$subtotal = 0;
foreach ($items as $item)
    $subtotal += $item['price'] * $item['qty'];
$tax = $subtotal * 0.05;
$shipping = $subtotal > 1000 ? 0 : 50;
$total = $subtotal + $tax + $shipping;

$page_title = 'Secure Checkout | AgriAI';

// Extra CSS for Glassmorphism & Amazon Layout
$extra_css = "
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.3);
        --premium-green: #16a34a;
        --soft-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
    }

    body {
        background: radial-gradient(circle at top right, #f0fdf4, #ffffff);
    }

    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 2rem;
        margin-top: 2rem;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: var(--soft-shadow);
        padding: 2rem;
        margin-bottom: 2rem;
        transition: transform 0.3s ease;
    }

    .item-row {
        display: flex;
        gap: 1.5rem;
        padding: 1.5rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .item-row:last-child { border-bottom: none; }

    .item-img {
        width: 100px;
        height: 100px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .sticky-summary {
        position: sticky;
        top: 100px;
    }

    .trust-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        color: #64748b;
        background: rgba(0,0,0,0.03);
        padding: 4px 12px;
        border-radius: 50px;
        margin-right: 10px;
    }

    .ai-insight-chip {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 10px 15px;
        border-radius: 12px;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 1rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }

    .checkout-btn {
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: white;
        width: 100%;
        padding: 1.2rem;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 10px 20px rgba(22, 163, 74, 0.2);
    }

    .checkout-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(22, 163, 74, 0.3);
    }

    @media (max-width: 992px) {
        .checkout-grid { grid-template-columns: 1fr; }
        .sticky-summary { position: relative; top: 0; }
    }
</style>
";

include 'includes/main_header.php';
?>

<div class="container" style="padding: 2rem 0;">
    <!-- Breadcrumb & Trust Info -->
    <div
        style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom: 1rem;">
        <nav style="color: #64748b; font-size: 0.9rem;">
            <a href="index.php" style="color:inherit; text-decoration:none;">Home</a> <i class="fas fa-chevron-right"
                style="font-size:0.7rem;"></i>
            <a href="cart.php" style="color:inherit; text-decoration:none;">Cart</a> <i class="fas fa-chevron-right"
                style="font-size:0.7rem;"></i>
            <span style="color:var(--premium-green); font-weight:600;">Checkout</span>
        </nav>
        <div>
            <span class="trust-badge"><i class="fas fa-shield-alt"></i> SSL SECURE</span>
            <span class="trust-badge"><i class="fas fa-truck"></i> TRACKED DELIVERY</span>
            <?php if($user_id): ?>
                <a href="purchase_history.php" class="trust-badge" style="background: rgba(22, 163, 74, 0.1); color: var(--premium-green); text-decoration: none; transition: all 0.2s;">
                    <i class="fas fa-history"></i> My Activities
                </a>
            <?php endif; ?>
        </div>
    </div>

    <h1 style="font-weight: 800; margin-bottom: 2rem; color: #1e293b;">Review Your Order</h1>

    <div class="checkout-grid">
        <!-- Main Content -->
        <div class="main-checkout-content">

            <!-- Items Card -->
            <div class="glass-card">
                <h3 style="margin-bottom: 1.5rem; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-shopping-basket" style="color:var(--premium-green);"></i> Order Selection
                </h3>
                <?php foreach ($items as $item): ?>
                    <div class="item-row">
                        <img src="<?php echo $item['image']; ?>" class="item-img">
                        <div style="flex:1;">
                            <span
                                style="font-size: 0.7rem; color: #94a3b8; text-transform:uppercase; font-weight:700;"><?php echo $item['category']; ?></span>
                            <h4 style="margin: 2px 0 5px; color: #1e293b;"><?php echo htmlspecialchars($item['name']); ?>
                            </h4>
                            <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 10px;">
                                <i class="fas fa-store"></i> Sold by: <span
                                    style="color:var(--premium-green); font-weight:600;"><?php echo htmlspecialchars($item['farmer']); ?></span>
                            </p>
                            <div style="display:flex; align-items:center; gap:20px;">
                                <div style="font-size: 1.1rem; font-weight:700; color: #1e293b;">
                                    ₹<?php echo number_format($item['price'], 2); ?></div>
                                <div
                                    style="background: rgba(0,0,0,0.05); padding: 5px 15px; border-radius: 8px; font-size: 0.9rem;">
                                    Qty: <strong><?php echo $item['qty']; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if ($subtotal > 2000): ?>
                    <div class="ai-insight-chip">
                        <i class="fas fa-magic"></i>
                        <span><strong>AI Savings:</strong> You saved ₹150 with a bulk purchase discount!</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Shipping Card -->
            <div class="glass-card">
                <h3 style="margin-bottom: 1.5rem; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-map-marker-alt" style="color:var(--premium-green);"></i> Delivery Information
                </h3>
                <form id="checkoutForm" action="payment.php" method="POST">
                    <div class="row" style="display:flex; gap:1.5rem; flex-wrap:wrap;">
                        <div style="flex:1; min-width:250px;">
                            <label
                                style="display:block; font-size: 0.85rem; font-weight:600; color: #475569; margin-bottom: 8px;">Full
                                Name</label>
                            <input type="text" name="fullname" class="form-control-glass"
                                style="width:100%; padding: 0.8rem; border-radius: 10px; border: 1px solid #e2e8f0;"
                                value="<?php echo $user_data['fullname'] ?? ''; ?>" required>
                        </div>
                        <div style="flex:1; min-width:250px;">
                            <label
                                style="display:block; font-size: 0.85rem; font-weight:600; color: #475569; margin-bottom: 8px;">Phone
                                Number</label>
                            <input type="text" name="phone" class="form-control-glass"
                                style="width:100%; padding: 0.8rem; border-radius: 10px; border: 1px solid #e2e8f0;"
                                value="<?php echo $user_data['phone'] ?? ''; ?>" required>
                        </div>
                    </div>
                    <div style="margin-top: 1.5rem;">
                        <label
                            style="display:block; font-size: 0.85rem; font-weight:600; color: #475569; margin-bottom: 8px;">Shipping
                            Address</label>
                        <textarea name="address" class="form-control-glass" rows="3"
                            style="width:100%; padding: 0.8rem; border-radius: 10px; border: 1px solid #e2e8f0;"
                            required><?php echo $user_data['location'] ?? ''; ?></textarea>
                    </div>

                    <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
                    <?php if ($direct_buy): ?>
                        <input type="hidden" name="product_id" value="<?php echo $direct_product_id; ?>">
                        <input type="hidden" name="qty" value="<?php echo $direct_qty; ?>">
                        <input type="hidden" name="type" value="<?php echo isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'crop'; ?>">
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Sidebar Summary -->
        <div class="sidebar-summary">
            <div class="glass-card sticky-summary" style="padding: 1.5rem;">
                <h4
                    style="margin-bottom: 1.5rem; font-weight:800; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 0.5rem;">
                    Order Summary</h4>

                <div
                    style="display:flex; justify-content:space-between; margin-bottom: 12px; font-size: 0.95rem; color: #64748b;">
                    <span>Items (<?php echo count($items); ?>)</span>
                    <span>₹<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div
                    style="display:flex; justify-content:space-between; margin-bottom: 12px; font-size: 0.95rem; color: #64748b;">
                    <span>Shipping Fee</span>
                    <span><?php echo $shipping == 0 ? '<span style="color:#16a34a; font-weight:700;">FREE</span>' : '₹' . number_format($shipping, 2); ?></span>
                </div>
                <div
                    style="display:flex; justify-content:space-between; margin-bottom: 12px; font-size: 0.95rem; color: #64748b;">
                    <span>Taxes (5%)</span>
                    <span>₹<?php echo number_format($tax, 2); ?></span>
                </div>

                <div
                    style="margin: 1.5rem 0; border-top: 2px solid rgba(0,0,0,0.05); padding-top: 1rem; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-weight: 800; font-size: 1.2rem; color: #1e293b;">Grand Total</span>
                    <span
                        style="font-weight: 800; font-size: 1.5rem; color: var(--premium-green);">₹<?php echo number_format($total, 2); ?></span>
                </div>

                <button type="submit" form="checkoutForm" class="checkout-btn">
                    PROCEED TO PAYMENT <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
                </button>

                <div style="margin-top: 1.5rem; text-align:center;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg"
                        style="height:20px; opacity:0.6; margin: 0 10px;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg"
                        style="height:12px; opacity:0.6; margin: 0 10px;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg"
                        style="height:20px; opacity:0.6; margin: 0 10px;">
                </div>

                <div
                    style="background: #fdf2f2; color: #991b1b; padding: 10px; border-radius: 10px; margin-top: 1.5rem; font-size: 0.8rem; display:flex; gap:10px;">
                    <i class="fas fa-info-circle"></i>
                    <span>Orders placed before 2 PM will be processed same day.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Login/Guest Modal (Triggered for Public users on Final Step) -->
<!-- Login/Guest Modal (Triggered for Public users on Final Step) -->
<div id="authModal"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); backdrop-filter:blur(8px); z-index:20000; align-items:center; justify-content:center;">
    <div class="glass-card" style="width:90%; max-width:500px; text-align:center; padding: 2.5rem; max-height: 90vh; overflow-y: auto;">
        
        <div id="modal-header">
            <i class="fa-solid fa-seedling" style="font-size: 3rem; color:var(--premium-green); margin-bottom: 1rem;"></i>
            <h2 style="margin-bottom: 0.5rem;" id="auth-title">Welcome</h2>
            <p style="color:#64748b; margin-bottom: 1.5rem;" id="auth-subtitle">Enter your mobile number to continue</p>
        </div>

        <form id="phoneLoginForm" action="checkout_login.php" method="POST">
            
            <!-- Step 1: Phone -->
            <div id="step-phone">
                <div style="margin-bottom: 1rem; text-align: left;">
                    <label style="display:block; font-size: 0.85rem; font-weight:600; color: #475569; margin-bottom: 8px;">Mobile Number</label>
                    <input type="text" id="phoneInput" name="phone" placeholder="Enter Mobile Number" required pattern="[0-9]{10}" title="Please enter valid 10 digit mobile number"
                        style="width: 100%; padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 10px; font-size: 1rem; outline: none; background: #f8fafc;">
                </div>
                <!-- Hidden inputs for registration -->
                <input type="hidden" name="is_new_user" id="is_new_user" value="0">
                
                <!-- Hidden inputs for Direct Buy Preservation -->
                <?php if ($direct_buy): ?>
                    <input type="hidden" name="product_id" value="<?php echo $direct_product_id; ?>">
                    <input type="hidden" name="qty" value="<?php echo $direct_qty; ?>">
                    <input type="hidden" name="type" value="<?php echo isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'crop'; ?>">
                <?php endif; ?>
                
                <button type="button" onclick="checkPhone()" class="checkout-btn" id="checkPhoneBtn" style="box-shadow: none; background: #4f46e5;">
                    Continue <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
                </button>
            </div>

            <!-- Step 2: Login (OTP) - Existing User -->
            <div id="step-otp-login" style="display:none;">
                <div style="margin-bottom: 1rem; text-align: left;">
                     <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px;">
                        <label style="font-size: 0.85rem; font-weight:600; color: #475569;">Enter OTP</label>
                        <span class="displayPhone" style="font-size: 0.8rem; color: #64748b;">warning</span>
                     </div>
                    <input type="text" id="otpInput" name="otp" placeholder="Enter 4-digit OTP" maxlength="4" pattern="[0-9]{4}"
                        style="width: 100%; padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 10px; font-size: 1rem; outline: none; background: #f8fafc; letter-spacing: 5px; text-align: center;">
                    <p style="font-size:0.75rem; color:#64748b; margin-top:5px;">Use default OTP: <strong>1234</strong></p>
                </div>
                <button type="submit" class="checkout-btn" style="box-shadow: none; background: #16a34a;">
                    Verify & Login
                </button>
            </div>

            <!-- Step 2: Register - New User -->
            <div id="step-register" style="display:none; text-align: left;">
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; color: #166534;">
                    <i class="fas fa-user-plus"></i> New user detected. Please complete your profile.
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; font-size: 0.85rem; font-weight:600; color: #475569; margin-bottom: 5px;">Full Name</label>
                    <input type="text" name="fullname" id="regName" placeholder="Enter your name" 
                        style="width: 100%; padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 10px;">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; font-size: 0.85rem; font-weight:600; color: #475569; margin-bottom: 5px;">Email Address</label>
                    <input type="email" name="email" id="regEmail" placeholder="Enter email address" 
                        style="width: 100%; padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 10px;">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; font-size: 0.85rem; font-weight:600; color: #475569; margin-bottom: 5px;">Create Password</label>
                    <input type="password" name="password" id="regPass" placeholder="e.g. secret123" 
                        style="width: 100%; padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 10px;">
                </div>

                <!-- Registration also requires OTP for phone verification -->
                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; font-size: 0.85rem; font-weight:600; color: #475569; margin-bottom: 5px;">Verify Phone (OTP)</label>
                    <div style="display:flex; gap:10px;">
                        <input type="text" name="otp_reg" id="otpRegInput" placeholder="OTP" maxlength="4" style="width: 80px; padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 10px; text-align:center;">
                        <button type="button" onclick="sendOtp()" id="resendBtn" style="flex:1; background: #e0e7ff; color: #4338ca; border:none; border-radius:10px; font-weight:600;">Get OTP</button>
                    </div>
                </div>

                <button type="submit" class="checkout-btn" style="box-shadow: none; background: #16a34a;">
                    Create Account & Checkout
                </button>
            </div>
            
        </form>
        
        <button onclick="resetPhoneForm()" id="changeNumBtn" style="display:none; background:none; border:none; color:#4f46e5; font-size:0.8rem; margin-top:10px; cursor:pointer;">
            Change Number
        </button>

        <!-- Optional Cancel -->
        <button onclick="closeAuthModal()"
            style="background:none; border:none; color:#64748b; font-size: 0.9rem; cursor:pointer; margin-top:0.5rem;">Cancel</button>
    </div>
</div>

<script>
    const isGuest = <?php echo $user_id ? 'false' : 'true'; ?>;

    // Check for phone passed from cart
    const urlParams = new URLSearchParams(window.location.search);
    const passedPhone = urlParams.get('phone');

    document.getElementById('checkoutForm').addEventListener('submit', function (e) {
        if (isGuest) {
            e.preventDefault();
            document.getElementById('authModal').style.display = 'flex';
            if (passedPhone && document.getElementById('phoneInput').value === '') {
                 document.getElementById('phoneInput').value = passedPhone;
                 checkPhone(); // Auto-check if passed
            }
        }
    });

    if (isGuest && passedPhone) {
        document.getElementById('authModal').style.display = 'flex';
        document.getElementById('phoneInput').value = passedPhone;
    }

    function closeAuthModal() {
        document.getElementById('authModal').style.display = 'none';
    }

    function checkPhone() {
        const phone = document.getElementById('phoneInput').value;
        const btn = document.getElementById('checkPhoneBtn');

        if (phone.length !== 10) {
            alert('Please enter a valid 10-digit mobile number');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = 'Checking...';

        fetch('check_phone_ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'phone=' + encodeURIComponent(phone)
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Continue <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>';

            document.getElementById('step-phone').style.display = 'none';
            document.querySelectorAll('.displayPhone').forEach(el => el.innerText = phone);
            document.getElementById('changeNumBtn').style.display = 'inline-block';

            if (data.exists) {
                // Existing User -> Login (OTP)
                document.getElementById('auth-title').innerText = 'Welcome Back';
                document.getElementById('auth-subtitle').innerText = 'Enter OTP to login';
                document.getElementById('step-otp-login').style.display = 'block';
                document.getElementById('is_new_user').value = "0";
                
                // Trigger OTP send immediately
                sendOtp(); 
                document.getElementById('otpInput').focus();
            } else {
                // New User -> Register
                document.getElementById('auth-title').innerText = 'Create Account';
                document.getElementById('auth-subtitle').innerText = 'Complete details to finish order';
                document.getElementById('step-register').style.display = 'block';
                document.getElementById('is_new_user').value = "1";
                
                // Require manual OTP trigger? Or auto? Let's user click "Get OTP" or we can auto-trigger.
                // Better to let user fill form then click 'Get OTP' or auto-trigger. 
                // Let's auto trigger to save time? 
                // sendOtp(); // Maybe better wait. The button is there.
                
                // Add required attributes
                document.getElementById('regName').required = true;
                document.getElementById('regEmail').required = true;
                document.getElementById('regPass').required = true;
                document.getElementById('otpRegInput').required = true;
            }
        })
        .catch(error => {
            console.error(error);
            alert('Error checking phone. Try again.');
            btn.disabled = false;
            btn.innerHTML = 'Continue <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>';
        });
    }

    // OTP Logic
    function sendOtp() {
        const phone = document.getElementById('phoneInput').value;

        // Visual feedback on whatever button triggered it
        // Note: For existing user, checkPhone triggers it.
        
        fetch('send_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'phone=' + encodeURIComponent(phone)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('OTP Sent: ' + data.debug_otp); // Alert for demo
            } else {
                alert(data.message);
            }
        })
        .catch(error => alert('Failed to send OTP'));
    }

    function resetPhoneForm() {
        document.getElementById('step-phone').style.display = 'block';
        document.getElementById('step-otp-login').style.display = 'none';
        document.getElementById('step-register').style.display = 'none';
        document.getElementById('changeNumBtn').style.display = 'none';
        
        document.getElementById('auth-title').innerText = 'Welcome';
        document.getElementById('auth-subtitle').innerText = 'Enter your mobile number to continue';
        
        // Reset Inputs
        document.getElementById('otpInput').value = '';
        document.getElementById('otpRegInput').value = '';
        document.getElementById('regName').value = '';
        document.getElementById('regEmail').value = '';
        document.getElementById('regPass').value = '';
    }
</script>

<?php include 'includes/main_footer.php'; ?>
</html>