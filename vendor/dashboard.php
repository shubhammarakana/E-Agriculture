<?php
// vendor/dashboard.php
include '../db_connect.php';
session_start();

// Vendor Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

$page = 'dashboard';
$page_title = 'Vendor Dashboard';

// Fetch Vendor Metrics (Placeholders for now, or basic queries if tables exist)
// Assuming products table has 'vendor_id' or 'user_id'
$uid = $_SESSION['user_id'];

// 1. Total Products
$uid = $_SESSION['user_id'];
$crops_count = $conn->query("SELECT COUNT(*) as c FROM crops WHERE farmer_id='$uid'")->fetch_assoc()['c'];
$chems_count = $conn->query("SELECT COUNT(*) as c FROM chemicals WHERE seller_id='$uid'")->fetch_assoc()['c'];
$seeds_count = $conn->query("SELECT COUNT(*) as c FROM seeds WHERE seller_id='$uid'")->fetch_assoc()['c'];
$products_count = $crops_count + $chems_count + $seeds_count;

// 2. Fetch Recent Crops
$recent_crops = $conn->query("SELECT * FROM crops WHERE farmer_id='$uid' ORDER BY id DESC LIMIT 5");

// 2. Total Orders
$orders_count = $conn->query("SELECT COUNT(*) as c FROM orders WHERE farmer_id='$uid'")->fetch_assoc()['c'];

// 3. Total Revenue
$revenue_query = $conn->query("SELECT SUM(p.amount) as total FROM payments p JOIN orders o ON p.order_id = o.id WHERE o.farmer_id='$uid' AND p.status='Completed'");
$revenue = $revenue_query->fetch_assoc()['total'] ?? 0;

include '../includes/main_header.php';
?>

<div class="container" style="padding: 2rem 0;">
    <h1 style="margin-bottom: 2rem; color: #1f2937;">Vendor Dashboard</h1>

    <!-- KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 3rem;">
        
        <!-- Total Products -->
        <div class="glass-panel" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 5px;">My Crops</p>
                <h2 style="font-size: 2rem; margin: 0; color: #16a34a;"><?php echo $products_count; ?></h2>
                <span style="font-size: 0.8rem; color: #64748b;"><?php echo $crops_count; ?> Crops, <?php echo $chems_count; ?> Chemicals, <?php echo $seeds_count; ?> Seeds</span>
            </div>
            <div style="background: #dcfce7; color: #16a34a; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-box-open"></i>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="glass-panel" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 5px;">Total Orders</p>
                <h2 style="font-size: 2rem; margin: 0; color: #2563eb;"><?php echo $orders_count; ?></h2>
            </div>
            <div style="background: #dbeafe; color: #2563eb; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-shopping-bag"></i>
            </div>
        </div>

        <!-- Revenue -->
        <div class="glass-panel" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 5px;">Total Revenue</p>
                <h2 style="font-size: 2rem; margin: 0; color: #9333ea;">₹<?php echo number_format($revenue); ?></h2>
            </div>
            <div style="background: #f3e8ff; color: #9333ea; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 2rem;">
        <div class="glass-panel">
            <h3 style="margin-bottom: 1.5rem;">Sales Performance</h3>
            <canvas id="salesChart" height="150"></canvas>
        </div>
        <div class="glass-panel">
            <h3 style="margin-bottom: 1.5rem;">Product Distribution</h3>
            <canvas id="productChart" height="200"></canvas>
        </div>
    </div>

    <!-- Recently Added Products -->
    <div class="glass-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; color: #1f2937;">Recently Added Crops</h3>
            <a href="products.php" style="color: #16a34a; text-decoration: none; font-weight: 500; font-size: 0.9rem;">View All</a>
        </div>
        
        <table class="glass-table" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <th style="text-align: left; padding: 10px; color: #6b7280;">Product</th>
                    <th style="text-align: left; padding: 10px; color: #6b7280;">Price</th>
                    <th style="text-align: left; padding: 10px; color: #6b7280;">Stock</th>
                    <th style="text-align: right; padding: 10px; color: #6b7280;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recent_crops->num_rows > 0): ?>
                    <?php while($row = $recent_crops->fetch_assoc()): ?>
                        <?php 
                            $img = !empty($row["image_path"]) ? $row["image_path"] : 'images/default-crop.png';
                            if (strpos($img, 'images/') === 0) $img = '../' . $img;
                        ?>
                        <tr style="border-bottom: 1px solid #f9fafb;">
                            <td style="padding: 12px 10px; display: flex; align-items: center; gap: 12px;">
                                <img src="<?php echo $img; ?>" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover;">
                                <div>
                                    <span style="display: block; font-weight: 600; color: #374151; font-size: 0.95rem;"><?php echo htmlspecialchars($row['name']); ?></span>
                                    <span style="font-size: 0.8rem; color: #9ca3af;"><?php echo htmlspecialchars($row['category']); ?></span>
                                </div>
                            </td>
                            <td style="padding: 10px; font-weight: 600; color: #16a34a;">₹<?php echo $row['price']; ?></td>
                            <td style="padding: 10px;">
                                <span style="font-size: 0.85rem; padding: 2px 8px; border-radius: 10px; background: <?php echo ($row['stock_status'] == 'In Stock') ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo ($row['stock_status'] == 'In Stock') ? '#166534' : '#991b1b'; ?>;">
                                    <?php echo $row['stock_status']; ?>
                                </span>
                            </td>
                            <td style="padding: 10px; text-align: right;">
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" style="color: #6366f1; background: #e0e7ff; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none;">
                                    <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; padding: 20px; color: #9ca3af;">No products added yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Product Chart (Dynamic Data from PHP would be better, but keeping simple for now)
    const ctxProduct = document.getElementById('productChart').getContext('2d');
    new Chart(ctxProduct, {
        type: 'doughnut',
        data: {
            labels: ['Crops', 'Chemicals', 'Seeds'],
            datasets: [{
                data: [<?php echo $crops_count; ?>, <?php echo $chems_count; ?>, <?php echo $seeds_count; ?>],
                backgroundColor: ['#16a34a', '#6366f1', '#f59e0b'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, cutout: '70%' }
    });

    // Sales Chart (Mock Data)
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    new Chart(ctxSales, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue (₹)',
                data: [5000, 8000, 3000, 12000, 15000, 20000],
                borderColor: '#16a34a',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(22, 163, 74, 0.1)'
            }]
        },
        options: { 
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

<?php include '../includes/main_footer.php'; ?>
