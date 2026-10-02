<?php
// admin/orders.php
include '../db_connect.php';
session_start();

// Admin Authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$page = 'orders';
$page_title = 'Order Management';

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    $comments = $_POST['comments'] ?? '';

    // Update Orders Table
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        // Insert into Tracking
        $track_stmt = $conn->prepare("INSERT INTO delivery_tracking (order_id, status, updated_by, comments) VALUES (?, ?, 'Admin', ?)");
        $track_stmt->bind_param("iss", $order_id, $new_status, $comments);
        $track_stmt->execute();
        $success_msg = "Order #$order_id updated to $new_status successfully.";
    } else {
        $error_msg = "Failed to update order.";
    }
}

include '../includes/main_header.php';
?>

<style>
    /* Styling Overrides for Premium Feel */
    body {
        background-color: #f3f4f6;
    }
    
    .page-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        padding: 2.5rem 0 4rem;
        color: white;
        margin-bottom: -3rem;
        border-radius: 0 0 30px 30px;
        box-shadow: 0 10px 30px -10px rgba(16, 185, 129, 0.4);
    }

    .main-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.05);
        border-radius: 20px;
        overflow: hidden;
    }

    .search-control {
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        padding: 0.7rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background-color: #f9fafb;
    }

    .search-control:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        background-color: white;
    }

    .table thead th {
        background-color: #f9fafb;
        color: #6b7280;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
        padding: 1rem 1.5rem;
        border-bottom: 2px solid #f3f4f6;
    }

    .table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        color: #374151;
        font-size: 0.9rem;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f0fdf4;
        transition: background-color 0.2s ease;
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .status-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: currentColor;
    }

    .status-pending { background-color: #fef3c7; color: #d97706; }
    .status-accepted { background-color: #e0f2fe; color: #0284c7; }
    .status-packed { background-color: #e0e7ff; color: #4f46e5; }
    .status-shipped { background-color: #ddd6fe; color: #7c3aed; }
    .status-delivered { background-color: #dcfce7; color: #16a34a; }
    .status-cancelled { background-color: #fee2e2; color: #dc2626; }

    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: none;
    }
    
    .btn-action-view { background-color: #e0f2fe; color: #0284c7; }
    .btn-action-view:hover { background-color: #0284c7; color: white; transform: translateY(-2px); }
    
    .btn-action-edit { background-color: #d1fae5; color: #059669; }
    .btn-action-edit:hover { background-color: #059669; color: white; transform: translateY(-2px); }

    .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .modal-header {
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        border-radius: 20px 20px 0 0;
        padding: 1.5rem;
    }

    /* Stats Cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
</style>

<div class="page-header">
    <div class="container-fluid px-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold mb-2">Order Management</h1>
                <p class="mb-0 opacity-90">Track, manage, and process all customer orders in one place.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-light text-success fw-bold px-4 shadow-sm" onclick="exportReport('excel')">
                    <i class="fas fa-file-excel me-2"></i> Export Excel
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-5" style="margin-bottom: 5rem;">
    
    <!-- Stats Row -->
    <?php
    $total_orders = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
    $pending_orders = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='Pending'")->fetch_assoc()['c'];
    $delivered_orders = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='Delivered'")->fetch_assoc()['c'];
    ?>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-blue-100 text-primary" style="background:#e0f2fe;">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <h6 class="text-secondary text-uppercase mb-1" style="font-size:0.75rem; letter-spacing:0.05em;">Total Orders</h6>
                    <h2 class="mb-0 fw-bold text-gray-800"><?php echo $total_orders; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-yellow-100 text-warning" style="background:#fef3c7;">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h6 class="text-secondary text-uppercase mb-1" style="font-size:0.75rem; letter-spacing:0.05em;">Pending Actions</h6>
                    <h2 class="mb-0 fw-bold text-gray-800"><?php echo $pending_orders; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-green-100 text-success" style="background:#dcfce7;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h6 class="text-secondary text-uppercase mb-1" style="font-size:0.75rem; letter-spacing:0.05em;">Completed</h6>
                    <h2 class="mb-0 fw-bold text-gray-800"><?php echo $delivered_orders; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-card">
        <!-- Filters Header -->
        <div class="p-4 border-bottom border-light">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 ps-3" style="border-radius: 12px 0 0 12px; border-color: #e5e7eb;">
                            <i class="fas fa-search text-secondary"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control search-control border-start-0 ps-2" style="border-radius: 0 12px 12px 0;" placeholder="Search Orders, Buyers, or Farmers...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select search-control">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Accepted">Accepted</option>
                        <option value="Packed">Packed</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Delivered">Delivered</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <!-- <div class="col-md-3 ms-auto text-end text-muted small">
                    Showing latest 50 orders
                </div> -->
            </div>
        </div>

        <!-- Order Table -->
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Order ID</th>
                        <th>Customer Details</th>
                        <th>Farmer Details</th>
                        <th>Total Amount</th>
                        <th>Ordered Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    <?php
                    // Fetch Orders
                    $query = "SELECT o.*, b.fullname as buyer_name, f.fullname as farmer_name 
                              FROM orders o 
                              LEFT JOIN users b ON o.buyer_id = b.id 
                              LEFT JOIN users f ON o.farmer_id = f.id 
                              ORDER BY o.created_at DESC";
                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $status_lower = strtolower($row['status']);
                            $status_class = "status-" . $status_lower;
                            
                            echo "<tr class='order-row' data-status='{$row['status']}' data-search='{$row['id']} {$row['buyer_name']} {$row['farmer_name']}'>
                                <td class='ps-4 fw-bold text-primary'>#{$row['id']}</td>
                                <td>
                                    <div class='fw-semibold'>{$row['buyer_name']}</div>
                                    <div class='text-muted small' style='font-size:0.8rem;'>Buyer</div>
                                </td>
                                <td>
                                    <div class='fw-semibold'>{$row['farmer_name']}</div>
                                    <div class='text-muted small' style='font-size:0.8rem;'>Farmer</div>
                                </td>
                                <td class='fw-bold'>₹" . number_format($row['total_amount'], 2) . "</td>
                                <td class='text-muted'>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
                                <td><span class='status-badge {$status_class}'>{$row['status']}</span></td>
                                <td class='text-end pe-4'>
                                    <button class='btn-action btn-action-view me-2' title='View Details' onclick='viewOrder({$row['id']})'>
                                        <i class='fas fa-eye'></i>
                                    </button>
                                    <button class='btn-action btn-action-edit' title='Update Status' onclick='updateStatus({$row['id']}, \"{$row['status']}\")'>
                                        <i class='fas fa-pen'></i>
                                    </button>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-5 text-muted'>
                                <i class='fas fa-box-open fa-3x mb-3 text-light-gray'></i><br>No orders found.
                              </td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Order Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="orderModalContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-success" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="order_id" id="update_order_id">
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">Current Status</label>
                        <input type="text" class="form-control bg-light" id="current_status_display" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">New Status</label>
                        <select name="status" class="form-select search-control" required>
                            <option value="Accepted">Accepted</option>
                            <option value="Packed">Packed</option>
                            <option value="Shipped">Shipped</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">Comments (Optional)</label>
                        <textarea name="comments" class="form-control search-control" rows="3" placeholder="Add tracking number or reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Live Search
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('.order-row');
        rows.forEach(row => {
            let text = row.getAttribute('data-search').toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // Status Filter
    document.getElementById('statusFilter').addEventListener('change', function() {
        let filter = this.value;
        let rows = document.querySelectorAll('.order-row');
        rows.forEach(row => {
            let status = row.getAttribute('data-status');
            row.style.display = (filter === '' || status === filter) ? '' : 'none';
        });
    });

    // View Order Modal
    function viewOrder(id) {
        var myModal = new bootstrap.Modal(document.getElementById('viewOrderModal'));
        myModal.show();
        
        // Fetch Details via AJAX
        fetch('get_order_details.php?id=' + id)
            .then(response => response.text())
            .then(html => {
                document.getElementById('orderModalContent').innerHTML = html;
            });
    }

    // Update Status Modal
    function updateStatus(id, currentStatus) {
        document.getElementById('update_order_id').value = id;
        document.getElementById('current_status_display').value = currentStatus;
        var myModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
        myModal.show();
    }

    // Export Placeholders
    function exportReport(type) {
        alert("Export to " + type + " feature coming soon!");
    }
</script>

<?php include '../includes/main_footer.php'; ?>
