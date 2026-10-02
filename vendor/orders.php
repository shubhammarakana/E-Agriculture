<?php
// vendor/orders.php
include '../db_connect.php';
session_start();

// Check if user is logged in and is a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

$vendor_id = $_SESSION['user_id'];
$page = 'orders';
$page_title = 'My Orders';

// Fetch Orders for this vendor
// Assuming 'farmer_id' in orders table stores the seller/vendor ID
$sql = "SELECT o.id, u.fullname, o.buyer_address, o.buyer_phone, o.total_amount, o.status, o.created_at, p.payment_method 
        FROM orders o 
        LEFT JOIN users u ON o.buyer_id = u.id 
        LEFT JOIN payments p ON o.id = p.order_id
        WHERE o.farmer_id = '$vendor_id'
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../includes/main_header.php'; ?>
<style>
    .filter-btn {
        background: rgba(255, 255, 255, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.5);
        padding: 6px 14px;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 500;
        color: #555;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }
    
    .status-badge { padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; display: inline-block; }
    .status-pending { background: #fef9c3; color: #854d0e; }
    .status-active { background: #dcfce7; color: #166534; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    
    .btn-primary-glass {
        background: rgba(79, 70, 229, 0.1); color: #4f46e5; border: 1px solid rgba(79, 70, 229, 0.2); border-radius: 8px; transition: all 0.2s;
    }
    .btn-primary-glass:hover { background: #4f46e5; color: white; }
</style>

<div class="container" style="padding: 2rem 0; min-height: 80vh;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="margin:0; color: #1e293b; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-shopping-bag" style="color: #4f46e5;"></i> Incoming Orders
            </h2>
            <p style="color: #64748b; font-size: 0.9rem; margin-top: 5px;">Manage orders for your products</p>
        </div>

        <div style="display: flex; gap: 8px;">
            <button class="filter-btn active" onclick="filterOrders('all', this)">All</button>
            <button class="filter-btn" onclick="filterOrders('new', this)">New</button>
            <button class="filter-btn" onclick="filterOrders('active', this)">Active</button>
            <button class="filter-btn" onclick="filterOrders('completed', this)">History</button>
        </div>
    </div>

    <div class="glass-table-container" style="background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(10px); border-radius: 16px; border: 1px solid rgba(255,255,255,0.6); overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <table class="glass-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: rgba(255,255,255,0.5); border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <th style="padding: 15px; text-align: left; color: #64748b; font-weight: 600;">Order ID</th>
                    <th style="padding: 15px; text-align: left; color: #64748b; font-weight: 600;">Buyer</th>
                    <th style="padding: 15px; text-align: left; color: #64748b; font-weight: 600;">Date</th>
                    <th style="padding: 15px; text-align: left; color: #64748b; font-weight: 600;">Amount</th>
                    <th style="padding: 15px; text-align: left; color: #64748b; font-weight: 600;">Payment</th>
                    <th style="padding: 15px; text-align: left; color: #64748b; font-weight: 600;">Status</th>
                    <th style="padding: 15px; text-align: right; color: #64748b; font-weight: 600;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Categorize status for filtering
                        $filter_cat = 'active';
                        if ($row['status'] == 'Pending') $filter_cat = 'new';
                        if ($row['status'] == 'Delivered' || $row['status'] == 'Cancelled') $filter_cat = 'completed';

                        $status_class = ($row['status'] == 'Pending') ? 'status-pending' : (($row['status'] == 'Cancelled') ? 'status-rejected' : 'status-active');
                        $payment = $row['payment_method'] ? ucfirst($row['payment_method']) : 'N/A';
                        $date = date('M d, Y', strtotime($row['created_at']));

                        echo "<tr class='order-row' data-category='$filter_cat' style='border-bottom: 1px solid rgba(0,0,0,0.03); transition: background 0.2s;'>
                                <td style='padding: 15px; font-family: monospace; color: #334155;'>#{$row['id']}</td>
                                <td style='padding: 15px; font-weight: 500; color: #1e293b;'>{$row['fullname']}</td>
                                <td style='padding: 15px; color: #64748b; font-size: 0.9rem;'>$date</td>
                                <td style='padding: 15px; font-weight: 600; color: #4f46e5;'>₹{$row['total_amount']}</td>
                                <td style='padding: 15px; color: #475569;'><i class='fas fa-credit-card' style='color:#94a3b8; margin-right:5px;'></i> $payment</td>
                                <td style='padding: 15px;'><span class='status-badge $status_class'>{$row['status']}</span></td>
                                <td style='padding: 15px; text-align: right;'>
                                    <a href='order_details.php?id={$row['id']}' class='btn-primary-glass' style='padding: 6px 16px; font-size:0.85rem; text-decoration:none; display: inline-flex; align-items: center; gap: 5px;'>
                                        Manage <i class='fas fa-arrow-right' style='font-size: 0.8rem;'></i>
                                    </a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center; padding: 3rem; color: #94a3b8;'>No orders received yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function filterOrders(category, btn) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const rows = document.querySelectorAll('.order-row');
        rows.forEach(row => {
            if (category === 'all' || row.dataset.category === category) {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>

<?php include '../includes/main_footer.php'; ?>
</html>
