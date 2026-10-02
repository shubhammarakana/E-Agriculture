<?php
// vendor/earnings.php
include '../db_connect.php';
session_start();

// Checklist: Vendor Access Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

$page = 'earnings';
$page_title = 'My Earnings';
$vendor_id = $_SESSION['user_id'];

// Helper function: Calculate Earnings for specific vendor
function getVendorEarnings($conn, $vendor_id, $period = 'total') {
    // Join payments with orders to filter by vendor (farmer_id)
    $sql = "SELECT SUM(p.amount) as total 
            FROM payments p 
            JOIN orders o ON p.order_id = o.id 
            WHERE o.farmer_id = '$vendor_id' AND p.status = 'Completed'";
    
    if ($period == 'today') {
        $sql .= " AND DATE(p.created_at) = CURDATE()";
    } elseif ($period == 'month') {
        $sql .= " AND MONTH(p.created_at) = MONTH(CURRENT_DATE()) AND YEAR(p.created_at) = YEAR(CURRENT_DATE())";
    } elseif ($period == 'year') {
        $sql .= " AND YEAR(p.created_at) = YEAR(CURRENT_DATE())";
    }
    
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    return $row['total'] ?? 0.00;
}

$total_earnings = getVendorEarnings($conn, $vendor_id, 'total');
$monthly_earnings = getVendorEarnings($conn, $vendor_id, 'month');
$today_earnings = getVendorEarnings($conn, $vendor_id, 'today');

// Monthly Breakdown for Vendor
$breakdown_sql = "SELECT DATE_FORMAT(p.created_at, '%Y-%m') as month, SUM(p.amount) as total 
                  FROM payments p 
                  JOIN orders o ON p.order_id = o.id 
                  WHERE o.farmer_id = '$vendor_id' AND p.status = 'Completed'
                  GROUP BY month 
                  ORDER BY month DESC LIMIT 12";
$breakdown_res = $conn->query($breakdown_sql);

?>
<!DOCTYPE html>
<html lang="en">
<?php include '../includes/main_header.php'; ?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="margin:0; color: #4338ca;"><i class="fas fa-wallet" style="color: #6366f1;"></i> My Earnings</h2>
            <p style="color: #64748b; margin-top: 5px;">Track your revenue from product sales.</p>
        </div>
        <!-- Potential Export Button -->
    </div>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        
        <!-- Total -->
        <div class="glass-panel" style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); color: white; border: none; box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);">
            <div style="font-size: 0.9rem; opacity: 0.9;">Total Earned</div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-top: 10px;">₹<?php echo number_format($total_earnings, 2); ?></div>
            <div style="font-size: 0.8rem; margin-top: 10px; opacity: 0.8; display: flex; align-items: center; gap: 5px;">
                <i class="fas fa-check-circle"></i> Lifetime Revenue
            </div>
        </div>

        <!-- Monthly -->
        <div class="glass-panel" style="background: white; border-left: 5px solid #4f46e5;">
            <div style="color: #6b7280; font-size: 0.9rem;">This Month</div>
            <div style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-top: 10px;">₹<?php echo number_format($monthly_earnings, 2); ?></div>
            <div style="font-size: 0.8rem; margin-top: 10px; color: #16a34a; font-weight: 500;">
                <i class="fas fa-arrow-trend-up"></i> Current Performance
            </div>
        </div>

        <!-- Today -->
        <div class="glass-panel" style="background: white; border-left: 5px solid #16a34a;">
            <div style="color: #6b7280; font-size: 0.9rem;">Today</div>
            <div style="font-size: 2rem; font-weight: 700; color: #1e293b; margin-top: 10px;">₹<?php echo number_format($today_earnings, 2); ?></div>
            <div style="font-size: 0.8rem; margin-top: 10px; color: #6b7280;">
                <i class="fas fa-clock"></i> Since Midnight
            </div>
        </div>
    </div>

    <!-- Breakdown Table -->
    <h3 style="color: #334155; margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-chart-bar" style="color: #6366f1;"></i> Monthly Breakdown
    </h3>
    <div class="glass-table-container">
        <table class="glass-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Revenue</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($breakdown_res && $breakdown_res->num_rows > 0) {
                    while ($row = $breakdown_res->fetch_assoc()) {
                        $month_name = date("F Y", strtotime($row['month']));
                        echo "<tr>
                                <td style='font-weight: 600; color: #475569;'>{$month_name}</td>
                                <td style='font-weight: 700; color: #4f46e5;'>₹" . number_format($row['total'], 2) . "</td>
                                <td><span style='background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;'>Settled</span></td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center; padding: 2rem; color: #94a3b8;'>No earnings recorded yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .glass-panel {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.5);
        transition: transform 0.2s;
    }
    .glass-panel:hover { transform: translateY(-3px); }
    
    /* Reuse Table Styles */
    .glass-table-container {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        overflow: hidden;
    }
    .glass-table { width: 100%; border-collapse: collapse; }
    .glass-table th { padding: 15px 20px; text-align: left; background: rgba(255, 255, 255, 0.7); color: #64748b; font-weight: 600; border-bottom: 1px solid rgba(0, 0, 0, 0.05); }
    .glass-table td { padding: 15px 20px; border-bottom: 1px solid rgba(0, 0, 0, 0.03); color: #334155; vertical-align: middle; }
    .glass-table tr:hover { background: rgba(255, 255, 255, 0.5); }
</style>
<?php include '../includes/main_footer.php'; ?>
</html>
