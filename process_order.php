<?php
// process_order.php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: shop_crops.php");
    exit();
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$buyer_name = $_POST['fullname'] ?? '';
$buyer_address = $_POST['address'] ?? '';
$buyer_phone = $_POST['phone'] ?? '';
$total_amount = $_POST['total_amount'];
$payment_method = $_POST['payment_method'];

// Start Transaction
$conn->begin_transaction();

try {
    // 1. Create Order
    $cart_items = [];
    $farmer_id = null;

    if (isset($_POST['product_id'])) {
        // --- DIRECT BUY MODE ---
        $pid = $conn->real_escape_string($_POST['product_id']);
        $qty = $conn->real_escape_string($_POST['qty']);
        
        $found = false;

        // 1. Try Crops
        $sql_prod = "SELECT * FROM crops WHERE id='$pid'";
        $res = $conn->query($sql_prod);
        if ($res->num_rows > 0) {
            $prod = $res->fetch_assoc();
            $farmer_id = $prod['farmer_id'];
            $cart_items[] = [
                'crop_id' => $prod['id'],
                'chemical_id' => null,
                'seed_id' => null,
                'quantity' => $qty,
                'price' => $prod['price'],
                'farmer_id' => $prod['farmer_id']
            ];
            $found = true;
        }

        // 2. Try Chemicals
        if (!$found) {
            $sql_chem = "SELECT * FROM chemicals WHERE id='$pid'";
            $res_c = $conn->query($sql_chem);
            if ($res_c->num_rows > 0) {
                $prod = $res_c->fetch_assoc();
                $farmer_id = $prod['seller_id'];
                $cart_items[] = [
                    'crop_id' => null,
                    'chemical_id' => $prod['id'],
                    'seed_id' => null,
                    'quantity' => $qty,
                    'price' => $prod['price'],
                    'farmer_id' => $prod['seller_id']
                ];
                $found = true;
            }
        }

        // 3. Try Seeds
        if (!$found) {
            $sql_seed = "SELECT * FROM seeds WHERE id='$pid'";
            $res_s = $conn->query($sql_seed);
            if ($res_s->num_rows > 0) {
                $prod = $res_s->fetch_assoc();
                $farmer_id = $prod['seller_id'];
                $cart_items[] = [
                    'crop_id' => null,
                    'chemical_id' => null,
                    'seed_id' => $prod['id'],
                    'quantity' => $qty,
                    'price' => $prod['price'],
                    'farmer_id' => $prod['seller_id']
                ];
                $found = true;
            }
        }
    } else {
        // --- CART MODE ---
        if (!$user_id) {
            // Check for guest token if not logged in (Assuming guest logic handled by session/cookie, but here strict check)
            // Actually, for now, let's stick to logged in required for CART MODE in this specific file if that was the logic.
            // But wait, guests can have carts.
             if (!isset($_SESSION['guest_token']) && !$user_id) {
                 throw new Exception("Session expired. Please try again.");
             }
             $identifier_field = $user_id ? "user_id='$user_id'" : "guest_token_id='{$_SESSION['guest_token']}'";
        } else {
            $identifier_field = "user_id='$user_id'";
        }
        
        // Fetch Crops
        $sql_cart_crops = "SELECT c.*, cr.price, cr.farmer_id 
                     FROM cart c JOIN crops cr ON c.crop_id = cr.id 
                     WHERE c.$identifier_field";
        $cart_res_crops = $conn->query($sql_cart_crops);
        if ($cart_res_crops) {
            while ($row = $cart_res_crops->fetch_assoc()) {
                $row['chemical_id'] = null; $row['seed_id'] = null;
                $cart_items[] = $row;
                if ($farmer_id === null) $farmer_id = $row['farmer_id'];
            }
        }
        
        // Fetch Chemicals
        $sql_cart_chem = "SELECT c.*, ch.price, ch.seller_id as farmer_id 
                     FROM cart c JOIN chemicals ch ON c.chemical_id = ch.id 
                     WHERE c.$identifier_field";
        $cart_res_chem = $conn->query($sql_cart_chem);
        if ($cart_res_chem) {
            while ($row = $cart_res_chem->fetch_assoc()) {
                $row['crop_id'] = null; $row['seed_id'] = null;
                $cart_items[] = $row;
                if ($farmer_id === null) $farmer_id = $row['farmer_id'];
            }
        }

        // Fetch Seeds
        $sql_cart_seeds = "SELECT c.*, s.price, s.seller_id as farmer_id 
                     FROM cart c JOIN seeds s ON c.seed_id = s.id 
                     WHERE c.$identifier_field";
        $cart_res_seeds = $conn->query($sql_cart_seeds);
        if ($cart_res_seeds) {
            while ($row = $cart_res_seeds->fetch_assoc()) {
                $row['crop_id'] = null; $row['chemical_id'] = null;
                $cart_items[] = $row;
                if ($farmer_id === null) $farmer_id = $row['farmer_id'];
            }
        }
    }

    if (empty($cart_items)) {
        throw new Exception("No items to process.");
    }

    // Fallback if no farmer_id found (should not happen if DB is correct)
    if (!$farmer_id) {
        $f_res = $conn->query("SELECT id FROM users WHERE role='farmer' LIMIT 1");
        if ($f_res->num_rows > 0)
            $farmer_id = $f_res->fetch_assoc()['id'];
        else
            throw new Exception("No valid farmer found to assign this order to.");
    }

    // Use buyer_id if logged in, otherwise use NULL
    $bid_val = $user_id ? "'$user_id'" : "NULL";
    $sql_order = "INSERT INTO orders (buyer_id, buyer_name, buyer_address, buyer_phone, farmer_id, total_amount, status) 
                  VALUES ($bid_val, '$buyer_name', '$buyer_address', '$buyer_phone', '$farmer_id', '$total_amount', 'Pending')";
    $conn->query($sql_order);
    if ($conn->error) throw new Exception("Order creation failed: " . $conn->error);
    $order_id = $conn->insert_id;

    // Track guest orders in session
    if (!$user_id) {
        if (!isset($_SESSION['guest_orders'])) {
            $_SESSION['guest_orders'] = [];
        }
        $_SESSION['guest_orders'][] = $order_id;
    }

    // 2. Insert Order Items (Updated for Seed ID)
    foreach ($cart_items as $item) {
        $subtotal = $item['quantity'] * $item['price'];
        
        $crop_val = !empty($item['crop_id']) ? "'{$item['crop_id']}'" : "NULL";
        $chem_val = !empty($item['chemical_id']) ? "'{$item['chemical_id']}'" : "NULL";
        $seed_val = !empty($item['seed_id']) ? "'{$item['seed_id']}'" : "NULL";

        $conn->query("INSERT INTO order_items (order_id, crop_id, chemical_id, seed_id, quantity, price_per_unit, subtotal) 
                      VALUES ('$order_id', $crop_val, $chem_val, $seed_val, '{$item['quantity']}', '{$item['price']}', '$subtotal')");
        if ($conn->error) throw new Exception("Item insertion failed: " . $conn->error);
    }

    // 4. Record Payment
    $conn->query("INSERT INTO payments (order_id, user_id, amount, payment_method, status, transaction_id) 
                  VALUES ('$order_id', " . ($user_id ? "'$user_id'" : "NULL") . ", '$total_amount', '$payment_method', 'Completed', 'TXN" . uniqid() . "')");

    // 5. Empty Cart
    if (!isset($_POST['product_id'])) {
         if ($user_id) {
            $conn->query("DELETE FROM cart WHERE user_id='$user_id'");
         } elseif (isset($_SESSION['guest_token'])) {
             $conn->query("DELETE FROM cart WHERE guest_token_id='{$_SESSION['guest_token']}'");
         }
    }

    $conn->commit();
    header("Location: order_confirmation.php?id=$order_id");

} catch (Exception $e) {
    $conn->rollback();
    die("Order processing failed: " . $e->getMessage());
}
?>