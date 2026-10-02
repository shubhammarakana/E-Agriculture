<?php
// manage_crops.php
include '../db_connect.php';
session_start();

// Check if user is logged in and is a farmer or admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['farmer', 'admin'])) {
    header("Location: ../login.php");
    exit();
}

$page = 'manage_crops';
$page_title = 'Manage Listings';

// For this demo, assuming all crops belong to the logged in farmer 
// (or updated schema would have farmer_id on crops table)
$sql = "SELECT * FROM crops ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<?php
$page_title = 'Manage Listings';
include '../includes/main_header.php';
?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin:0; color: #2E7D32;">My Listings</h2>
        <a href="add_crop.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Listing</a>
    </div>

    <div class="glass-table-container">
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'deleted'): ?>
                <div
                    style="background: rgba(76,175,80,0.2); color: #2e7d32; padding: 10px; border-radius: 8px; margin-bottom: 15px; border: 1px solid rgba(76,175,80,0.4);">
                    Listing deleted successfully.
                </div>
            <?php elseif ($_GET['msg'] == 'error_dependency'): ?>
                <div
                    style="background: rgba(244,67,54,0.2); color: #c62828; padding: 10px; border-radius: 8px; margin-bottom: 15px; border: 1px solid rgba(244,67,54,0.4);">
                    <strong>Cannot Delete:</strong> This crop has been ordered by customers. <br>
                    To stop selling it, please <strong>Edit</strong> and change status to "Out of Stock".
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <table class="glass-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Crop Name</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $img = !empty($row["image_path"]) ? $row["image_path"] : '../images/default-crop.png';
                        // Fix image path if it's relative
                        if (strpos($img, 'images/') === 0) {
                            $img = '../' . $img;
                        }

                        echo "<tr>
                                <td><img src='$img' style='width:50px; height:50px; object-fit:cover; border-radius:8px;'></td>
                                <td>{$row['name']}</td>
                                <td>\${$row['price']} / {$row['price_unit']}</td>
                                <td><span class='status-badge status-active'>{$row['stock_status']}</span></td>
                                <td>
                                    <a href='edit_crop.php?id={$row['id']}' style='color:var(--primary); margin-right:10px;'><i class='fas fa-edit'></i></a>
                                    <a href='delete_crop.php?id={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this listing?\");' style='color:var(--danger);'><i class='fas fa-trash'></i></a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center;'>No crops listed yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/main_footer.php'; ?>

</html>