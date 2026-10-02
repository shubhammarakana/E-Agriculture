<?php
// purchase_history.php - Public Buyer Dashboard
include 'db_connect.php';
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$page = 'orders';
$page_title = 'My Purchase History | AgriAI';

// Search/Filter logic
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

$where_clause = "";
if ($user_id) {
    $where_clause = "buyer_id='$user_id'";
} else if (isset($_SESSION['guest_orders']) && !empty($_SESSION['guest_orders'])) {
    $ids = implode(',', array_map('intval', $_SESSION['guest_orders']));
    $where_clause = "id IN ($ids)";
}

if (!empty($search)) {
    $where_clause .= ($where_clause ? " AND " : "") . "(id LIKE '%$search%' OR buyer_name LIKE '%$search%')";
}
if (!empty($status_filter)) {
    $where_clause .= ($where_clause ? " AND " : "") . "status='$status_filter'";
}

if ($where_clause) {
    $sql = "SELECT * FROM orders WHERE $where_clause ORDER BY created_at DESC";
    $result = $conn->query($sql);

    // Stats calc
    $stats = [
        'Total' => $conn->query("SELECT COUNT(*) as c FROM orders WHERE $where_clause")->fetch_assoc()['c'],
        'Delivered' => $conn->query("SELECT COUNT(*) as c FROM orders WHERE $where_clause AND status='Delivered'")->fetch_assoc()['c'],
        'In Transit' => $conn->query("SELECT COUNT(*) as c FROM orders WHERE $where_clause AND status IN ('Shipped', 'Packed')")->fetch_assoc()['c'],
        'Pending' => $conn->query("SELECT COUNT(*) as c FROM orders WHERE $where_clause AND status='Pending'")->fetch_assoc()['c'],
    ];
} else {
    $result = null;
    $stats = ['Total' => 0, 'Delivered' => 0, 'In Transit' => 0, 'Pending' => 0];
}

// Premium Styles
$extra_css = "
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.4);
        --premium-green: #16a34a;
        --premium-blue: #0284c7;
        --status-pending: #f59e0b;
        --status-shipped: #3b82f6;
        --status-delivered: #10b981;
        --status-cancelled: #ef4444;
    }

    body {
        background: radial-gradient(circle at bottom right, #f0fdf4, #f8fafc, #ffffff);
    }

    .dashboard-header {
        margin-bottom: 3rem;
    }
    
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        transition: transform 0.3s ease;
    }

    .stat-card:hover { transform: translateY(-5px); }

    .stat-val { font-size: 1.8rem; font-weight: 900; color: #1e293b; margin-bottom: 5px; }
    .stat-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; }

    .order-card {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.04);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .order-header {
        background: rgba(0,0,0,0.02);
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .order-body {
        padding: 1.5rem;
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .product-preview {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex: 2;
        min-width: 300px;
    }

    .crop-img {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
    }

    .order-actions {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-width: 150px;
    }

    .badge-glass {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    
    .type-badge {
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.65rem;
        text-transform: uppercase;
        font-weight: 700;
        margin-right: 5px;
    }
    .type-crop { background: #dcfce7; color: #166534; }
    .type-chemical { background: #dbeafe; color: #1e40af; }
    .type-seed { background: #fef9c3; color: #854d0e; }

    .status-Pending { background: #fef3c7; color: #92400e; }
    .status-Delivered { background: #d1fae5; color: #065f46; }
    .status-Shipped { background: #dbeafe; color: #1e40af; }
    .status-Cancelled { background: #fee2e2; color: #991b1b; }

    .action-btn-glass {
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 700;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s;
        border: 1px solid var(--glass-border);
        background: white;
        color: #1e293b;
    }

    .action-btn-glass:hover { background: #f8fafc; transform: scale(1.02); }

    .search-filter-panel {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .search-input {
        flex: 1;
        min-width: 250px;
        background: rgba(255,255,255,0.5);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        padding: 12px 20px;
        outline: none;
    }

    @media (max-width: 768px) {
        .product-preview { flex-direction: column; text-align: center; }
        .order-body { flex-direction: column; text-align: center; }
        .order-actions { width: 100%; }
    }
</style>
";

include 'includes/main_header.php';
?>

<div class="container" style="padding-bottom: 5rem;">

    <div class="dashboard-header">
        <h1 style="font-weight: 800; color: #1e293b;">My Purchase History</h1>
        <p style="color: #64748b;">Manage your purchases and track deliveries in one place.</p>
    </div>

    <!-- Quick Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-val"><?php echo $stats['Total']; ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color:var(--status-delivered);"><?php echo $stats['Delivered']; ?></div>
            <div class="stat-label">Delivered</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color:var(--status-shipped);"><?php echo $stats['In Transit']; ?></div>
            <div class="stat-label">In Transit</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="color:var(--status-pending);"><?php echo $stats['Pending']; ?></div>
            <div class="stat-label">Pending</div>
        </div>
    </div>

    <!-- Search & Filters -->
    <form action="" method="GET" class="search-filter-panel">
        <input type="text" name="search" class="search-input" placeholder="Search by Order ID or Product..."
            value="<?php echo htmlspecialchars($search); ?>">
        <select name="status" class="search-input" style="width: auto;">
            <option value="">All Statuses</option>
            <option value="Pending" <?php if ($status_filter == 'Pending')
                echo 'selected'; ?>>Pending</option>
            <option value="Shipped" <?php if ($status_filter == 'Shipped')
                echo 'selected'; ?>>In Transit</option>
            <option value="Delivered" <?php if ($status_filter == 'Delivered')
                echo 'selected'; ?>>Delivered</option>
            <option value="Cancelled" <?php if ($status_filter == 'Cancelled')
                echo 'selected'; ?>>Cancelled</option>
        </select>
        <button type="submit" class="btn btn-primary" style="padding: 0 30px; border-radius: 12px; background: var(--premium-green); color: white; border: none; font-weight: 700;">FILTER</button>
    </form>

    <?php if (!$user_id): ?>
        <div class="glass-card" style="padding: 1rem; text-align: center; background: rgba(22, 163, 74, 0.05); margin-bottom: 2rem; border-radius: 12px;">
            <p style="margin:0; font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i> Showing <strong>Guest Orders</strong> from your current session.
                <a href="login.php" style="color:var(--premium-green); font-weight:700;">Login</a> to see your full history.
            </p>
        </div>
    <?php endif; ?>

    <!-- Orders List -->
    <div style="margin-top: 2rem;">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()):
                $oid = $row['id'];
                // Updated Fetch Query for multiple item types
                $item_sql = "SELECT oi.*, 
                             COALESCE(c.name, ch.name, s.name) as name, 
                             COALESCE(c.image_path, ch.image_path, s.image_path) as image_path,
                             CASE 
                                WHEN oi.chemical_id IS NOT NULL THEN 'chemical' 
                                WHEN oi.seed_id IS NOT NULL THEN 'seed'
                                ELSE 'crop' 
                             END as item_type
                             FROM order_items oi 
                             LEFT JOIN crops c ON oi.crop_id = c.id 
                             LEFT JOIN chemicals ch ON oi.chemical_id = ch.id
                             LEFT JOIN seeds s ON oi.seed_id = s.id
                             WHERE oi.order_id = '$oid' LIMIT 1";
                $item = $conn->query($item_sql)->fetch_assoc();
                
                // Fallback Logic
                $item_type = $item['item_type'] ?? 'crop';
                $def_img = 'images/default-crop.png';
                if($item_type == 'chemical') $def_img = 'images/default_chem.png';
                if($item_type == 'seed') $def_img = 'images/default_seed.png';

                $img_path = !empty($item['image_path']) ? $item['image_path'] : $def_img;
                
                // Clean path if needed
                if(strpos($img_path, 'uploads/') === 0) $img_path = $img_path; // Keep relative
                elseif(strpos($img_path, 'images/') !== 0 && strpos($img_path, 'http') !== 0) $img_path = 'images/' . $img_path;
                ?>
                <div class="order-card">
                    <div class="order-header">
                        <div style="display:flex; gap:2rem; flex-wrap:wrap;">
                            <div>
                                <p style="font-size: 0.65rem; font-weight:700; color:#64748b; margin:0;">ORDER PLACED</p>
                                <p style="font-weight:600; font-size: 0.9rem; margin:0;">
                                    <?php echo date('d M Y', strtotime($row['created_at'])); ?></p>
                            </div>
                            <div>
                                <p style="font-size: 0.65rem; font-weight:700; color:#64748b; margin:0;">TOTAL</p>
                                <p style="font-weight:700; font-size: 0.9rem; margin:0; color:var(--premium-green);">
                                    ₹<?php echo number_format($row['total_amount'], 2); ?></p>
                            </div>
                            <div>
                                <p style="font-size: 0.65rem; font-weight:700; color:#64748b; margin:0;">SHIP TO</p>
                                <p style="font-weight:600; font-size: 0.9rem; margin:0; color:var(--premium-blue);">
                                    <?php echo htmlspecialchars($row['buyer_name'] ?? 'Self'); ?></p>
                            </div>
                        </div>
                        <div>
                            <p style="font-size: 0.65rem; font-weight:700; color:#64748b; margin:0; text-align:right;">ORDER #
                                <?php echo $oid; ?></p>
                            <a href="order_tracking.php?id=<?php echo $oid; ?>"
                                style="font-size: 0.8rem; color:var(--premium-blue); font-weight:700;">View order details</a>
                        </div>
                    </div>

                    <div class="order-body">
                        <div class="product-preview">
                            <img src="<?php echo $img_path; ?>" class="crop-img">
                            <div>
                                <div style="margin-bottom: 5px;">
                                    <span class="type-badge type-<?php echo $item_type; ?>"><?php echo $item_type; ?></span>
                                    <span class="badge-glass status-<?php echo $row['status']; ?>"><?php echo $row['status']; ?></span>
                                </div>
                                <h4 style="margin: 5px 0 5px; font-weight:700;">
                                    <?php echo htmlspecialchars($item['name'] ?? 'Multiple Items'); ?></h4>
                                <p style="font-size: 0.85rem; color:#64748b; margin:0;">Quantity:
                                    <strong><?php echo $item['quantity'] ?? '1'; ?></strong></p>
                            </div>
                        </div>

                        <div class="order-actions">
                            <a href="order_tracking.php?id=<?php echo $oid; ?>" class="btn btn-primary"
                                style="border-radius:10px; background: var(--premium-green); color: white; padding: 10px 20px; text-decoration: none; text-align: center; font-weight: 600;">Track Package</a>
                            <a href="submit_review.php?order_id=<?php echo $oid; ?>" class="action-btn-glass">Give Feedback</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="glass-card" style="text-align:center; padding: 4rem; background: rgba(255,255,255,0.5); border-radius: 20px;">
                <i class="fas fa-shopping-basket" style="font-size: 4rem; color:#e2e8f0; margin-bottom: 1.5rem;"></i>
                <h3 style="color:#64748b;">No orders found.</h3>
                <p style="color:#94a3b8; margin-bottom: 2rem;">Looks like you haven't placed any orders yet.</p>
                <a href="shop_crops.php" class="btn btn-primary" style="padding: 12px 30px; border-radius: 50px; background: var(--premium-green); color: white; text-decoration: none; font-weight: 700;">Start
                    Shopping</a>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php include 'includes/main_footer.php'; ?>

</html>
