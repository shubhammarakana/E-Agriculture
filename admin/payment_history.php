<?php
// admin/payment_history.php
include '../db_connect.php';
session_start();

// Role Check: Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$page = 'payment_history';
$page_title = 'Payment History';

// Fetch All Payments with User Info
$sql = "SELECT p.*, u.fullname as payer_name, u.email 
        FROM payments p 
        LEFT JOIN users u ON p.user_id = u.id 
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../includes/main_header.php'; ?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin:0; color: #4338ca;">Transaction History</h2>
        <!-- Future: Export Button -->
    </div>

    <div class="glass-table-container">
        <table class="glass-table">
            <thead>
                <tr>
                    <th>Txn ID</th>
                    <th>Payer / User</th>
                    <th>Order Ref</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        
                        $status_colors = [
                            'Completed' => ['bg' => '#dcfce7', 'text' => '#166534'],
                            'Pending'   => ['bg' => '#fef9c3', 'text' => '#854d0e'],
                            'Failed'    => ['bg' => '#fee2e2', 'text' => '#991b1b']
                        ];
                        $st = $row['status'] ?? 'Pending';
                        $sc = $status_colors[$st] ?? $status_colors['Pending'];
                        
                        $date = date("M d, Y h:i A", strtotime($row['created_at']));

                        echo "<tr>
                                <td style='font-family: monospace; color: #555;'>{$row['transaction_id']}</td>
                                <td>
                                    <div style='font-weight:600; color:#1f2937;'>{$row['payer_name']}</div>
                                    <div style='font-size:0.85rem; color:#6b7280;'>{$row['email']}</div>
                                </td>
                                <td>#{$row['order_id']}</td>
                                <td style='font-weight: 600; color: #4b5563;'>₹{$row['amount']}</td>
                                <td>{$row['payment_method']}</td>
                                <td style='font-size: 0.9rem;'>$date</td>
                                <td><span style='background: {$sc['bg']}; color: {$sc['text']}; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;'>{$st}</span></td>
                                <td>
                                    <button onclick='confirmDelete({$row['id']})' class='btn-danger-glass' style='padding: 5px 10px; font-size:0.8rem; border:none; background: rgba(239, 68, 68, 0.2); color: #ef4444; border-radius: 8px; cursor: pointer; transition: all 0.2s;'>
                                        <i class='fas fa-trash'></i>
                                    </button>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center; padding: 2rem;'>No transaction history found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Admin Table Styles (Reused) */
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
</style>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#3b82f6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'delete_payment.php?id=' + id;
            }
        })
    }
</script>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
<script>
    Swal.fire(
        'Deleted!',
        'The payment record has been deleted.',
        'success'
    )
</script>
<?php endif; ?>

<?php include '../includes/main_footer.php'; ?>
</html>
