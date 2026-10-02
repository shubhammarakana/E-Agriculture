<?php
// admin/add_chemical.php
include '../db_connect.php';
session_start();

// Role Check: Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = "";
$message_type = ""; // success or error
$edit_mode = false;
$chem_id = "";
$c = [];

// Handle Edit Mode
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $chem_id = $_GET['edit'];
    // Admin can edit any chemical
    $res = $conn->query("SELECT * FROM chemicals WHERE id='$chem_id'");
    if ($res->num_rows > 0) {
        $c = $res->fetch_assoc();
    } else {
        die("Product not found.");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seller_id = $_SESSION['user_id']; // Posted as Admin

    $name = $conn->real_escape_string($_POST['name']);
    $brand = $conn->real_escape_string($_POST['brand']);
    $category = $conn->real_escape_string($_POST['category']);
    $ingredient = $conn->real_escape_string($_POST['active_ingredient']);
    $pack_size = $conn->real_escape_string($_POST['pack_size']);
    $crop_suitability = $conn->real_escape_string($_POST['crop_suitability']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $safety_label = $_POST['safety_label'];
    $description = $conn->real_escape_string($_POST['description']);
    $status = $_POST['status'];

    $image_path = $edit_mode ? $c['image_path'] : "";

    // Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../images/";
        $file_ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid('chem_') . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = "images/" . $new_filename;
        }
    }

    if ($edit_mode) {
        $sql = "UPDATE chemicals SET 
                name='$name', brand='$brand', category='$category', active_ingredient='$ingredient', 
                pack_size='$pack_size', crop_suitability='$crop_suitability', price='$price', 
                stock='$stock', safety_label='$safety_label', description='$description', 
                image_path='$image_path', status='$status' 
                WHERE id='$chem_id'";
        $msg_text = "Chemical updated successfully!";
    } else {
        $sql = "INSERT INTO chemicals (seller_id, name, brand, category, active_ingredient, pack_size, crop_suitability, price, stock, safety_label, description, image_path, status) 
                VALUES ('$seller_id', '$name', '$brand', '$category', '$ingredient', '$pack_size', '$crop_suitability', '$price', '$stock', '$safety_label', '$description', '$image_path', '$status')";
        $msg_text = "Chemical added successfully!";
    }

    if ($conn->query($sql) === TRUE) {
        $_SESSION['success_msg'] = $msg_text;
        header("Location: manage_chemicals.php");
        exit();
    } else {
        $message = "Error: " . $conn->error;
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php
$page = 'add_chemical';
$page_title = $edit_mode ? 'Edit Chemical' : 'Add New Chemical';
include '../includes/main_header.php';

// Helper to get value for retention
function getVal($field, $edit_mode, $data, $post)
{
    if (isset($post[$field]))
        return htmlspecialchars($post[$field]);
    if ($edit_mode && isset($data[$field]))
        return htmlspecialchars($data[$field]);
    return '';
}
?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">

    <div style="max-width: 1000px; margin: 0 auto; display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">

        <!-- Left Side: Form -->
        <div class="glass-panel" style="flex: 2; min-width: 320px; position: relative; overflow: hidden;">
            <div
                style="background: linear-gradient(90deg, #6366f1, #818cf8); height: 5px; width: 100%; position: absolute; top: 0; left: 0;">
            </div>

            <h2 style="margin-bottom: 0.5rem; color: #4338ca; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-flask"></i> <?php echo $edit_mode ? 'Edit Product' : 'List New Chemical'; ?>
            </h2>
            <p style="color: #666; margin-bottom: 2rem; font-size: 0.95rem;">Manage the chemical inventory.</p>

            <form action="" method="POST" enctype="multipart/form-data" id="addChemForm">

                <!-- Basic Info -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-dashboard">
                        <label>Product Name</label>
                        <div class="input-group-glass">
                            <i class="fas fa-tag"></i>
                            <input type="text" name="name" value="<?php echo getVal('name', $edit_mode, $c, $_POST); ?>"
                                placeholder="e.g. Urea 46%" required>
                        </div>
                    </div>
                    <div class="form-group-dashboard">
                        <label>Brand Name</label>
                        <div class="input-group-glass">
                            <i class="fas fa-trademark"></i>
                            <input type="text" name="brand"
                                value="<?php echo getVal('brand', $edit_mode, $c, $_POST); ?>" placeholder="e.g. IFFCO"
                                required>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-dashboard">
                        <label>Category</label>
                        <div class="input-group-glass">
                            <i class="fas fa-list"></i>
                            <select name="category" required>
                                <option value="">Select Type</option>
                                <?php
                                $cats = ['Fertilizer', 'Pesticide', 'Herbicide', 'Fungicide', 'Nutrient', 'Other'];
                                $selected_cat = getVal('category', $edit_mode, $c, $_POST);
                                foreach ($cats as $cat) {
                                    $sel = ($selected_cat == $cat) ? 'selected' : '';
                                    echo "<option value='$cat' $sel>$cat</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group-dashboard">
                        <label>Size / Pack</label>
                        <div class="input-group-glass">
                            <i class="fas fa-box"></i>
                            <input type="text" name="pack_size"
                                value="<?php echo getVal('pack_size', $edit_mode, $c, $_POST); ?>"
                                placeholder="e.g. 50 kg bag, 1L bottle" required>
                        </div>
                    </div>
                </div>

                <!-- Price & Stock -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-dashboard">
                        <label>Price (₹)</label>
                        <div class="input-group-glass">
                            <i class="fas fa-rupee-sign"></i>
                            <input type="number" step="0.01" name="price"
                                value="<?php echo getVal('price', $edit_mode, $c, $_POST); ?>" placeholder="0.00"
                                required>
                        </div>
                    </div>
                    <div class="form-group-dashboard">
                        <label>Stock Qty</label>
                        <div class="input-group-glass">
                            <i class="fas fa-layer-group"></i>
                            <input type="number" name="stock"
                                value="<?php echo getVal('stock', $edit_mode, $c, $_POST); ?>"
                                placeholder="Available Units" required>
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="form-group-dashboard">
                    <label>Active Ingredient</label>
                    <div class="input-group-glass">
                        <i class="fas fa-dna"></i>
                        <input type="text" name="active_ingredient"
                            value="<?php echo getVal('active_ingredient', $edit_mode, $c, $_POST); ?>"
                            placeholder="e.g. Nitrogen 46%, Glyphosate 41%" required>
                    </div>
                </div>

                <div class="form-group-dashboard">
                    <label>Crop Suitability</label>
                    <div class="input-group-glass">
                        <i class="fas fa-seedling"></i>
                        <input type="text" name="crop_suitability"
                            value="<?php echo getVal('crop_suitability', $edit_mode, $c, $_POST); ?>"
                            placeholder="e.g. Wheat, Rice, Cotton (Comma separated)" required>
                    </div>
                </div>

                <div class="form-group-dashboard">
                    <label>Safety Label</label>
                    <div class="input-group-glass">
                        <i class="fas fa-shield-alt"></i>
                        <select name="safety_label" required>
                            <?php
                            $labels = [
                                'Green' => 'Green (Safe)',
                                'Blue' => 'Blue (Moderate)',
                                'Yellow' => 'Yellow (Toxic)',
                                'Red' => 'Red (Extreme Toxic)'
                            ];
                            $selected_label = getVal('safety_label', $edit_mode, $c, $_POST);
                            foreach ($labels as $val => $lbl) {
                                $sel = ($selected_label == $val) ? 'selected' : '';
                                $style = "";
                                if ($val == 'Green')
                                    $style = "color: green;";
                                if ($val == 'Blue')
                                    $style = "color: blue;";
                                if ($val == 'Yellow')
                                    $style = "color: #bf9000;";
                                if ($val == 'Red')
                                    $style = "color: red;";
                                echo "<option value='$val' style='$style font-weight: bold;' $sel>$lbl</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group-dashboard">
                    <label>Description & Dosage</label>
                    <div class="input-group-glass">
                        <i class="fas fa-align-left" style="align-self: flex-start; margin-top: 10px;"></i>
                        <textarea name="description" rows="4"
                            placeholder="Enter product description and dosage instructions..." required
                            style="width: 100%; border: none; background: transparent; outline: none; padding: 10px;"><?php echo getVal('description', $edit_mode, $c, $_POST); ?></textarea>
                    </div>
                </div>

                <!-- Status -->
                <div class="form-group-dashboard">
                    <label>Listing Status</label>
                    <div class="input-radio-group">
                        <?php $curr_status = getVal('status', $edit_mode, $c, $_POST) ?: 'Active'; ?>
                        <label class="radio-card">
                            <input type="radio" name="status" value="Active" <?php echo ($curr_status == 'Active') ? 'checked' : ''; ?>>
                            <div class="radio-content status-active-card">
                                <i class="fas fa-check-circle"></i>
                                <span>Active</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="status" value="Inactive" <?php echo ($curr_status == 'Inactive') ? 'checked' : ''; ?>>
                            <div class="radio-content status-inactive-card">
                                <i class="fas fa-pause-circle"></i>
                                <span>Inactive</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Image Upload (Drag & Drop) -->
                <div class="form-group-dashboard">
                    <label>Product Image</label>
                    <div class="upload-zone" id="uploadZone">
                        <div class="upload-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag & Drop image here or <span style="color: #3b82f6;">Browse</span></p>
                            <span class="file-info">Supports JPG, PNG</span>
                        </div>
                        <input type="file" name="image" id="chemImage" accept="image/*" <?php echo $edit_mode ? '' : 'required'; ?>>
                        <!-- Preview -->
                        <div id="imagePreview" class="image-preview"
                            style="<?php echo ($edit_mode && !empty($c['image_path'])) ? 'display:flex;' : 'display:none;'; ?>">
                            <img src="<?php echo ($edit_mode && !empty($c['image_path'])) ? '../' . $c['image_path'] : ''; ?>"
                                alt="Preview">
                            <button type="button" class="remove-btn"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit-glass"
                    style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
                    <span><?php echo $edit_mode ? 'Update Chemical' : 'Publish Listing'; ?></span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>

        <!-- Right Side: Tips card -->
        <div style="flex: 1; min-width: 250px;">
            <div class="glass-panel"
                style="background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); border: 1px solid #a5b4fc;">
                <h4 style="color: #4338ca; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-shield-alt"></i> Admin Control
                </h4>
                <ul style="list-style: none; padding: 0; margin-top: 1rem; color: #3730a3; font-size: 0.9rem;">
                    <li style="margin-bottom: 10px; display: flex; gap: 10px;">
                        <i class="fas fa-check" style="margin-top: 4px;"></i>
                        <span>Verify chemical contents.</span>
                    </li>
                    <li style="margin-bottom: 10px; display: flex; gap: 10px;">
                        <i class="fas fa-check" style="margin-top: 4px;"></i>
                        <span>Ensure compliance.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    /* Admin Theme Styles */
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
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        background: white;
    }

    .input-group-glass i {
        color: #9ca3af;
        margin-right: 10px;
    }

    .input-group-glass input,
    .input-group-glass select {
        border: none;
        background: transparent;
        width: 100%;
        padding: 10px 0;
        font-size: 1rem;
        outline: none;
        color: #333;
    }

    .input-radio-group {
        display: flex;
        gap: 1rem;
    }

    .radio-card {
        flex: 1;
        cursor: pointer;
        position: relative;
    }

    .radio-card input {
        position: absolute;
        opacity: 0;
    }

    .radio-content {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.5);
        transition: all 0.2s;
        font-weight: 600;
        color: #666;
    }

    .status-active-card i {
        color: #22c55e;
    }

    .status-inactive-card i {
        color: #dc2626;
    }

    .radio-card input:checked+.status-active-card {
        border-color: #22c55e;
        background: #f0fdf4;
        color: #166534;
        box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.15);
    }

    .radio-card input:checked+.status-inactive-card {
        border-color: #dc2626;
        background: #fef2f2;
        color: #991b1b;
        box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.15);
    }

    .upload-zone {
        border: 2px dashed #d1d5db;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        background: rgba(255, 255, 255, 0.4);
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
    }

    .upload-zone:hover {
        border-color: #6366f1;
        background: rgba(224, 231, 255, 0.5);
    }

    .upload-zone input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }

    .upload-content i {
        font-size: 2.5rem;
        color: #9ca3af;
        margin-bottom: 10px;
    }

    .file-info {
        display: block;
        font-size: 0.8rem;
        color: #9ca3af;
        margin-top: 5px;
    }

    .image-preview {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: white;
        border-radius: 13px;
        padding: 5px;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-preview img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 8px;
        object-fit: contain;
    }

    .remove-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.6);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        z-index: 20;
    }

    .btn-submit-glass {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 2rem;
        transition: all 0.3s;
        box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3);
    }

    .btn-submit-glass:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
    }
</style>

<script>
    // SweetAlert error message if any
    <?php if ($message && $message_type == 'error'): ?>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?php echo addslashes($message); ?>',
            confirmButtonColor: '#4338ca'
        });
    <?php endif; ?>

    // File Upload Preview
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('chemImage');
    const preview = document.getElementById('imagePreview');
    const previewImg = preview.querySelector('img');
    const removeBtn = preview.querySelector('.remove-btn');
    const uploadContent = uploadZone.querySelector('.upload-content');

    fileInput.addEventListener('change', function (e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                preview.style.display = 'flex';
                uploadContent.style.opacity = '0';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    removeBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        fileInput.value = '';
        preview.style.display = 'none';
        uploadContent.style.opacity = '1';
    });
</script>

<?php include '../includes/main_footer.php'; ?>

</html>