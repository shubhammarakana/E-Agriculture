<?php
// vendor/edit_product.php
include '../db_connect.php';
session_start();

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$vendor_id = $_SESSION['user_id'];
$message = "";

// Fetch Existing Data
$sql = "SELECT * FROM crops WHERE id = '$id' AND farmer_id = '$vendor_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Product not found or access denied.";
    exit();
}

$product = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $_POST['price'];
    $price_unit = $conn->real_escape_string($_POST['price_unit']);
    $stock_status = $_POST['stock_status'];

    // Image Upload Logic
    $image_path = $product['image_path']; // Default to existing
    if (isset($_FILES['crop_image']) && $_FILES['crop_image']['error'] == 0) {
        $target_dir = "../images/";
        $file_ext = strtolower(pathinfo($_FILES["crop_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid('crop_') . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        $db_image_path = "images/" . $new_filename;

        if (move_uploaded_file($_FILES["crop_image"]["tmp_name"], $target_file)) {
            $image_path = $db_image_path;
            
            // Optional: Delete old image if it exists and isn't default
            // if (file_exists("../" . $product['image_path']) && strpos($product['image_path'], 'default') === false) {
            //     unlink("../" . $product['image_path']);
            // }
        }
    }

    $update_sql = "UPDATE crops SET 
                   name='$name', category='$category', description='$description', 
                   price='$price', price_unit='$price_unit', image_path='$image_path', 
                   stock_status='$stock_status' 
                   WHERE id='$id' AND farmer_id='$vendor_id'";

    if ($conn->query($update_sql) === TRUE) {
        $message = "Product updated successfully!";
        // Refresh data
        $product = $conn->query($sql)->fetch_assoc();
    } else {
        $message = "Error: " . $conn->error;
    }
}

$page = 'products';
$page_title = 'Edit Product';
include '../includes/main_header.php';
?>

<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    
    <div style="max-width: 900px; margin: 0 auto; display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
        
        <!-- Left Side: Form -->
        <div class="glass-panel" style="flex: 2; min-width: 320px; position: relative; overflow: hidden;">
            <div style="background: linear-gradient(90deg, #16a34a, #15803d); height: 5px; width: 100%; position: absolute; top: 0; left: 0;"></div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 style="margin:0; color: #166534; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-edit"></i> Edit Product
                </h2>
                <a href="products.php" class="btn btn-outline" style="border-radius: 8px; padding: 5px 15px; font-size: 0.9rem;">Back to List</a>
            </div>

            <?php if ($message): ?>
                <div class="alert-box <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                
                <!-- Name -->
                <div class="form-group-dashboard">
                    <label>Product Name</label>
                    <div class="input-group-glass">
                        <i class="fas fa-seedling"></i>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                </div>

                <!-- Category -->
                <div class="form-group-dashboard">
                    <label>Category</label>
                    <div class="input-group-glass">
                        <i class="fas fa-list"></i>
                        <select name="category" required>
                            <?php 
                            $cats = ['Grains', 'Pulses', 'Fruits', 'Vegetables'];
                            foreach ($cats as $cat) {
                                $sel = ($product['category'] == $cat) ? 'selected' : '';
                                echo "<option value='$cat' $sel>$cat</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group-dashboard">
                    <label>Description</label>
                    <div class="input-group-glass">
                        <i class="fas fa-align-left" style="align-self: flex-start; margin-top: 10px;"></i>
                        <textarea name="description" rows="3" required style="resize: none;"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                </div>

                <!-- Price & Unit -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-dashboard">
                        <label>Price</label>
                        <div class="input-group-glass">
                            <i class="fas fa-tag"></i>
                            <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
                        </div>
                    </div>
                    <div class="form-group-dashboard">
                        <label>Unit</label>
                        <div class="input-group-glass">
                            <i class="fas fa-weight-hanging"></i>
                            <select name="price_unit">
                                <?php 
                                $units = ['kg' => 'per kg', 'ton' => 'per ton', 'crate' => 'per crate', 'quintal' => 'per quintal'];
                                foreach ($units as $val => $label) {
                                    $sel = ($product['price_unit'] == $val) ? 'selected' : '';
                                    echo "<option value='$val' $sel>$label</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Stock Status -->
                <div class="form-group-dashboard">
                    <label>Availability</label>
                    <div class="input-radio-group">
                        <label class="radio-card">
                            <input type="radio" name="stock_status" value="In Stock" <?php echo ($product['stock_status'] == 'In Stock') ? 'checked' : ''; ?>>
                            <div class="radio-content">
                                <i class="fas fa-check-circle" style="color: #22c55e;"></i>
                                <span>In Stock</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="stock_status" value="Out of Stock" <?php echo ($product['stock_status'] == 'Out of Stock') ? 'checked' : ''; ?>>
                            <div class="radio-content">
                                <i class="fas fa-times-circle" style="color: #ef4444;"></i>
                                <span>Out of Stock</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="form-group-dashboard">
                    <label>Product Image</label>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <img src="<?php echo '../' . $product['image_path']; ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                        <div class="input-group-glass" style="flex: 1;">
                            <input type="file" name="crop_image" accept="image/*">
                        </div>
                    </div>
                    <small style="color: #64748b;">Leave empty to keep current image.</small>
                </div>

                <button type="submit" class="btn-submit-glass">
                    <span>Update Product</span>
                    <i class="fas fa-check"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Reusing styles from add_product.php */
    .input-group-glass {
        position: relative;
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 5px 15px;
        transition: all 0.3s;
    }
    .input-group-glass:focus-within {
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
        background: white;
    }
    .input-group-glass i { color: #9ca3af; margin-right: 10px; }
    .input-group-glass input, .input-group-glass select, .input-group-glass textarea {
        border: none; background: transparent; width: 100%; padding: 10px 0; font-size: 1rem; outline: none; color: #333; font-family: inherit;
    }
    .input-radio-group { display: flex; gap: 1rem; }
    .radio-card { flex: 1; cursor: pointer; position: relative; }
    .radio-card input { position: absolute; opacity: 0; }
    .radio-content {
        border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 8px; background: rgba(255,255,255,0.5); transition: all 0.2s; font-weight: 500; color: #555;
    }
    .radio-card input:checked + .radio-content {
        border-color: #16a34a; background: #f0fdf4; color: #16a34a; box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.1);
    }
    .btn-submit-glass {
        width: 100%; padding: 14px; border: none; border-radius: 12px; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); color: white; font-weight: 600; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 2rem; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.3);
    }
    .btn-submit-glass:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(22, 163, 74, 0.4); }
    .alert-box { padding: 12px; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.95rem; text-align: center; }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
</style>

<?php include '../includes/main_footer.php'; ?>
