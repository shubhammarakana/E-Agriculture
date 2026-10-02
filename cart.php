<?php
// cart.php
include 'db_connect.php';
session_start();

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : 0;
include_once 'includes/cart_helper.php';
$guest_token = get_guest_token();
$page = 'cart';

// --- HANDLE POST ACTIONS (Update/Remove) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['remove'])) {
    
    // Helper logger (append to same log file)
    $logFile = 'cart_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    
    // 1. REMOVE ITEM
    if (isset($_GET['remove'])) {
        $cart_id = $_GET['remove']; // Can be string 'chemical_5' or int
        file_put_contents($logFile, "[$timestamp] Removing Item: $cart_id\n", FILE_APPEND);
        
        if ($is_logged_in) {
            // Ensure DB ID is int
            // NOTE: $cart_id from GET might be "chemical_5" string if coming from our cart view!
            // But verify_cart DB logic below (lines 61+) returns `c.id as cart_id` which IS int.
            // Wait, for GUESTS we use keys like 'chemical_5'. For LOGGED IN we use `c.id`.
            // Let's verify what we are passing.
            // If logged in, cart_id should be integer ID of cart table.
            
            $cart_id = intval($cart_id); // Cast to int for DB safety
            $del_sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($del_sql);
            $stmt->bind_param("ii", $cart_id, $user_id);
            if ($stmt->execute()) {
                 file_put_contents($logFile, "[$timestamp] DB Delete Success\n", FILE_APPEND);
            } else {
                 file_put_contents($logFile, "[$timestamp] DB Delete Fail: " . $stmt->error . "\n", FILE_APPEND);
            }
        } else {
            // Guest: delete from DB using token
            $cart_id = intval($cart_id);
            $del_sql = "DELETE FROM cart WHERE id = ? AND guest_token_id = ?";
            $stmt = $conn->prepare($del_sql);
            $stmt->bind_param("is", $cart_id, $guest_token);
            if ($stmt->execute()) {
                 file_put_contents($logFile, "[$timestamp] Guest DB Delete Success\n", FILE_APPEND);
            } else {
                 file_put_contents($logFile, "[$timestamp] Guest DB Delete Fail: " . $stmt->error . "\n", FILE_APPEND);
            }
        }
        header("Location: cart.php");
        exit();
    }

    // 2. UPDATE QUANTITY
    if (isset($_POST['update_qty'])) {
        $cart_id = $_POST['cart_id']; // Can be string
        $qty = intval($_POST['qty']);
        file_put_contents($logFile, "[$timestamp] Updating Item: $cart_id to Qty $qty\n", FILE_APPEND);

        if ($qty > 0) {
            if ($is_logged_in) {
                $cart_id = intval($cart_id);
                $upd_sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
                $stmt = $conn->prepare($upd_sql);
                $stmt->bind_param("iii", $qty, $cart_id, $user_id);
                if ($stmt->execute()) {
                     file_put_contents($logFile, "[$timestamp] DB Update Success\n", FILE_APPEND);
                } else {
                     file_put_contents($logFile, "[$timestamp] DB Update Fail: " . $stmt->error . "\n", FILE_APPEND);
                }
            } else {
                // Guest: update DB using token
                $cart_id = intval($cart_id);
                $upd_sql = "UPDATE cart SET quantity = ? WHERE id = ? AND guest_token_id = ?";
                $stmt = $conn->prepare($upd_sql);
                $stmt->bind_param("iis", $qty, $cart_id, $guest_token);
                if ($stmt->execute()) {
                     file_put_contents($logFile, "[$timestamp] Guest DB Update Success\n", FILE_APPEND);
                } else {
                     file_put_contents($logFile, "[$timestamp] Guest DB Update Fail: " . $stmt->error . "\n", FILE_APPEND);
                }
            }
        }
        header("Location: cart.php");
        exit();
    }
}

// --- FETCH CART ITEMS ---
$cart_items = []; // Unified array for display

// Log the fetch attempt
$logFile = 'cart_debug.log';
$timestamp = date('Y-m-d H:i:s');
$sess_id = session_id();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
file_put_contents($logFile, "[$timestamp] [Viewing Cart] UserID: $user_id, Role: $role, Session: $sess_id\n", FILE_APPEND);

if ($is_logged_in) {
    // Database Fetch
    file_put_contents($logFile, "[$timestamp] Check DB for User $user_id\n", FILE_APPEND);
    $sql = "SELECT c.id as cart_id, c.quantity, 
            COALESCE(cr.id, ch.id, s.id) as item_id,
            COALESCE(cr.name, ch.name, s.name) as name,
            COALESCE(cr.price, ch.price, s.price) as price,
            COALESCE(cr.image_path, ch.image_path, s.image_path) as image_path,
            u.fullname as seller_name,
            CASE 
                WHEN c.chemical_id IS NOT NULL THEN 'chemical' 
                WHEN c.seed_id IS NOT NULL THEN 'seed'
                ELSE 'crop' 
            END as item_type
            FROM cart c 
            LEFT JOIN crops cr ON c.crop_id = cr.id 
            LEFT JOIN chemicals ch ON c.chemical_id = ch.id
            LEFT JOIN seeds s ON c.seed_id = s.id
            LEFT JOIN users u ON (cr.farmer_id = u.id OR ch.seller_id = u.id OR s.seller_id = u.id)
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
} else {
    // Guest Fetch (DB Persistent)
    file_put_contents($logFile, "[$timestamp] Check DB for Guest Token $guest_token\n", FILE_APPEND);
    $sql = "SELECT c.id as cart_id, c.quantity, 
            COALESCE(cr.id, ch.id, s.id) as item_id,
            COALESCE(cr.name, ch.name, s.name) as name,
            COALESCE(cr.price, ch.price, s.price) as price,
            COALESCE(cr.image_path, ch.image_path, s.image_path) as image_path,
            u.fullname as seller_name,
            CASE 
                WHEN c.chemical_id IS NOT NULL THEN 'chemical' 
                WHEN c.seed_id IS NOT NULL THEN 'seed'
                ELSE 'crop' 
            END as item_type
            FROM cart c 
            LEFT JOIN crops cr ON c.crop_id = cr.id 
            LEFT JOIN chemicals ch ON c.chemical_id = ch.id
            LEFT JOIN seeds s ON c.seed_id = s.id
            LEFT JOIN users u ON (cr.farmer_id = u.id OR ch.seller_id = u.id OR s.seller_id = u.id)
            WHERE c.guest_token_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $guest_token);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
}

$total_price = 0;
// Log the result count
$count = count($cart_items);
file_put_contents($logFile, "[$timestamp] [Cart Loaded] Found $count items.\n", FILE_APPEND);
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/main_header.php'; ?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    <h2 style="margin-bottom: 2rem; color: #4338ca;">My Shopping Cart <?php echo !$is_logged_in ? '(Guest)' : ''; ?></h2>

    <?php if (!empty($cart_items)): ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Cart Items -->
            <div class="glass-panel" style="background: rgba(255, 255, 255, 0.6); border-radius: 12px; padding: 1.5rem;">
                <?php foreach ($cart_items as $row): 
                    $subtotal = $row['price'] * $row['quantity'];
                    $total_price += $subtotal;
                    
                    $def_img = 'images/default-crop.png';
                    if ($row['item_type'] == 'chemical') $def_img = 'images/default_chem.png';
                    if ($row['item_type'] == 'seed') $def_img = 'images/default_seed.png';

                    $img = !empty($row['image_path']) ? $row['image_path'] : $def_img;
                    // Sanitize path
                    if (strpos($img, 'images/') !== 0 && strpos($img, 'http') !== 0) {
                        $img = 'images/' . $img; // Fallback helper
                    }
                ?>
                    <div style="display: flex; gap: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 1.5rem; margin-bottom: 1.5rem; align-items: center;">
                        <img src="<?php echo $img; ?>" alt="<?php echo $row['name']; ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                        
                        <div style="flex-grow: 1;">
                            <h4 style="margin: 0; color: #1f2937;"><?php echo $row['name']; ?></h4>
                            <small style="color: #6b7280;">Seller: <?php echo $row['seller_name']; ?></small>
                            <div style="margin-top: 5px; color: #4f46e5; font-weight: 600;">₹<?php echo number_format($row['price'], 2); ?></div>
                        </div>

                        <form action="cart.php" method="POST" style="display: flex; align-items: center; gap: 10px;">
                            <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                            <input type="hidden" name="update_qty" value="1">
                            <input type="number" name="qty" value="<?php echo $row['quantity']; ?>" min="1" style="width: 60px; padding: 5px; border-radius: 5px; border: 1px solid #ccc;">
                            <button type="submit" style="background: none; border: none; color: #4f46e5; cursor: pointer;"><i class="fas fa-sync-alt"></i></button>
                        </form>

                        <div style="font-weight: 700; color: #1f2937; min-width: 80px; text-align: right;">
                            ₹<?php echo number_format($subtotal, 2); ?>
                        </div>

                        <a href="cart.php?remove=<?php echo $row['cart_id']; ?>" style="color: #ef4444; margin-left: 10px;"><i class="fas fa-trash"></i></a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary -->
            <div>
                <div class="glass-panel" style="background: rgba(255, 255, 255, 0.8); border-radius: 12px; padding: 1.5rem; position: sticky; top: 100px;">
                    <h3 style="margin-top: 0;">Order Summary</h3>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: #555;">
                        <span>Subtotal</span>
                        <span>₹<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <?php $tax = $total_price * 0.05; ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: #555;">
                        <span>Tax (5%)</span>
                        <span>₹<?php echo number_format($tax, 2); ?></span>
                    </div>
                    <hr style="border: 0; border-top: 1px solid #ddd; margin: 1rem 0;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 1.2rem; font-weight: 700;">
                        <span>Total</span>
                        <span>₹<?php echo number_format($total_price + $tax, 2); ?></span>
                    </div>
                    
                    <?php if ($is_logged_in): ?>
                        <a href="checkout.php" style="display: block; width: 100%; text-align: center; background: #4f46e5; color: white; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: 600;">Proceed to Checkout</a>
                    <?php else: ?>
                        <!-- Guest Checkout Section (Inline) -->
                        <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1rem;">
                                <i class="fas fa-user-clock" style="font-size: 1.25rem; color: #4f46e5;"></i>
                                <p style="font-weight: 700; color: #1f2937; margin: 0;">Guest Checkout</p>
                            </div>
                            
                            <form id="inlineAuthForm" action="checkout_login.php" method="POST">
                                <!-- Phone Input -->
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; font-size: 0.8rem; color: #6b7280; margin-bottom: 0.5rem;">Mobile Number</label>
                                    <div style="display: flex; gap: 10px;">
                                        <input type="text" id="cartPhoneInput" name="phone" placeholder="Enter number" pattern="[0-9]{10}" title="10 digit mobile number" required
                                            style="flex-grow: 1; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; outline: none; transition: border-color 0.2s;">
                                        <button type="button" id="chkBtn" onclick="checkInlinePhone()" 
                                            style="background: #e0e7ff; color: #4338ca; border: none; border-radius: 8px; padding: 0 15px; font-weight: 600; cursor: pointer;">
                                            Check
                                        </button>
                                        <button type="button" id="changeBtn" onclick="resetInlineForm()" style="display:none; background: #fee2e2; color: #991b1b; border: none; border-radius: 8px; padding: 0 15px; font-weight: 600; cursor: pointer;">
                                            Change
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="is_new_user" id="is_new_user" value="0">

                                <!-- Inline Loading -->
                                <div id="inlineLoading" style="display:none; text-align:center; color:#6b7280; margin-bottom:1rem;">
                                    <i class="fas fa-spinner fa-spin"></i> Checking...
                                </div>

                                <!-- Step: Password Login (Existing) -->
                                <div id="inline-login" style="display:none; margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                                    
                                    <div style="margin-bottom: 12px;">
                                        <label style="display: block; font-size: 0.8rem; color: #6b7280; margin-bottom: 0.5rem;">Username</label>
                                        <input type="text" name="login_fullname" id="loginName" 
                                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; color: #1f2937;">
                                    </div>
                                    <div style="margin-bottom: 12px;">
                                        <label style="display: block; font-size: 0.8rem; color: #6b7280; margin-bottom: 0.5rem;">Email ID</label>
                                        <input type="text" name="login_email" id="loginEmail" 
                                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; color: #1f2937;">
                                    </div>

                                    <div style="margin-bottom: 15px;">
                                        <label style="display: block; font-size: 0.8rem; color: #6b7280; margin-bottom: 0.5rem;">Password</label>
                                        <input type="password" name="login_password" placeholder="Enter Password"
                                            style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; outline: none;">
                                    </div>
                                    <button type="submit" style="width: 100%; background: #16a34a; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                                        Login <i class="fas fa-sign-in-alt" style="margin-left: 5px;"></i>
                                    </button>
                                </div>

                                <!-- Step: Register (New User) -->
                                <div id="inline-register" style="display:none; margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 8px; border-radius: 6px; margin-bottom: 12px; font-size: 0.85rem; color: #166534;">
                                        <i class="fas fa-user-plus"></i> New User? Fill details below.
                                    </div>

                                    <div style="margin-bottom: 12px;">
                                        <label style="display: block; font-size: 0.8rem; color: #6b7280; margin-bottom: 0.5rem;">Username</label>
                                        <input type="text" name="fullname" id="regName" placeholder="Enter Username"
                                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
                                    </div>
                                    <div style="margin-bottom: 12px;">
                                        <label style="display: block; font-size: 0.8rem; color: #6b7280; margin-bottom: 0.5rem;">Email ID</label>
                                        <input type="email" name="email" id="regEmail" placeholder="Enter Email ID"
                                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
                                    </div>
                                    <div style="margin-bottom: 12px;">
                                        <label style="display: block; font-size: 0.8rem; color: #6b7280; margin-bottom: 0.5rem;">Password</label>
                                        <input type="password" name="password" id="regPass" placeholder="Enter Password"
                                            style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
                                    </div>
                                    
                                    <button type="submit" style="width: 100%; background: #4f46e5; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                                        Login <i class="fas fa-user-check" style="margin-left: 5px;"></i>
                                    </button>
                                </div>

                            </form>
                        </div>
                    <?php endif; ?>

                    <script>
                        function checkInlinePhone() {
                             const phoneInput = document.getElementById('cartPhoneInput');
                             const phone = phoneInput.value;
                             
                             if (phone.length !== 10) {
                                 alert('Please enter a valid 10-digit mobile number');
                                 return;
                             }

                             // UI Updates
                             document.getElementById('chkBtn').style.display = 'none';
                             document.getElementById('inlineLoading').style.display = 'block';
                             phoneInput.readOnly = true;
                             phoneInput.style.backgroundColor = '#f3f4f6';

                             checkCartPhone(phone);
                        }

                        function resetInlineForm() {
                             document.getElementById('inline-login').style.display = 'none';
                             document.getElementById('inline-register').style.display = 'none';
                             document.getElementById('chkBtn').style.display = 'block';
                             document.getElementById('changeBtn').style.display = 'none';
                             
                             const phoneInput = document.getElementById('cartPhoneInput');
                             phoneInput.readOnly = false;
                             phoneInput.style.backgroundColor = 'white';
                             phoneInput.focus();
                             
                             // Clear values
                             document.getElementById('loginName').value = '';
                             document.getElementById('loginEmail').value = '';
                        }

                        function checkCartPhone(phone) {
                            fetch('check_phone_ajax.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: 'phone=' + encodeURIComponent(phone)
                            })
                            .then(response => response.json())
                            .then(data => {
                                document.getElementById('inlineLoading').style.display = 'none';
                                document.getElementById('changeBtn').style.display = 'block';

                                if (data.exists) {
                                    // Existing -> Password Login WITH DETAILS
                                    document.getElementById('inline-login').style.display = 'block';
                                    document.getElementById('is_new_user').value = "0";
                                    
                                    // Populate details
                                    document.getElementById('loginName').value = data.fullname || 'User';
                                    document.getElementById('loginEmail').value = data.email || 'N/A';
                                } else {
                                    // New -> Register
                                    document.getElementById('inline-register').style.display = 'block';
                                    document.getElementById('is_new_user').value = "1";
                                    
                                     document.getElementById('regName').required = true;
                                     document.getElementById('regEmail').required = true;
                                     document.getElementById('regPass').required = true;
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert('Error checking status');
                                resetInlineForm(); // Fallback
                            });
                        }
                    </script>

                    <a href="shop_crops.php" style="display: block; width: 100%; text-align: center; margin-top: 10px; color: #6b7280; text-decoration: none;">Continue Shopping</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 3rem; background: rgba(255,255,255,0.5); border-radius: 12px;">
            <i class="fas fa-shopping-basket" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
            <h3>Your cart is empty</h3>
            <p>Looks like you haven't added anything to your cart yet.</p>
            <a href="shop_crops.php" class="btn" style="background: #16a34a; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block; margin-top: 1rem;">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/main_footer.php'; ?>
</html>
