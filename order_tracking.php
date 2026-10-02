<?php
// order_tracking.php - PUBLIC REDESIGN
include 'db_connect.php';
session_start();

$search_query = $_GET['q'] ?? $_GET['id'] ?? '';
$search_query = $conn->real_escape_string($search_query);
$order = null;
$error_msg = '';

if (!empty($search_query)) {
    // Search by ID or Mobile Number
    $sql = "SELECT o.*, u.fullname as farmer_name 
            FROM orders o 
            LEFT JOIN users u ON o.farmer_id = u.id 
            WHERE o.id = '$search_query' OR o.buyer_phone = '$search_query' 
            ORDER BY o.created_at DESC LIMIT 1";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $order_id = $order['id'];

        // Fetch items for preview
        // Fetch items for preview
        // Fetch items for preview
        // Fetch Items with support for Crops, Chemicals, and Seeds
        $sql_items = "SELECT oi.*, 
                      COALESCE(c.name, ch.name, s.name) as item_name, 
                      COALESCE(c.image_path, ch.image_path, s.image_path) as image_path,
                      c.id as crop_id,
                      ch.id as chemical_id,
                      s.id as seed_id
                      FROM order_items oi 
                      LEFT JOIN crops c ON oi.crop_id = c.id 
                      LEFT JOIN chemicals ch ON oi.chemical_id = ch.id
                      LEFT JOIN seeds s ON oi.seed_id = s.id
                      WHERE oi.order_id = '$order_id'";
        $items_res = $conn->query($sql_items);
    } else {
        $error_msg = "No order found matching '$search_query'. Please verify the details.";
    }
}

$page = 'track_order';
$page_title = 'Track Order | AgriAI';

// Premium Styles
$extra_css = "
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.4);
        --premium-green: #16a34a;
        --soft-shadow: 0 8px 32px rgba(31, 38, 135, 0.07);
    }

    body {
        background: radial-gradient(circle at top right, #f0fdf4, #f8fafc, #ffffff);
    }

    .tracking-wrapper {
        max-width: 1000px;
        margin: 3rem auto;
        padding-bottom: 5rem;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        box-shadow: var(--soft-shadow);
        padding: 2.5rem;
        margin-bottom: 2rem;
    }

    .search-panel {
        max-width: 600px;
        margin: 0 auto 3rem;
        text-align: center;
    }

    .search-input-group {
        display: flex;
        background: rgba(255,255,255,0.5);
        border: 1px solid var(--glass-border);
        border-radius: 50px;
        padding: 5px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }

    .search-input-group input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 12px 25px;
        font-size: 1.1rem;
        font-weight: 500;
        outline: none;
    }

    .search-btn {
        background: var(--premium-green);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }

    .search-btn:hover { background: #15803d; transform: scale(1.05); }

    /* Timeline Styles */
    .timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 3rem 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 0;
        right: 0;
        height: 4px;
        background: rgba(0,0,0,0.05);
        z-index: 0;
    }

    .timeline-progress {
        position: absolute;
        top: 25px;
        left: 0;
        height: 4px;
        background: var(--premium-green);
        z-index: 0;
        transition: width 1s ease-in-out;
    }

    .timeline-step {
        z-index: 1;
        text-align: center;
        width: 100px;
    }

    .step-icon {
        width: 50px;
        height: 50px;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.2rem;
        color: #94a3b8;
        transition: all 0.3s;
    }

    .timeline-step.active .step-icon {
        background: var(--premium-green);
        border-color: var(--premium-green);
        color: white;
        box-shadow: 0 0 20px rgba(22, 163, 74, 0.4);
    }

    .timeline-step.completed .step-icon {
        background: var(--premium-green);
        border-color: var(--premium-green);
        color: white;
    }

    .step-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
    }

    .timeline-step.active .step-label { color: #1e293b; }

    /* Summary & Preview */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        background: rgba(255,255,255,0.3);
        padding: 1.5rem;
        border-radius: 16px;
    }

    .product-tile {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1rem;
        border-radius: 16px;
        background: white;
        margin-bottom: 1rem;
        transition: transform 0.2s;
    }

    .product-tile:hover { transform: translateY(-5px); }

    .ai-insight {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: white;
        padding: 1.5rem;
        border-radius: 20px;
        margin-top: 2rem;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    @media (max-width: 768px) {
        .timeline { flex-direction: column; padding-left: 20px; }
        .timeline::before { left: 45px; width: 4px; height: 100%; top: 0; }
        .timeline-progress { left: 45px; width: 4px; height: 0; }
        .timeline-step { display: flex; align-items: center; text-align: left; width: 100%; margin-bottom: 2rem; }
        .step-icon { margin: 0 1.5rem 0 0; }
        .summary-grid { grid-template-columns: 1fr 1fr; }
    }
</style>
";

include 'includes/main_header.php';
?>

<div class="container tracking-wrapper">

    <!-- TOP SEARCH PANEL -->
    <div class="glass-card search-panel">
        <h1 style="font-weight: 800; color: #1e293b; margin-bottom: 0.5rem;">Track Your Order</h1>
        <p style="color: #64748b; margin-bottom: 2rem;">Stay updated with real-time delivery progress.</p>

        <form action="" method="GET" class="search-input-group">
            <input type="text" name="q" placeholder="Enter Order ID or Mobile Number" required
                value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i> SEARCH</button>
        </form>

        <?php if ($error_msg): ?>
            <div
                style="margin-top: 1.5rem; color: #ef4444; font-size: 0.9rem; background: #fef2f2; padding: 10px; border-radius: 10px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($order):
        $status_steps = [
            ['Pending', 'fa-receipt'],
            ['Accepted', 'fa-thumbs-up'],
            ['Packed', 'fa-box-open'],
            ['Shipped', 'fa-truck'],
            ['Delivered', 'fa-home']
        ];
        $current_status = $order['status'];
        $progress_index = 0;
        foreach ($status_steps as $idx => $step) {
            if ($step[0] == $current_status)
                $progress_index = $idx;
        }
        $progress_pct = ($progress_index / (count($status_steps) - 1)) * 100;
        ?>

        <!-- MAIN TRACKING CARD -->
        <div class="glass-card">
            <div
                style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem; flex-wrap:wrap;">
                <div>
                    <span style="font-size: 0.8rem; font-weight:700; color:#64748b; text-transform:uppercase;">Order
                        Summary</span>
                    <h2 style="font-weight: 800; color: #1e293b;">#<?php echo $order['id']; ?></h2>
                </div>
                <div style="text-align:right;">
                    <span style="font-size: 0.8rem; font-weight:700; color:#64748b; text-transform:uppercase;">Estimated
                        Delivery</span>
                    <h4 style="color: var(--premium-green); font-weight:700;">
                        <?php echo date('d M Y', strtotime($order['created_at'] . ' + 3 days')); ?></h4>
                </div>
            </div>

            <!-- Timeline -->
            <div class="timeline">
                <div class="timeline-progress" style="width: <?php echo $progress_pct; ?>%;"></div>
                <?php foreach ($status_steps as $idx => $step):
                    $class = ($idx < $progress_index) ? 'completed' : (($idx == $progress_index) ? 'active' : '');
                    ?>
                    <div class="timeline-step <?php echo $class; ?>">
                        <div class="step-icon">
                            <i class="fas <?php echo $step[1]; ?>"></i>
                        </div>
                        <span class="step-label"><?php echo $step[0]; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Details Grid -->
            <div class="summary-grid">
                <div>
                    <p style="font-size: 0.75rem; font-weight:700; color:#94a3b8;">ORDER DATE</p>
                    <p style="font-weight:600;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                </div>
                <div>
                    <p style="font-size: 0.75rem; font-weight:700; color:#94a3b8;">SELLER</p>
                    <p style="font-weight:600;">
                        <?php echo htmlspecialchars($order['farmer_name'] ?? 'Authorized Farmer'); ?></p>
                </div>
                <div>
                    <p style="font-size: 0.75rem; font-weight:700; color:#94a3b8;">TOTAL VALUE</p>
                    <p style="font-weight:600; color:var(--premium-green);">
                        ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                </div>
                <div>
                    <p style="font-size: 0.75rem; font-weight:700; color:#94a3b8;">DESTINATION</p>
                    <p style="font-weight:600; font-size: 0.85rem;"><?php echo htmlspecialchars($order['buyer_address']); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- PRODUCT PREVIEW & AI INSIGHT -->
        <div style="display:grid; grid-template-columns: 1.5fr 1fr; gap: 2rem;">
            <div class="glass-card" style="margin-bottom:0;">
                <h3 style="margin-bottom: 1.5rem; font-weight:800;">Product Preview</h3>
                <?php while ($item = $items_res->fetch_assoc()): ?>
                    <div class="product-tile">
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
                        <img src="<?php echo htmlspecialchars($img_src); ?>"
                            style="width: 70px; height: 70px; border-radius: 12px; object-fit: cover;" onerror="this.src='<?php echo $default_img; ?>'">
                        <div>
                            <h5 style="margin:0;"><?php echo htmlspecialchars($item['item_name']); ?></h5>
                            <p style="margin:0; font-size: 0.85rem; color:#64748b;">Quantity:
                                <strong><?php echo $item['quantity']; ?></strong></p>
                        </div>
                        <div style="margin-left:auto; font-weight:700;">₹<?php echo number_format($item['subtotal'], 2); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div>
                <div class="glass-card"
                    style="margin-bottom:0; background: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2);">
                    <h4 style="font-weight:800;"><i class="fas fa-headset"></i> Support</h4>
                    <p style="font-size: 0.9rem; color:#475569; margin-top: 10px;">Having issues with delivery? Contact our
                        24/7 Agri-Support team.</p>
                    <a href="contact.php" class="btn btn-outline" style="width:100%; margin-top: 1rem;">GET HELP</a>
                </div>

                <div class="ai-insight">
                    <i class="fas fa-magic" style="font-size: 1.5rem; color:#fbbf24;"></i>
                    <div>
                        <p style="margin:0; font-size: 0.7rem; font-weight:700; opacity:0.6; text-transform:uppercase;">AI
                            Delivery Insight</p>
                        <p style="margin:0; font-size: 0.9rem;">Optimal delivery route calculated. No weather delays
                            expected for your region.</p>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>

<?php include 'includes/main_footer.php'; ?>

</html>