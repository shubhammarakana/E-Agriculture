<?php
// admin/earnings.php
include '../db_connect.php';
session_start();

// Role Check: Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$page = 'earnings';
$page_title = 'Total Earnings';

// Helper function definitions
function getEarnings($conn, $period = 'total') {
    $sql = "SELECT SUM(amount) as total FROM payments WHERE status = 'Completed'";
    
    if ($period == 'today') {
        $sql .= " AND DATE(created_at) = CURDATE()";
    } elseif ($period == 'month') {
        $sql .= " AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    } elseif ($period == 'year') {
        $sql .= " AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    }
    
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    return $row['total'] ?? 0.00;
}

$total_earnings = getEarnings($conn, 'total');
$monthly_earnings = getEarnings($conn, 'month');
$today_earnings = getEarnings($conn, 'today');

// Monthly Breakdown for Chart/Table
$breakdown_sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total 
                  FROM payments 
                  WHERE status = 'Completed' 
                  GROUP BY month 
                  ORDER BY month DESC LIMIT 12";
$breakdown_res = $conn->query($breakdown_sql);

?>
<!DOCTYPE html>
<html lang="en">
<?php include '../includes/main_header.php'; ?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    <h2 style="margin-bottom: 2rem; color: #4338ca;">Earnings Overview</h2>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        
        <!-- Total -->
        <div class="glass-panel" style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); color: white;">
            <div style="font-size: 0.9rem; opacity: 0.9;">Total Revenue</div>
            <div style="font-size: 2rem; font-weight: 700; margin-top: 10px;">₹<?php echo number_format($total_earnings, 2); ?></div>
            <div style="font-size: 0.8rem; margin-top: 10px; opacity: 0.8;"><i class="fas fa-chart-line"></i> Lifetime Earnings</div>
        </div>

        <!-- Monthly -->
        <div class="glass-panel" style="background: white; border-left: 5px solid #4f46e5;">
            <div style="color: #6b7280; font-size: 0.9rem;">This Month</div>
            <div style="font-size: 2rem; font-weight: 700; color: #1f2937; margin-top: 10px;">₹<?php echo number_format($monthly_earnings, 2); ?></div>
            <div style="font-size: 0.8rem; margin-top: 10px; color: #16a34a;"><i class="fas fa-arrow-up"></i> Current Month</div>
        </div>

        <!-- Today -->
        <div class="glass-panel" style="background: white; border-left: 5px solid #16a34a;">
            <div style="color: #6b7280; font-size: 0.9rem;">Today</div>
            <div style="font-size: 2rem; font-weight: 700; color: #1f2937; margin-top: 10px;">₹<?php echo number_format($today_earnings, 2); ?></div>
            <div style="font-size: 0.8rem; margin-top: 10px; color: #6b7280;">Since Midnight</div>
        </div>
    </div>

    <!-- Breakdown Table -->
    <h3 style="color: #3730a3; margin-bottom: 1rem;">Monthly Breakdown</h3>
    <div class="glass-table-container">
        <table class="glass-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Revenue</th>
                    <th>Trend</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($breakdown_res && $breakdown_res->num_rows > 0) {
                    while ($row = $breakdown_res->fetch_assoc()) {
                        $month_name = date("F Y", strtotime($row['month']));
                        echo "<tr>
                                <td style='font-weight: 600; color: #555;'>{$month_name}</td>
                                <td style='font-weight: 700; color: #4f46e5;'>₹" . number_format($row['total'], 2) . "</td>
                                <td><span style='color: #16a34a; font-size: 0.85rem;'><i class='fas fa-chart-bar'></i> Recorded</span></td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center; padding: 1.5rem;'>No earnings data yet.</td></tr>";
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
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    
    /* Reuse Table Styles */
    .glass-table-container {
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        overflow: hidden;
    }
    .glass-table { width: 100%; border-collapse: collapse; }
    .glass-table th { padding: 15px 20px; text-align: left; background: rgba(255, 255, 255, 0.7); color: #555; font-weight: 600; border-bottom: 1px solid rgba(0, 0, 0, 0.05); }
    .glass-table td { padding: 15px 20px; border-bottom: 1px solid rgba(0, 0, 0, 0.03); color: #444; vertical-align: middle; }
    .glass-table tr:hover { background: rgba(255, 255, 255, 0.4); }
</style>
<?php include '../includes/main_footer.php'; ?>
</html>
