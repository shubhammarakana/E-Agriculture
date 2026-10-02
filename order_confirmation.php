<?php
// order_confirmation.php - PUBLIC REDESIGN
include 'db_connect.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $conn->real_escape_string($_GET['id']);

// Fetch Order Base Info with Payment Details
$sql_order = "SELECT o.*, u.fullname as farmer_name, u.phone as farmer_phone, p.payment_method, p.transaction_id
              FROM orders o 
              LEFT JOIN users u ON o.farmer_id = u.id 
              LEFT JOIN payments p ON o.id = p.order_id
              WHERE o.id = '$order_id'
              LIMIT 1";
$order_res = $conn->query($sql_order);
$order = $order_res->fetch_assoc();

if (!$order) {
    header("Location: index.php");
    exit();
}

// Fetch Order Items Items (Support Crops, Chemicals, Seeds)
$sql_items = "SELECT oi.*, 
                     COALESCE(c.name, ch.name, s.name) as item_name, 
                     COALESCE(c.image_path, ch.image_path, s.image_path) as image_path, 
                     COALESCE(c.category, ch.category, s.category) as category,
                     c.id as crop_id,
                     ch.id as chemical_id,
                     s.id as seed_id
              FROM order_items oi 
              LEFT JOIN crops c ON oi.crop_id = c.id 
              LEFT JOIN chemicals ch ON oi.chemical_id = ch.id
              LEFT JOIN seeds s ON oi.seed_id = s.id
              WHERE oi.order_id = '$order_id'";
$items_res = $conn->query($sql_items);

$page_title = 'Order Confirmed | AgriAI';

// Premium Styles
$extra_css = "
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.75);
        --glass-border: rgba(255, 255, 255, 0.4);
        --premium-green: #16a34a;
        --soft-amber: #fffbeb;
    }

    body {
        background: radial-gradient(circle at top, #f0fdf4, #fff7ed, #ffffff);
    }

    .conf-container {
        max-width: 900px;
        margin: 3rem auto;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        padding: 3rem;
        margin-bottom: 2rem;
    }

    .success-icon {
        width: 80px;
        height: 80px;
        background: #dcfce7;
        color: #166534;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 1.5rem;
        animation: scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes scaleIn {
        from { transform: scale(0); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .order-badge {
        display: inline-block;
        background: rgba(0,0,0,0.05);
        padding: 6px 16px;
        border-radius: 50px;
        font-weight: 700;
        color: #1e293b;
        margin-top: 1rem;
    }

    .item-table {
        width: 100%;
        margin: 2rem 0;
        border-collapse: collapse;
    }

    .item-row {
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .item-row:last-child { border: none; }

    .item-img {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        object-fit: cover;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-top: 2rem;
    }

    .info-panel {
        background: rgba(255,255,255,0.4);
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid var(--glass-border);
    }

    .action-btn {
        padding: 0.8rem 1.5rem;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .trust-strip {
        display: flex;
        justify-content: center;
        gap: 2rem;
        flex-wrap: wrap;
        margin-top: 4rem;
        opacity: 0.7;
    }

    .trust-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .summary-grid { grid-template-columns: 1fr; }
        .glass-card { padding: 1.5rem; }
    }
    @media print {
        header, .sub-header, footer, .action-btn, .success-icon, .trust-strip, .order-badge {
            display: none !important;
        }
        body {
            background: white !important;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 20px !important;
        }
        .glass-card {
            background: none !important;
            backdrop-filter: none !important;
            border: 1px solid #eee !important;
            box-shadow: none !important;
            padding: 1.5rem !important;
            margin-bottom: 1rem !important;
        }
        .summary-grid {
            display: block !important;
        }
        .info-panel {
            margin-bottom: 20px !important;
            page-break-inside: avoid;
        }
        .print-only {
            display: block !important;
            text-align: center;
            margin-bottom: 30px;
        }
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #16a34a;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    }
    
    .print-only { display: none; }
</style>
";

include 'includes/main_header.php';
?>

<div class="container conf-container">
    <!-- Print Header -->
    <div class="print-only">
        <div class="print-header">
            <div>
                <h1 style="color: #16a34a; margin:0; font-weight:900;">AgriAI</h1>
                <p style="margin:0; font-size:0.8rem; color:#64748b;">Smart Digital Marketplace for Farmers</p>
            </div>
            <div style="text-align:right;">
                <h2 style="margin:0; text-transform:uppercase;">Invoice</h2>
                <p style="margin:0; font-size:0.9rem;">Date:
                    <?php echo date('d M Y', strtotime($order['created_at'])); ?></p>
            </div>
        </div>
    </div>

    <div class="glass-card" style="text-align:center;">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h1 style="font-weight: 800; color: #1e293b;">Order Confirmed!</h1>
        <p style="color: #64748b; font-size: 1.1rem; margin-top: 0.5rem;">Thank you for your purchase. Your order has
            been registered.</p>
        <div class="order-badge">
            <i class="fas fa-hashtag"></i> ORDER ID: <?php echo $order_id; ?>
        </div>
    </div>

    <!-- Items Details -->
    <div class="glass-card">
        <h3
            style="margin-bottom: 1.5rem; font-weight:800; border-bottom: 2px solid rgba(0,0,0,0.05); padding-bottom: 0.5rem;">
            Purchase Summary</h3>
        <table class="item-table">
            <?php while ($item = $items_res->fetch_assoc()): ?>
                <tr class="item-row">
                    <td style="padding: 1rem 0;">
                        <?php
                        // Determine Default Image based on Type
                        if (!empty($item['chemical_id'])) {
                            $default_img = 'images/default_chem.png';
                        } elseif (!empty($item['seed_id'])) {
                            $default_img = 'images/default-seed.png';
                        } else {
                            $default_img = 'images/default-crop.png';
                        }

                        $raw_img = $item['image_path'];
                        $img_src = $default_img; // Start with default
                    
                        if (!empty($raw_img)) {
                            // Clean path: remove '../' to handle relative admin paths
                            $clean_path = str_replace('../', '', $raw_img);

                            // Ensure 'images/' prefix if it's a local file (not http) and missing prefix
                            if (strpos($clean_path, 'http') !== 0 && strpos($clean_path, 'images/') !== 0) {
                                $clean_path = 'images/' . $clean_path;
                            }

                            // Verify file existence (Physical check)
                            if (file_exists($clean_path)) {
                                $img_src = $clean_path;
                            } elseif (strpos($clean_path, 'http') === 0) {
                                $img_src = $clean_path; // Allow external URLs without check
                            }
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($img_src); ?>" class="item-img"
                            onerror="this.src='<?php echo $default_img; ?>'">
                    </td>
                    <td style="padding: 1rem;">
                        <span
                            style="font-size: 0.7rem; color: #94a3b8; font-weight:700; text-transform:uppercase;"><?php echo htmlspecialchars($item['category'] ?? 'Item'); ?></span>
                        <h5 style="margin: 0; color: #1e293b;"><?php echo htmlspecialchars($item['item_name']); ?></h5>
                    </td>
                    <td style="padding: 1rem; text-align:center; color: #64748b;">
                        Qty: <strong><?php echo $item['quantity']; ?></strong>
                    </td>
                    <td style="padding: 1rem; text-align:right; font-weight:700; color: #1e293b;">
                        ₹<?php echo number_format($item['subtotal'], 2); ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Pricing Breakdown -->
        <div style="max-width: 300px; margin-left: auto; text-align: right; display: grid; gap: 10px;">
            <div style="display:flex; justify-content:space-between; color: #64748b;">
                <span>Subtotal</span>
                <span>₹<?php echo number_format($order['total_amount'] / 1.05, 2); ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; color: #64748b;">
                <span>Tax (5%)</span>
                <span>₹<?php echo number_format($order['total_amount'] - ($order['total_amount'] / 1.05), 2); ?></span>
            </div>
            <div
                style="display:flex; justify-content:space-between; font-weight: 800; font-size: 1.3rem; color: var(--premium-green); border-top: 1px solid rgba(0,0,0,0.05); padding-top: 10px;">
                <span>Total Paid</span>
                <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>
    </div>

    <!-- Split Panels -->
    <div class="summary-grid">
        <div class="glass-card info-panel" style="margin:0;">
            <h4 style="margin-bottom: 1.2rem; font-weight:700; display:flex; align-items:center; gap:10px;">
                <i class="fas fa-truck" style="color:var(--premium-green);"></i> Delivery Details
            </h4>
            <div style="font-size: 0.95rem; line-height: 1.6; color: #475569;">
                <p><strong><?php echo htmlspecialchars($order['buyer_name']); ?></strong></p>
                <p><?php echo nl2br(htmlspecialchars($order['buyer_address'])); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['buyer_phone']); ?></p>
                <p style="margin-top: 10px; color: var(--premium-green); font-weight:600;">
                    <i class="fas fa-calendar-check"></i> Estimated:
                    <?php echo date('d M Y', strtotime($order['created_at'] . ' + 3 days')); ?>
                </p>
            </div>
        </div>

        <div class="glass-card info-panel" style="margin:0;">
            <h4 style="margin-bottom: 1.2rem; font-weight:700; display:flex; align-items:center; gap:10px;">
                <i class="fas fa-store" style="color:#0284c7;"></i> Seller Information
            </h4>
            <div style="font-size: 0.95rem; line-height: 1.6; color: #475569;">
                <p><strong><?php echo htmlspecialchars($order['farmer_name'] ?? 'Authorized Vendor'); ?></strong></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['farmer_phone'] ?? 'N/A'); ?></p>
                <p style="margin-top: 10px; font-size: 0.85rem; color: #64748b;">
                    Payment Method: <span
                        style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($order['payment_method'] ?? 'COD'); ?></span>
                </p>
                <?php if (!empty($order['transaction_id'])): ?>
                    <p style="font-size: 0.85rem; color: #64748b;">
                        Transaction ID: <span
                            style="font-weight:600; color:#1e293b;"><?php echo htmlspecialchars($order['transaction_id']); ?></span>
                    </p>
                <?php endif; ?>
                <div style="margin-top: 1rem;">
                    <a href="farmer_shop.php?id=<?php echo $order['farmer_id']; ?>" class="action-btn"
                        style="background: rgba(2, 132, 199, 0.1); color: #0284c7; font-size: 0.85rem;">
                        Visit Store <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div style="margin-top: 2rem; display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
        <a href="order_tracking.php?id=<?php echo $order_id; ?>" class="action-btn"
            style="background: var(--premium-green); color:white;">
            <i class="fas fa-map-marker-alt"></i> Track My Order
        </a>
        <a href="shop_crops.php" class="action-btn"
            style="background: white; border: 1px solid var(--glass-border); color: #1e293b;">
            <i class="fas fa-shopping-basket"></i> Continue Shopping
        </a>
        <button onclick="window.print()" class="action-btn"
            style="background: rgba(0,0,0,0.05); color: #64748b; border:none;">
            <i class="fas fa-download"></i> Print Invoice
        </button>
    </div>

    <!-- Trust Strip -->
    <div class="trust-strip">
        <div class="trust-item"><i class="fas fa-shield-alt" style="color:#16a34a;"></i> SECURE CHECKOUT</div>
        <div class="trust-item"><i class="fas fa-check-circle" style="color:#0284c7;"></i> VERIFIED FARMERS</div>
        <div class="trust-item"><i class="fas fa-leaf" style="color:#f59e0b;"></i> QUALITY GUARANTEE</div>
        <div class="trust-item"><i class="fas fa-headset" style="color:#ef4444;"></i> 24/7 AGRI SUPPORT</div>
    </div>
</div>

<?php include 'includes/main_footer.php'; ?>

</html>