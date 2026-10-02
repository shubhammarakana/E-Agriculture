<?php
// order_details.php
include '../db_connect.php';
session_start();

// Check if user is logged in and is a farmer or admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['farmer', 'admin'])) {
    header("Location: ../login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];
$order_id = $_GET['id'];

// Fetch Order Info
// Fetch Order Info
$sql_cond = "";
if ($_SESSION['role'] != 'admin') {
    $sql_cond = "AND o.farmer_id='$farmer_id'";
}

$sql_order = "SELECT o.*, u.fullname, u.email, u.phone as user_phone, u.location as user_location 
              FROM orders o 
              LEFT JOIN users u ON o.buyer_id = u.id 
              WHERE o.id='$order_id' $sql_cond";
$res_order = $conn->query($sql_order);

if ($res_order->num_rows == 0) {
    die("Order not found or access denied.");
}

$order = $res_order->fetch_assoc();

// Fetch Order Items
// Fetch Order Items
// Fetch Order Items
$sql_items = "SELECT oi.*, 
              COALESCE(c.name, ch.name, s.name) as name, 
              COALESCE(c.image_path, ch.image_path, s.image_path) as image_path,
              ch.id as chemical_id,
              s.id as seed_id
              FROM order_items oi 
              LEFT JOIN crops c ON oi.crop_id = c.id 
              LEFT JOIN chemicals ch ON oi.chemical_id = ch.id
              LEFT JOIN seeds s ON oi.seed_id = s.id
              WHERE oi.order_id='$order_id'";
$res_items = $conn->query($sql_items);

$page = 'orders';
$page_title = 'Order Details #' . $order_id;
?>
<!DOCTYPE html>
<html lang="en">
<?php
$page_title = 'Order #' . $order_id;
include '../includes/main_header.php';
?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">

    <!-- Toast Notification Container -->
    <div id="toast" class="glass-toast">
        <i class="fas fa-check-circle"></i>
        <span id="toast-msg">Action Successful</span>
    </div>

    <div class="glass-panel"
        style="background: linear-gradient(120deg, rgba(255,255,255,0.4), rgba(255,255,255,0.1)); border:1px solid rgba(255,255,255,0.6);">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 style="margin:0; color:#2E7D32;">Order #<?php echo $order_id; ?></h2>
                <p style="margin:5px 0 0; color:#555;">Placed on
                    <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?>
                </p>
            </div>
            <div class="status-badge <?php echo ($order['status'] == 'Pending') ? 'status-pending' : (($order['status'] == 'Cancelled') ? 'status-rejected' : 'status-active'); ?>"
                style="font-size:1rem; padding: 8px 15px;">
                <?php echo $order['status']; ?>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 2rem; flex-wrap: wrap; margin-top: 2rem;">

        <!-- Items Column -->
        <div style="flex: 2; min-width: 300px;">
            <div class="glass-panel">
                <h3 style="border-bottom:1px solid rgba(0,0,0,0.05); padding-bottom:10px; margin-bottom:15px;"><i
                        class="fas fa-box-open"></i> Order Items</h3>
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $res_items->fetch_assoc()):
                            // Determine Default
                            if (!empty($item['chemical_id'])) {
                                $default_img_relative = 'images/default_chem.png';
                            } elseif (!empty($item['seed_id'])) {
                                $default_img_relative = 'images/default-seed.png';
                            } else {
                                $default_img_relative = 'images/default-crop.png';
                            }
                            
                            // Adjust for Admin Path (needs ../)
                            $default_img = '../' . $default_img_relative;

                            $raw_img = $item['image_path'];
                            $img_src = $default_img;

                            if (!empty($raw_img)) {
                                // Clean path from DB (strip existing ../ to normalize)
                                $clean_path = str_replace('../', '', $raw_img);
                                
                                // Check for external URL
                                if (strpos($clean_path, 'http') === 0) {
                                    $img_src = $clean_path;
                                } else {
                                    // Local file
                                    if (strpos($clean_path, 'images/') !== 0) {
                                        $clean_path = 'images/' . $clean_path;
                                    }
                                    // Prepend ../ for admin view
                                    $admin_path = '../' . $clean_path;
                                    
                                    // Check if file exists (relative to current script)
                                    // file_exists works on file system paths. 
                                    // __DIR__ is C:\...\admin. So ../images is C:\...\images.
                                    if (file_exists(__DIR__ . '/../' . $clean_path)) {
                                        $img_src = $admin_path;
                                    }
                                }
                            }
                        ?>
                            <tr>
                                <td style="display:flex; align-items:center; gap:15px;">
                                    <img src="<?php echo htmlspecialchars($img_src); ?>" onerror="this.src='<?php echo $default_img; ?>'"
                                        style="width:50px; height:50px; border-radius:10px; object-fit:cover; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
                                    <div>
                                        <div style="font-weight:600; color:#333;"><?php echo $item['name']; ?></div>
                                    </div>
                                </td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['price_per_unit'], 2); ?></td>
                                <td style="font-weight:bold; color:#2E7D32;">
                                    $<?php echo number_format($item['subtotal'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div
                    style="display:flex; justify-content:flex-end; margin-top: 20px; padding-top:20px; border-top:1px dashed rgba(0,0,0,0.1);">
                    <div style="text-align: right;">
                        <span style="display:block; color:#666; font-size:0.9rem;">Total Amount</span>
                        <span
                            style="font-size: 1.8rem; font-weight: 800; color: #2E7D32;">$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info Column -->
        <div style="flex: 1; min-width: 250px; display:flex; flex-direction:column; gap:20px;">

            <!-- Status Update Card -->
            <div class="glass-panel" style="border-left: 4px solid var(--primary);">
                <h3 style="margin-top:0;"><i class="fas fa-tasks"></i> Update Status</h3>
                <form action="update_order_status.php" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    <div class="form-group-dashboard">
                        <label style="font-size:0.9rem; color:#666; margin-bottom:8px; display:block;">Current
                            Status</label>
                        <select name="status" class="form-control-glass" style="font-weight:600; color:#333;">
                            <option value="Pending" <?php if ($order['status'] == 'Pending')
                                echo 'selected'; ?>>Pending
                            </option>
                            <option value="Accepted" <?php if ($order['status'] == 'Accepted')
                                echo 'selected'; ?>>Accepted
                            </option>
                            <option value="Processing" <?php if ($order['status'] == 'Processing')
                                echo 'selected'; ?>>
                                Processing</option>
                            <option value="Packed" <?php if ($order['status'] == 'Packed')
                                echo 'selected'; ?>>Packed
                            </option>
                            <option value="Shipped" <?php if ($order['status'] == 'Shipped')
                                echo 'selected'; ?>>Shipped
                            </option>
                            <option value="Delivered" <?php if ($order['status'] == 'Delivered')
                                echo 'selected'; ?>>
                                Delivered</option>
                            <option value="Cancelled" <?php if ($order['status'] == 'Cancelled')
                                echo 'selected'; ?>>
                                Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary-glass"
                        style="width:100%; display:flex; justify-content:center; align-items:center; gap:8px;">
                        <i class="fas fa-save"></i> Save Update
                    </button>
                </form>
            </div>

            <!-- Buyer Info Card -->
            <div class="glass-panel">
                <h3 style="margin-top:0;"><i class="fas fa-user-circle"></i> Buyer Info</h3>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div
                            style="width:35px; height:35px; background:rgba(76,175,80,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--primary);">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <small style="color:#777; display:block; line-height:1;">Name</small>
                            <span style="font-weight:500;"><?php echo $order['fullname']; ?></span>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; gap:10px;">
                        <div
                            style="width:35px; height:35px; background:rgba(33,150,243,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#2196F3;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div style="overflow:hidden; text-overflow:ellipsis;">
                            <small style="color:#777; display:block; line-height:1;">Email</small>
                            <span style="font-weight:500; font-size:0.9rem;"><?php echo $order['email']; ?></span>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; gap:10px;">
                        <div
                            style="width:35px; height:35px; background:rgba(255,152,0,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#FF9800;">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <small style="color:#777; display:block; line-height:1;">Phone</small>
                            <span style="font-weight:500;"><?php echo !empty($order['buyer_phone']) ? $order['buyer_phone'] : $order['user_phone']; ?></span>
                        </div>
                    </div>

                    <div
                        style="display:flex; flex-direction:column; gap:5px; margin-top:5px; background:rgba(0,0,0,0.02); padding:10px; border-radius:8px;">
                        <small style="color:#777;"><i class="fas fa-map-marker-alt"></i> Delivery Address</small>
                        <span style="font-size:0.9rem; line-height:1.4;"><?php echo !empty($order['buyer_address']) ? $order['buyer_address'] : $order['user_location']; ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
</div>
<script>
    // Check for URL parameters for Toast
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');

    if (msg) {
        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toast-msg');

        toastMsg.innerText = msg;
        toast.classList.add('show');
        toast.classList.add('success'); // Default to success for now

        if (msg.toLowerCase().includes('error')) {
            toast.classList.remove('success');
            toast.classList.add('error');
            toast.querySelector('i').className = 'fas fa-exclamation-circle';
        }

        setTimeout(() => {
            toast.classList.remove('show');
            // Clean URL
            const url = new URL(window.location);
            url.searchParams.delete('msg');
            window.history.replaceState({}, '', url);
        }, 4000);
    }
</script>
<?php include '../includes/main_footer.php'; ?>

</html>