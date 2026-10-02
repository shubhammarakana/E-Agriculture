<?php
// incoming_orders.php
include '../db_connect.php';
session_start();

// Check if user is logged in and is a farmer or admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['farmer', 'admin'])) {
    header("Location: ../login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];
$page = 'orders';
$page_title = 'Incoming Orders';

// Fetch Orders with Buyer info and Payment Method
// Left join payments because payment might fail or be missing in edge cases
$sql_cond = "";
if ($_SESSION['role'] != 'admin') {
    $sql_cond = "WHERE o.farmer_id='$farmer_id'";
}

$sql = "SELECT o.id, u.fullname, o.buyer_address, o.buyer_phone, o.total_amount, o.status, o.created_at, p.payment_method 
        FROM orders o 
        LEFT JOIN users u ON o.buyer_id = u.id 
        LEFT JOIN payments p ON o.id = p.order_id
        $sql_cond
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<?php
$page_title = 'Incoming Orders';
include '../includes/main_header.php';
?>
<style>
    .filter-btn {
        background: rgba(255, 255, 255, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.5);
        padding: 8px 16px;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 500;
        color: #555;
        transition: all 0.3s;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }
</style>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    <!-- Content -->

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin:0; color: #2E7D32;">Order Management</h2>

        <div style="display: flex; gap: 10px;">
            <button class="filter-btn active" onclick="filterOrders('all', this)">All Orders</button>
            <button class="filter-btn" onclick="filterOrders('new', this)">New (Pending)</button>
            <button class="filter-btn" onclick="filterOrders('active', this)">In Progress</button>
            <button class="filter-btn" onclick="filterOrders('completed', this)">Completed</button>
        </div>
    </div>

    <div class="glass-table-container">
        <table class="glass-table" id="ordersTable">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Buyer Name</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Categorize status for filtering
                        $filter_cat = 'active';
                        if ($row['status'] == 'Pending')
                            $filter_cat = 'new';
                        if ($row['status'] == 'Delivered' || $row['status'] == 'Cancelled')
                            $filter_cat = 'completed';

                        $status_class = ($row['status'] == 'Pending') ? 'status-pending' : (($row['status'] == 'Cancelled') ? 'status-rejected' : 'status-active');

                        $payment = $row['payment_method'] ? ucfirst($row['payment_method']) : 'N/A';

                        // Fetch First Item Image
                        $oid = $row['id'];
                        $sql_img = "SELECT 
                                    COALESCE(c.image_path, ch.image_path, s.image_path) as image_path,
                                    ch.id as chemical_id, s.id as seed_id
                                    FROM order_items oi
                                    LEFT JOIN crops c ON oi.crop_id = c.id
                                    LEFT JOIN chemicals ch ON oi.chemical_id = ch.id
                                    LEFT JOIN seeds s ON oi.seed_id = s.id
                                    WHERE oi.order_id='$oid' LIMIT 1";
                        $res_img = $conn->query($sql_img);
                        $img_item = $res_img->fetch_assoc();
                        
                        // Default Image Logic
                        if (!empty($img_item['chemical_id'])) {
                            $default_img_relative = 'images/default_chem.png';
                        } elseif (!empty($img_item['seed_id'])) {
                            $default_img_relative = 'images/default-seed.png';
                        } else {
                            $default_img_relative = 'images/default-crop.png';
                        }
                        $default_img = '../' . $default_img_relative;
                        
                        $img_src = $default_img;
                        if ($img_item && !empty($img_item['image_path'])) {
                            $raw_img = $img_item['image_path'];
                            $clean_path = str_replace('../', '', $raw_img);
                             if (strpos($clean_path, 'http') === 0) {
                                $img_src = $clean_path;
                            } else {
                                if (strpos($clean_path, 'images/') !== 0) $clean_path = 'images/' . $clean_path;
                                if (file_exists(__DIR__ . '/../' . $clean_path)) {
                                    $img_src = '../' . $clean_path;
                                }
                            }
                        }

                        echo "<tr class='order-row' data-category='$filter_cat'>
                                <td>#{$row['id']}</td>
                                <td>
                                    <img src='" . htmlspecialchars($img_src) . "' onerror=\"this.src='$default_img'\" style='width:40px; height:40px; border-radius:8px; object-fit:cover; margin-right:10px; vertical-align:middle;'>
                                    {$row['fullname']}
                                </td>
                                <td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
                                <td>\${$row['total_amount']}</td>
                                <td><i class='fas fa-credit-card' style='color:#777; margin-right:5px;'></i> $payment</td>
                                <td><span class='status-badge $status_class'>{$row['status']}</span></td>
                                <td>
                                    <!-- Hidden Data for Modal -->
                                    <input type='hidden' class='order-address' value='{$row['buyer_address']}'>
                                    <input type='hidden' class='order-phone' value='{$row['buyer_phone']}'>
                                    <input type='hidden' class='order-buyer' value='{$row['fullname']}'>
                                    <a href='order_details.php?id={$row['id']}' class='btn-primary-glass' style='padding: 5px 15px; font-size:0.8rem; text-decoration:none;'>Manage</a>
                                    <button onclick='confirmDelete({$row['id']})' class='btn-danger-glass' style='padding: 5px 10px; font-size:0.8rem; border:none; background: rgba(239, 68, 68, 0.2); color: #ef4444; border-radius: 8px; cursor: pointer; transition: all 0.2s;'>
                                        <i class='fas fa-trash'></i>
                                    </button>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center;'>No orders found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- /Content -->
</div>
<?php include '../includes/main_footer.php'; ?>

</html>

<script>
    function filterOrders(category, btn) {
        // Update Buttons
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Filter Rows
        const rows = document.querySelectorAll('.order-row');
        rows.forEach(row => {
            if (category === 'all' || row.dataset.category === category) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

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
                window.location.href = 'delete_order.php?id=' + id;
            }
        })
    }
</script>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
<script>
    Swal.fire(
        'Deleted!',
        'The order has been deleted.',
        'success'
    )
</script>
<?php endif; ?>

</body>

</html>