<?php
// vendor/order_details.php
include '../db_connect.php';
session_start();

// Check if user is logged in and is a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

$vendor_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch Order Info
// Strictly enforce vendor ownership
$sql_order = "SELECT o.*, u.fullname, u.email, u.phone as user_phone, u.location as user_location 
              FROM orders o 
              LEFT JOIN users u ON o.buyer_id = u.id 
              WHERE o.id='$order_id' AND o.farmer_id='$vendor_id'";
$res_order = $conn->query($sql_order);

if ($res_order->num_rows == 0) {
    die("Order not found or access denied.");
}

$order = $res_order->fetch_assoc();

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
<?php include '../includes/main_header.php'; ?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">

    <!-- Toast Notification Container -->
    <div id="toast" class="glass-toast">
        <i class="fas fa-check-circle"></i>
        <span id="toast-msg">Action Successful</span>
    </div>

    <!-- Header Card -->
    <div class="glass-panel" style="background: linear-gradient(120deg, rgba(255,255,255,0.4), rgba(255,255,255,0.1)); border:1px solid rgba(255,255,255,0.6); display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h2 style="margin:0; color:#1e293b;">Order #<?php echo $order_id; ?></h2>
            <p style="margin:5px 0 0; color:#64748b;">Placed on <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
        </div>
        <div class="status-badge <?php echo ($order['status'] == 'Pending') ? 'status-pending' : (($order['status'] == 'Cancelled') ? 'status-rejected' : 'status-active'); ?>" style="font-size:1rem; padding: 8px 15px;">
            <?php echo $order['status']; ?>
        </div>
    </div>

    <div style="display: flex; gap: 2rem; flex-wrap: wrap; margin-top: 2rem;">

        <!-- Items Column -->
        <div style="flex: 2; min-width: 300px;">
            <div class="glass-panel">
                <h3 style="border-bottom:1px solid rgba(0,0,0,0.05); padding-bottom:10px; margin-bottom:15px; color: #374151;">
                    <i class="fas fa-box-open" style="color: #6366f1;"></i> Order Items
                </h3>
                <table class="glass-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="padding: 10px; text-align: left; color:#64748b;">Product</th>
                            <th style="padding: 10px; color:#64748b;">Qty</th>
                            <th style="padding: 10px; color:#64748b;">Price</th>
                            <th style="padding: 10px; color:#64748b;">Total</th>
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
                            
                            // Adjust for Vendor Path (needs ../)
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
                                    // Prepend ../ for vendor view
                                    $vendor_path = '../' . $clean_path;
                                    
                                    // Check if file exists (relative to current script)
                                    if (file_exists(__DIR__ . '/../' . $clean_path)) {
                                        $img_src = $vendor_path;
                                    }
                                }
                            }
                        ?>
                            <tr>
                                <td style="padding: 12px 10px; display:flex; align-items:center; gap:15px;">
                                    <img src="<?php echo htmlspecialchars($img_src); ?>" onerror="this.src='<?php echo $default_img; ?>'" style="width:50px; height:50px; border-radius:10px; object-fit:cover; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
                                    <div style="font-weight:600; color:#333; font-size: 0.95rem;"><?php echo $item['name']; ?></div>
                                </td>
                                <td style="padding: 10px; text-align: center; color: #4b5563;"><?php echo $item['quantity']; ?></td>
                                <td style="padding: 10px; text-align: center; color: #4b5563;">₹<?php echo number_format($item['price_per_unit'], 2); ?></td>
                                <td style="padding: 10px; text-align: center; font-weight:bold; color:#16a34a;">₹<?php echo number_format($item['subtotal'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div style="display:flex; justify-content:flex-end; margin-top: 20px; padding-top:20px; border-top:1px dashed rgba(0,0,0,0.1);">
                    <div style="text-align: right;">
                        <span style="display:block; color:#64748b; font-size:0.9rem;">Total Amount</span>
                        <span style="font-size: 1.8rem; font-weight: 800; color: #16a34a;">₹<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info Column -->
        <div style="flex: 1; min-width: 280px; display:flex; flex-direction:column; gap:20px;">

            <!-- Status Update Card -->
            <div class="glass-panel" style="border-left: 4px solid #6366f1;">
                <h3 style="margin-top:0; color: #374151;"><i class="fas fa-tasks"></i> Update Status</h3>
                <form action="update_order_status.php" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    <div class="form-group-dashboard">
                        <label style="font-size:0.9rem; color:#64748b; margin-bottom:8px; display:block;">Current Status</label>
                        <div class="input-group-glass">
                            <i class="fas fa-clipboard-check"></i>
                            <select name="status" required>
                                <?php 
                                $statuses = ['Pending', 'Accepted', 'Processing', 'Packed', 'Shipped', 'Delivered', 'Cancelled'];
                                foreach($statuses as $st) {
                                    $sel = ($order['status'] == $st) ? 'selected' : '';
                                    echo "<option value='$st' $sel>$st</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit-glass" style="margin-top: 15px; padding: 12px; font-size: 1rem;">
                        <i class="fas fa-save"></i> Save Update
                    </button>
                </form>
            </div>

            <!-- Buyer Info Card -->
            <div class="glass-panel">
                <h3 style="margin-top:0; color: #374151;"><i class="fas fa-user-circle"></i> Buyer Info</h3>
                <div style="display:flex; flex-direction:column; gap:15px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:35px; height:35px; background:rgba(99, 102, 241, 0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#6366f1;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <small style="color:#64748b; display:block; line-height:1;">Name</small>
                            <span style="font-weight:500; color: #334155;"><?php echo $order['fullname']; ?></span>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:35px; height:35px; background:rgba(37, 99, 235, 0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#2563eb;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div style="overflow:hidden; text-overflow:ellipsis;">
                            <small style="color:#64748b; display:block; line-height:1;">Email</small>
                            <span style="font-weight:500; font-size:0.9rem; color: #334155;"><?php echo $order['email']; ?></span>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:35px; height:35px; background:rgba(234, 179, 8, 0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#eab308;">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <small style="color:#64748b; display:block; line-height:1;">Phone</small>
                            <span style="font-weight:500; color: #334155;"><?php echo !empty($order['buyer_phone']) ? $order['buyer_phone'] : $order['user_phone']; ?></span>
                        </div>
                    </div>

                    <div style="background:rgba(241, 245, 249, 0.6); padding:12px; border-radius:10px; margin-top:5px;">
                        <small style="color:#64748b; display: flex; align-items: center; gap: 5px; margin-bottom: 5px;">
                            <i class="fas fa-map-marker-alt"></i> Delivery Address
                        </small>
                        <span style="font-size:0.95rem; line-height:1.4; color: #334155;"><?php echo !empty($order['buyer_address']) ? $order['buyer_address'] : $order['user_location']; ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Reuse consistent styles */
    .status-badge { display: inline-block; border-radius: 99px; font-weight: 600; }
    .status-pending { background: #fef9c3; color: #854d0e; }
    .status-active { background: #dcfce7; color: #166534; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    
    .input-group-glass {
        display: flex; align-items: center; background: rgba(255, 255, 255, 0.6); border: 1px solid #e2e8f0; border-radius: 10px; padding: 5px 15px; 
    }
    .input-group-glass select { border: none; background: transparent; width: 100%; padding: 10px 0; outline: none; font-size: 1rem; color: #334155; }
    .btn-submit-glass { width: 100%; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: white; border: none; border-radius: 12px; cursor: pointer; transition: all 0.3s; font-weight: 600; }
    .btn-submit-glass:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3); }
    
    /* Toast */
    .glass-toast {
        position: fixed; top: 90px; right: 20px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); padding: 15px 25px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-left: 5px solid #16a34a; display: flex; align-items: center; gap: 15px; transform: translateX(150%); transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 2000;
    }
    .glass-toast.show { transform: translateX(0); }
    .glass-toast i { font-size: 1.2rem; color: #16a34a; }
</style>

<script>
    // Check for URL parameters for Toast
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');

    if (msg) {
        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toast-msg');

        toastMsg.innerText = msg;
        toast.classList.add('show');

        if (msg.toLowerCase().includes('error')) {
            toast.style.borderLeftColor = '#ef4444';
            toast.querySelector('i').className = 'fas fa-exclamation-circle';
            toast.querySelector('i').style.color = '#ef4444';
        }

        setTimeout(() => {
            toast.classList.remove('show');
            const url = new URL(window.location);
            url.searchParams.delete('msg');
            window.history.replaceState({}, '', url);
        }, 4000);
    }
</script>
<?php include '../includes/main_footer.php'; ?>
</html>
