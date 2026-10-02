<?php
// vendor/products.php
include '../db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

$page = 'products';
$page_title = 'My Crops';
include '../includes/main_header.php';

// Fetch Vendor's Crops
$vendor_id = $_SESSION['user_id'];
$sql = "SELECT * FROM crops WHERE farmer_id = '$vendor_id' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="margin:0; color: #166534;">My Crops</h2>
            <p style="color: #64748b; margin-top: 5px;">Manage your marketplace listings</p>
        </div>
        <a href="add_product.php" class="btn btn-primary" style="background: linear-gradient(135deg, #16a34a, #15803d); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; display:flex; align-items:center; gap:8px;">
            <i class="fas fa-plus"></i> Add New Crop
        </a>
    </div>

    <!-- Stats Review (Optional) -->
    <!-- <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:15px; margin-bottom: 2rem;"> ... </div> -->

    <div class="glass-panel" style="background:white; border-radius:12px; border:1px solid #e2e8f0; overflow:hidden;">
        <?php if (isset($_GET['msg'])): ?>
            <div style="padding: 15px; background: #dcfce7; color: #166534; border-bottom: 1px solid #bbf7d0;">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <div style="overflow-x: auto;">
            <table class="glass-table" style="width:100%; border-collapse:collapse;">
                <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <tr>
                        <th style="padding: 15px; text-align: left; color: #475569; font-weight: 600;">Crop</th>
                        <th style="padding: 15px; text-align: left; color: #475569; font-weight: 600;">Category</th>
                        <th style="padding: 15px; text-align: left; color: #475569; font-weight: 600;">Price</th>
                        <th style="padding: 15px; text-align: left; color: #475569; font-weight: 600;">Stock Status</th>
                        <th style="padding: 15px; text-align: right; color: #475569; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php 
                                $img = !empty($row["image_path"]) ? $row["image_path"] : 'images/default-crop.png';
                                // Fix root path
                                if (!preg_match('/^https?:\/\//', $img) && strpos($img, '../') !== 0) {
                                    $img = '../' . $img;
                                }
                            ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 15px;">
                                    <a href="../crop_details.php?id=<?php echo $row['id']; ?>" style="text-decoration: none;">
                                        <div style="display: flex; align-items: center; gap: 15px;">
                                            <div style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; background: #f1f5f9;">
                                                <img src="<?php echo $img; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <span style="font-weight: 600; color: #334155;"><?php echo htmlspecialchars($row['name']); ?></span>
                                        </div>
                                    </a>
                                </td>
                                <td style="padding: 15px; color: #64748b;"><?php echo htmlspecialchars($row['category']); ?></td>
                                <td style="padding: 15px; font-weight: 600; color: #16a34a;">₹<?php echo $row['price']; ?> <span style="font-size:0.8em; color:#94a3b8; font-weight:normal;">/ <?php echo $row['price_unit']; ?></span></td>
                                <td style="padding: 15px;">
                                    <?php 
                                        $badge_bg = ($row['stock_status'] == 'In Stock') ? '#dcfce7' : '#fee2e2';
                                        $badge_col = ($row['stock_status'] == 'In Stock') ? '#166534' : '#991b1b';
                                    ?>
                                    <span style="background: <?php echo $badge_bg; ?>; color: <?php echo $badge_col; ?>; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                        <?php echo $row['stock_status']; ?>
                                    </span>
                                </td>
                                <td style="padding: 15px; text-align: right;">
                                    <a href="../crop_details.php?id=<?php echo $row['id']; ?>" style="color: #10b981; background: #d1fae5; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; margin-right: 5px; text-decoration: none;" title="View">
                                        <i class="fas fa-eye" style="font-size: 0.9rem;"></i>
                                    </a>
                                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" style="color: #6366f1; background: #e0e7ff; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; margin-right: 5px; text-decoration: none;" title="Edit">
                                        <i class="fas fa-edit" style="font-size: 0.9rem;"></i>
                                    </a>
                                    <a href="delete_product.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this crop?');" style="color: #ef4444; background: #fee2e2; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none;" title="Delete">
                                        <i class="fas fa-trash" style="font-size: 0.9rem;"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="padding: 4rem; text-align: center; color: #94a3b8;">
                                <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
                                <p>No crops found. Start selling today!</p>
                                <a href="add_product.php" style="color: #16a34a; font-weight: 600; text-decoration: none;">Add your first crop</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../includes/main_footer.php'; ?>
