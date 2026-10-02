<?php
// admin/manage_chemicals.php
include '../db_connect.php';
session_start();

// Role Check: Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$page = 'manage_chemicals';
$page_title = 'Manage Chemicals';

// Fetch All Chemicals with Seller Info
$sql = "SELECT c.*, u.fullname as seller_name FROM chemicals c LEFT JOIN users u ON c.seller_id = u.id ORDER BY c.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<?php
include '../includes/main_header.php';
?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin:0; color: #4338ca;">Chemical Inventory</h2>
        <a href="add_chemical.php" class="btn btn-primary-glass"
            style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
            <i class="fas fa-plus"></i> Add New
        </a>
    </div>

    <div class="glass-table-container">
        <?php if (isset($_SESSION['success_msg'])): ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?php echo $_SESSION['success_msg']; ?>',
                    confirmButtonColor: '#4338ca'
                });
            </script>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'deleted'): ?>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: 'Chemical deleted successfully.',
                        confirmButtonColor: '#4338ca'
                    });
                </script>
            <?php endif; ?>
        <?php endif; ?>

        <table class="glass-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Seller</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $img = !empty($row["image_path"]) ? $row["image_path"] : 'images/default_chem.png';
                        if (strpos($img, 'images/') === 0 && file_exists('../' . $img)) {
                            $img = '../' . $img;
                        } elseif (strpos($img, 'http') !== 0 && !file_exists($img)) {
                            $img = '../images/default_chem.png';
                        }

                        $status_color = ($row['status'] == 'Active') ? '#dcfce7' : '#fee2e2';
                        $status_text_color = ($row['status'] == 'Active') ? '#166534' : '#991b1b';

                        echo "<tr>
                                <td><img src='$img' style='width:50px; height:50px; object-fit:cover; border-radius:8px;'></td>
                                <td>
                                    <div style='font-weight:600; color:#1f2937;'>{$row['name']}</div>
                                    <div style='font-size:0.85rem; color:#6b7280;'>{$row['brand']} ({$row['category']})</div>
                                </td>
                                <td>" . htmlspecialchars($row['seller_name'] ?? 'Admin/Unknown') . "</td>
                                <td>₹{$row['price']}</td>
                                <td>{$row['stock']}</td>
                                <td><span style='background: $status_color; color: $status_text_color; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;'>{$row['status']}</span></td>
                                <td>
                                    <a href='add_chemical.php?edit={$row['id']}' style='color:#6366f1; margin-right:10px;'><i class='fas fa-edit'></i></a>
                                    <a href='delete_chemical.php?id={$row['id']}' onclick='return confirm(\"Delete this chemical listing?\");' style='color:#ef4444;'><i class='fas fa-trash'></i></a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center; padding: 2rem;'>No chemicals found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Admin Table Styles */
    .glass-table-container {
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        overflow: hidden;
    }

    .glass-table {
        width: 100%;
        border-collapse: collapse;
    }

    .glass-table th {
        padding: 15px 20px;
        text-align: left;
        background: rgba(255, 255, 255, 0.7);
        color: #555;
        font-weight: 600;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .glass-table td {
        padding: 15px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        color: #444;
        vertical-align: middle;
    }

    .glass-table tr:hover {
        background: rgba(255, 255, 255, 0.4);
    }

    .btn-primary-glass {
        padding: 10px 20px;
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }

    .btn-primary-glass:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
</style>
<?php include '../includes/main_footer.php'; ?>

</html>