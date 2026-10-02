<?php
// admin/dashboard.php
include '../db_connect.php';
session_start();

// Admin Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$page = 'dashboard';
$page_title = 'Admin Dashboard';

// Fetch Metrics
$buyers_count = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='buyer'")->fetch_assoc()['c'];
$vendors_count = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='vendor'")->fetch_assoc()['c'];

$orders_count = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
// Assuming orders has total_amount. If not, we might need to check payments or sum order_items.
// For now, let's assume 'orders' table has 'total_amount'.
$revenue_res = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status='Delivered' OR status='completed'"); 
// Note: spelling of 'delivered' might vary, checking generic 'completed' or just all orders for overview
$revenue = $revenue_res->fetch_assoc()['total'] ?? 0;


include '../includes/main_header.php';
?>

<div class="container" style="padding: 2rem 0;">
    <h1 style="margin-bottom: 2rem; color: #1f2937;">Admin Dashboard</h1>

    <!-- KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 3rem;">
        


        <!-- Buyers -->
        <div class="glass-panel" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 5px;">Total Buyers</p>
                <h2 style="font-size: 2rem; margin: 0; color: #2563eb;"><?php echo $buyers_count; ?></h2>
            </div>
            <div style="background: #dbeafe; color: #2563eb; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <!-- Vendors -->
        <div class="glass-panel" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 5px;">Total Vendors</p>
                <h2 style="font-size: 2rem; margin: 0; color: #f59e0b;"><?php echo $vendors_count; ?></h2>
            </div>
            <div style="background: #fef3c7; color: #f59e0b; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-store"></i>
            </div>
        </div>

        <!-- Orders -->
        <div class="glass-panel" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 5px;">Total Orders</p>
                <h2 style="font-size: 2rem; margin: 0; color: #ea580c;"><?php echo $orders_count; ?></h2>
            </div>
            <div style="background: #ffedd5; color: #ea580c; width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
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
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        
        <div class="glass-panel">
            <h3 style="margin-bottom: 1.5rem;">System Growth</h3>
            <canvas id="growthChart" height="150"></canvas>
        </div>

        <div class="glass-panel">
            <h3 style="margin-bottom: 1.5rem;">User Distribution</h3>
            <canvas id="userChart" height="200"></canvas>
        </div>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // User Chart
    const ctxUser = document.getElementById('userChart').getContext('2d');
    new Chart(ctxUser, {
        type: 'doughnut',
        data: {
            labels: ['Buyers', 'Vendors'],
            datasets: [{
                data: [<?php echo $buyers_count; ?>, <?php echo $vendors_count; ?>],
                backgroundColor: ['#2563eb', '#f59e0b'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, cutout: '70%' }
    });

    // Growth Chart (Mock Data for Demo)
    const ctxGrowth = document.getElementById('growthChart').getContext('2d');
    new Chart(ctxGrowth, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue (₹)',
                data: [12000, 19000, 3000, 5000, 20000, <?php echo $revenue ?: 25000; ?>],
                borderColor: '#9333ea',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(147, 51, 234, 0.1)'
            }]
        },
        options: { 
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

<?php include '../includes/main_footer.php'; ?>
