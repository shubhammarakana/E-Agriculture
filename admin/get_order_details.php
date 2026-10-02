<?php
include '../db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo "Invalid Order ID";
    exit();
}

$order_id = intval($_GET['id']);

// Fetch Order Info
$sql = "SELECT o.*, 
        b.fullname as buyer_name, b.email as buyer_email, b.phone as buyer_phone, b.location as buyer_address,
        f.fullname as farmer_name, f.phone as farmer_phone
        FROM orders o 
        LEFT JOIN users b ON o.buyer_id = b.id 
        LEFT JOIN users f ON o.farmer_id = f.id 
        WHERE o.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit();
}

// Fetch Order Items
$sql_items = "SELECT oi.*, c.name as crop_name, c.image_path 
              FROM order_items oi 
              LEFT JOIN crops c ON oi.crop_id = c.id 
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();

// Tracking History
$sql_track = "SELECT * FROM delivery_tracking WHERE order_id = ? ORDER BY updated_at DESC";
$stmt_track = $conn->prepare($sql_track);
$stmt_track->bind_param("i", $order_id);
$stmt_track->execute();
$tracking = $stmt_track->get_result();

?>

<div class="order-details-content">
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-muted">Order Info</h6>
            <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
            <p><strong>Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
            <p><strong>Status:</strong> <span class="badge badge-<?php echo strtolower($order['status']); ?>"><?php echo $order['status']; ?></span></p>
        </div>
        <div class="col-md-6 text-end">
            <h6 class="text-muted">Customer Details</h6>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['buyer_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['buyer_email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['buyer_phone']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['buyer_address']); ?></p>
        </div>
    </div>

    <hr>

    <h6 class="mb-3">Order Items</h6>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $items->fetch_assoc()): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <?php if($item['image_path']): ?>
                                <img src="../<?php echo $item['image_path']; ?>" alt="img" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                            <?php endif; ?>
                            <span><?php echo htmlspecialchars($item['crop_name']); ?></span>
                        </div>
                    </td>
                    <td>₹<?php echo number_format($item['price_per_unit'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₹<?php echo number_format($item['subtotal'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <?php if ($tracking->num_rows > 0): ?>
    <div class="mt-4">
        <h6 class="mb-3">Tracking History</h6>
        <ul class="timeline">
            <?php while($track = $tracking->fetch_assoc()): ?>
            <li class="timeline-item">
                <p class="timeline-date"><?php echo date('d M Y, h:i A', strtotime($track['updated_at'])); ?></p>
                <p><strong><?php echo $track['status']; ?></strong></p>
                <?php if($track['comments']): ?>
                    <p class="text-muted small"><?php echo htmlspecialchars($track['comments']); ?></p>
                <?php endif; ?>
            </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <?php endif; ?>

</div>
