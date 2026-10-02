<?php
// vendor/edit_chemical.php
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
$sql = "SELECT * FROM chemicals WHERE id = '$id' AND seller_id = '$vendor_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Product not found or access denied.";
    exit();
}

$chem = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $image_path = $chem['image_path'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../images/";
        $file_ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid('chem_') . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = "images/" . $new_filename;
        }
    }

    $update_sql = "UPDATE chemicals SET 
                   name='$name', brand='$brand', category='$category', active_ingredient='$ingredient',
                   pack_size='$pack_size', crop_suitability='$crop_suitability', price='$price',
                   stock='$stock', safety_label='$safety_label', description='$description',
                   image_path='$image_path', status='$status'
                   WHERE id='$id' AND seller_id='$vendor_id'";

    if ($conn->query($update_sql) === TRUE) {
        $message = "Chemical updated successfully!";
        $chem = $conn->query($sql)->fetch_assoc(); // Refresh
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php
$page = 'chemicals';
$page_title = 'Edit Chemical';
include '../includes/main_header.php';
?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    
    <div style="max-width: 1000px; margin: 0 auto; display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
        
        <!-- Left Side: Form -->
        <div class="glass-panel" style="flex: 2; min-width: 320px; position: relative; overflow: hidden;">
            <div style="background: linear-gradient(90deg, #6366f1, #818cf8); height: 5px; width: 100%; position: absolute; top: 0; left: 0;"></div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 style="margin:0; color: #4338ca; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-edit"></i> Edit Chemical
                </h2>
                <a href="chemicals.php" class="btn btn-outline" style="border-radius: 8px; padding: 5px 15px; font-size: 0.9rem;">Back to List</a>
            </div>

            <?php if ($message): ?>
                <div class="alert-box <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                
                <!-- Basic Info -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-dashboard">
                        <label>Product Name</label>
                        <div class="input-group-glass">
                            <i class="fas fa-tag"></i>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($chem['name']); ?>" required>
                        </div>
                    </div>
                    <div class="form-group-dashboard">
                        <label>Brand Name</label>
                        <div class="input-group-glass">
                            <i class="fas fa-trademark"></i>
                            <input type="text" name="brand" value="<?php echo htmlspecialchars($chem['brand']); ?>" required>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-dashboard">
                        <label>Category</label>
                        <div class="input-group-glass">
                            <i class="fas fa-list"></i>
                            <select name="category" required>
                                <?php $cats = ['Fertilizer', 'Pesticide', 'Herbicide', 'Fungicide', 'Nutrient', 'Other']; 
                                foreach($cats as $cat) {
                                    $sel = ($chem['category'] == $cat) ? 'selected' : '';
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
                            <input type="text" name="pack_size" value="<?php echo htmlspecialchars($chem['pack_size']); ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Price & Stock -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-dashboard">
                        <label>Price (₹)</label>
                        <div class="input-group-glass">
                            <i class="fas fa-rupee-sign"></i>
                            <input type="number" step="0.01" name="price" value="<?php echo $chem['price']; ?>" required>
                        </div>
                    </div>
                    <div class="form-group-dashboard">
                        <label>Stock Qty</label>
                        <div class="input-group-glass">
                            <i class="fas fa-layer-group"></i>
                            <input type="number" name="stock" value="<?php echo $chem['stock']; ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="form-group-dashboard">
                    <label>Active Ingredient</label>
                    <div class="input-group-glass">
                        <i class="fas fa-dna"></i>
                        <input type="text" name="active_ingredient" value="<?php echo htmlspecialchars($chem['active_ingredient']); ?>" required>
                    </div>
                </div>

                <div class="form-group-dashboard">
                    <label>Crop Suitability</label>
                    <div class="input-group-glass">
                        <i class="fas fa-seedling"></i>
                        <input type="text" name="crop_suitability" value="<?php echo htmlspecialchars($chem['crop_suitability']); ?>" required>
                    </div>
                </div>
                
                 <div class="form-group-dashboard">
                    <label>Safety Label</label>
                     <div class="input-group-glass">
                        <i class="fas fa-shield-alt"></i>
                        <select name="safety_label" required>
                            <?php 
                            $labels = ['Green', 'Blue', 'Yellow', 'Red'];
                            foreach($labels as $lbl) {
                                $sel = ($chem['safety_label'] == $lbl) ? 'selected' : '';
                                echo "<option value='$lbl' $sel>$lbl</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group-dashboard">
                    <label>Description & Dosage</label>
                    <div class="input-group-glass">
                        <textarea name="description" rows="4" required style="width: 100%; border: none; background: transparent; outline: none; padding: 10px;"><?php echo htmlspecialchars($chem['description']); ?></textarea>
                    </div>
                </div>

                <div class="form-group-dashboard">
                    <label>Status</label>
                    <div class="input-radio-group">
                        <label class="radio-card">
                            <input type="radio" name="status" value="Active" <?php echo ($chem['status'] == 'Active') ? 'checked' : ''; ?>>
                            <div class="radio-content">
                                <i class="fas fa-check-circle" style="color: #22c55e;"></i>
                                <span>Active</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="status" value="Inactive" <?php echo ($chem['status'] == 'Inactive') ? 'checked' : ''; ?>>
                            <div class="radio-content">
                                <i class="fas fa-pause-circle" style="color: #dc2626;"></i>
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
                            <!-- Display Current Image -->
                            <div style="margin-bottom: 15px;">
                                <img src="<?php echo '../' . $chem['image_path']; ?>" style="height: 100px; object-fit: contain; border-radius: 8px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));" alt="Current Image">
                            </div>
                            <div style="color: #64748b;">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 1.5rem; color: #818cf8; margin-bottom: 8px;"></i>
                                <p style="margin: 0; font-size: 0.95rem;">Drag & Drop to replace or <span style="color: #4f46e5; font-weight: 700; text-decoration: underline;">Browse</span></p>
                                <span style="font-size: 0.8rem; opacity: 0.7;">Supports JPG, PNG</span>
                            </div>
                        </div>
                        <input type="file" name="image" id="chemImage" accept="image/*">
                        
                        <!-- New Preview (Hidden initially) -->
                        <div id="imagePreview" class="image-preview" style="display:none;">
                            <img src="" alt="New Image Preview">
                            <button type="button" class="remove-btn" title="Remove Selection"><i class="fas fa-times"></i></button>
                            <div style="position: absolute; bottom: 10px; left: 0; width: 100%; text-align: center;">
                                <span style="background: rgba(0,0,0,0.6); color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;">New Selection</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit-glass" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
                    <span>Update Listing</span>
                    <i class="fas fa-check"></i>
                </button>
            </form>
        </div>
        <!-- Right Side: Tips card -->
        <div style="flex: 1; min-width: 250px;">
            <div class="glass-panel" style="background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); border: 1px solid #a5b4fc;">
                <h4 style="color: #4338ca; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-magic"></i> Editing Tips
                </h4>
                <ul style="list-style: none; padding: 0; margin-top: 1rem; color: #3730a3; font-size: 0.9rem;">
                    <li style="margin-bottom: 10px; display: flex; gap: 10px;">
                        <i class="fas fa-check-circle" style="margin-top: 3px;"></i>
                        <span>Keep safety labels up to date with latest regulations.</span>
                    </li>
                    <li style="margin-bottom: 10px; display: flex; gap: 10px;">
                        <i class="fas fa-check-circle" style="margin-top: 3px;"></i>
                        <span>Update stock counts regularly to avoid cancellations.</span>
                    </li>
                    <li style="margin-bottom: 10px; display: flex; gap: 10px;">
                        <i class="fas fa-check-circle" style="margin-top: 3px;"></i>
                        <span>High-quality images increase sales by 40%.</span>
                    </li>
                </ul>
            </div>

            <div class="glass-panel" style="margin-top: 1.5rem; text-align: center; background: rgba(255,255,255,0.6);">
                 <i class="fas fa-chart-pie" style="font-size: 2rem; color: #6366f1; margin-bottom: 1rem;"></i>
                 <h4 style="font-size: 0.9rem; color: #1e293b;">Product Performance</h4>
                 <p style="font-size: 0.8rem; color: #64748b; margin-top: 5px;">
                     This item has been viewed <strong>24 times</strong> this week.
                 </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium Glass Design */
    .glass-panel {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        padding: 2rem;
        transition: transform 0.3s ease;
    }
    
    .input-group-glass {
        position: relative; 
        display: flex; 
        align-items: center; 
        background: rgba(255, 255, 255, 0.6); 
        border: 1px solid #e5e7eb; 
        border-radius: 12px; 
        padding: 5px 15px; 
        transition: all 0.3s;
        backdrop-filter: blur(4px);
    }
    .input-group-glass:focus-within { 
        border-color: #6366f1; 
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); 
        background: rgba(255, 255, 255, 0.9); 
    }
    .input-group-glass i { color: #818cf8; margin-right: 12px; font-size: 1.1rem; }
    .input-group-glass input, .input-group-glass select, .input-group-glass textarea { 
        border: none; background: transparent; width: 100%; padding: 12px 0; font-size: 1rem; outline: none; color: #334155; font-family: inherit; font-weight: 500;
    }
    
    .form-group-dashboard label {
        display: block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;
    }
    .form-group-dashboard { margin-bottom: 1.5rem; }

    /* Radio Cards */
    .input-radio-group { display: flex; gap: 1rem; }
    .radio-card { flex: 1; cursor: pointer; position: relative; }
    .radio-card input { position: absolute; opacity: 0; }
    .radio-content { 
        border: 1px solid #e5e7eb; border-radius: 12px; padding: 12px; display: flex; align-items: center; justify-content: center; gap: 8px; background: rgba(255,255,255,0.5); transition: all 0.2s; font-weight: 600; color: #64748b; 
    }
    .radio-card input:checked + .radio-content { 
        border-color: #6366f1; background: #e0e7ff; color: #4338ca; box-shadow: 0 4px 12px -2px rgba(99, 102, 241, 0.2); transform: translateY(-2px);
    }

    /* Button */
    .btn-submit-glass { 
        width: 100%; padding: 16px; border: none; border-radius: 14px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: white; font-weight: 700; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 2.5rem; transition: all 0.3s; box-shadow: 0 4px 15px -3px rgba(99, 102, 241, 0.4); 
    }
    .btn-submit-glass:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -3px rgba(99, 102, 241, 0.5); }

    /* Alerts */
    .alert-box { padding: 15px; border-radius: 12px; margin-bottom: 2rem; font-size: 0.95rem; text-align: center; font-weight: 500; animation: slideDown 0.4s ease; }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    
    @keyframes slideDown { from {opacity:0; transform:translateY(-10px);} to {opacity:1; transform:translateY(0);} }

    /* Upload Zone Styles */
    .upload-zone { 
        border: 2px dashed #a5b4fc; 
        border-radius: 16px; 
        padding: 2rem; 
        text-align: center; 
        background: rgba(255, 255, 255, 0.4); 
        cursor: pointer; 
        transition: all 0.3s; 
        position: relative; 
        overflow: hidden;
    }
    .upload-zone:hover { 
        border-color: #6366f1; 
        background: rgba(224, 231, 255, 0.6); 
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    }
    .upload-zone input[type="file"] { 
        position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer; z-index: 5;
    }
    .image-preview { 
        position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(255, 255, 255, 0.95); 
        backdrop-filter: blur(5px);
        padding: 10px; z-index: 10; 
        display: flex; align-items: center; justify-content: center; 
    }
    .image-preview img { 
        max-width: 100%; max-height: 100%; 
        border-radius: 12px; 
        object-fit: contain; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .remove-btn { 
        position: absolute; top: 15px; right: 15px; 
        background: white; color: #ef4444; 
        border: none; border-radius: 50%; 
        width: 36px; height: 36px; 
        cursor: pointer; z-index: 20; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex; align-items: center; justify-content: center;
        transition: transform 0.2s;
    }
    .remove-btn:hover { transform: scale(1.1); color: #dc2626; }
</style>

<script>
    // File Upload Preview Logic
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('chemImage');
    const preview = document.getElementById('imagePreview');
    const previewImg = preview.querySelector('img');
    const removeBtn = preview.querySelector('.remove-btn');
    const uploadContent = uploadZone.querySelector('.upload-content');

    // Drag & Drop visual feedback
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.style.borderColor = '#4338ca';
        uploadZone.style.background = 'rgba(224, 231, 255, 0.8)';
    });

    uploadZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadZone.style.borderColor = '#a5b4fc';
        uploadZone.style.background = 'rgba(255, 255, 255, 0.4)';
    });

    // Handle File Selection
    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'flex';
                // uploadContent.style.opacity = '0'; // Optional: hide background content
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Remove Selection
    removeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Stop click from triggering file input
        fileInput.value = ''; // Clear input
        preview.style.display = 'none'; // Hide preview
        uploadContent.style.opacity = '1'; // Show original content (current image)
    });
</script>

<?php include '../includes/main_footer.php'; ?>
</html>
