<?php
// edit_crop.php
include '../db_connect.php';
session_start();

// Check if user is logged in and is a farmer or admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['farmer', 'admin'])) {
    header("Location: ../login.php");
    exit();
}

$message = "";
$msg_type = "";
$crop = null;

// Get crop details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM crops WHERE id='$id'");
    if ($result->num_rows > 0) {
        $crop = $result->fetch_assoc();
    } else {
        header("Location: manage_crops.php");
        exit();
    }
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $price = $_POST['price'];
    $price_unit = $_POST['price_unit'];
    $description = $conn->real_escape_string($_POST['description']);
    $stock_status = $_POST['stock_status'];

    // Handle Image Upload (Optional Update)
    $image_path = $crop['image_path']; // Default to existing
    if (isset($_FILES['crop_image']) && $_FILES['crop_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir))
            mkdir($target_dir, 0777, true);

        $file_ext = strtolower(pathinfo($_FILES["crop_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["crop_image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    $sql = "UPDATE crops SET 
            name='$name', 
            price='$price', 
            price_unit='$price_unit', 
            description='$description', 
            stock_status='$stock_status', 
            image_path='$image_path' 
            WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        $message = "Crop updated successfully!";
        $msg_type = "success";
        // Refresh crop data
        $crop['name'] = $name;
        $crop['price'] = $price;
        $crop['price_unit'] = $price_unit;
        $crop['description'] = $description;
        $crop['stock_status'] = $stock_status;
        $crop['image_path'] = $image_path;

        echo "<script>setTimeout(()=>{ window.location.href='manage_crops.php'; }, 1500);</script>";
    } else {
        $message = "Error: " . $conn->error;
        $msg_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php
$page_title = 'Edit Crop';
include '../includes/main_header.php';
?>
<!-- ... -->
<div style="max-width: 800px; margin: 40px auto; min-height: 80vh;">

    <div class="glass-panel">
        <h2 style="margin-bottom: 25px; color: var(--primary); display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-edit"></i> Edit Listing
        </h2>

        <?php if ($message != ""): ?>
            <div
                style="background: <?php echo ($msg_type == 'success') ? 'rgba(76,175,80,0.2)' : 'rgba(244,67,54,0.2)'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="edit_crop.php?id=<?php echo $crop['id']; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $crop['id']; ?>">

            <div class="form-group-dashboard">
                <label style="font-weight: 500; margin-bottom: 8px; display: block; color: #555;">Crop Name</label>
                <input type="text" name="name" class="form-control-glass" value="<?php echo $crop['name']; ?>" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group-dashboard">
                    <label style="font-weight: 500; margin-bottom: 8px; display: block; color: #555;">Price</label>
                    <input type="number" name="price" step="0.01" class="form-control-glass"
                        value="<?php echo $crop['price']; ?>" required>
                </div>
                <div class="form-group-dashboard">
                    <label style="font-weight: 500; margin-bottom: 8px; display: block; color: #555;">Per Unit</label>
                    <select name="price_unit" class="form-control-glass">
                        <option value="kg" <?php echo ($crop['price_unit'] == 'kg') ? 'selected' : ''; ?>>per Kg</option>
                        <option value="ton" <?php echo ($crop['price_unit'] == 'ton') ? 'selected' : ''; ?>>per Ton
                        </option>
                        <option value="quintal" <?php echo ($crop['price_unit'] == 'quintal') ? 'selected' : ''; ?>>per
                            Quintal</option>
                        <option value="piece" <?php echo ($crop['price_unit'] == 'piece') ? 'selected' : ''; ?>>per Piece
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group-dashboard">
                <label style="font-weight: 500; margin-bottom: 8px; display: block; color: #555;">Stock Status</label>
                <select name="stock_status" class="form-control-glass">
                    <option value="In Stock" <?php echo ($crop['stock_status'] == 'In Stock') ? 'selected' : ''; ?>>In
                        Stock</option>
                    <option value="Out of Stock" <?php echo ($crop['stock_status'] == 'Out of Stock') ? 'selected' : ''; ?>>Out of Stock</option>
                    <option value="Harvesting Soon" <?php echo ($crop['stock_status'] == 'Harvesting Soon') ? 'selected' : ''; ?>>Harvesting Soon</option>
                </select>
            </div>

            <div class="form-group-dashboard">
                <label style="font-weight: 500; margin-bottom: 8px; display: block; color: #555;">Description (Optional)</label>
                <textarea name="description" class="form-control-glass"
                    rows="4"><?php echo $crop['description']; ?></textarea>
            </div>

            <div class="form-group-dashboard">
                <label style="font-weight: 500; margin-bottom: 8px; display: block; color: #555;">Update Image (Leave empty to keep current)</label>
                <br>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <img src="<?php echo $crop['image_path'] ?: 'images/default-crop.png'; ?>"
                        style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <input type="file" name="crop_image" class="form-control-glass" accept="image/*" style="flex: 1;">
                </div>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 15px;">
                <button type="submit" class="btn-primary-glass" style="flex: 1;">
                    <i class="fas fa-save"></i> Update Listing
                </button>
                <a href="manage_crops.php" style="flex: 1; text-align: center; background: rgba(255,255,255,0.6); color: #333; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid rgba(0,0,0,0.1); transition: all 0.3s;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
</div>
<?php include '../includes/main_footer.php'; ?>

</html>