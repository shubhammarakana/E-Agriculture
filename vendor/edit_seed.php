<?php
// vendor/edit_seed.php
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
$sql = "SELECT * FROM seeds WHERE id = '$id' AND seller_id = '$vendor_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Seed not found or access denied.";
    exit();
}

$seed = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $crop_type = $conn->real_escape_string($_POST['crop_type']);
    $weight = $conn->real_escape_string($_POST['weight']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $description = $conn->real_escape_string($_POST['description']);
    $status = $_POST['status'];

    $image_path = $seed['image_path'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../images/";
        $file_ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid('seed_') . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = "images/" . $new_filename;
        }
    }

    $update_sql = "UPDATE seeds SET 
                   name='$name', category='$category', crop_type='$crop_type',
                   weight='$weight', price='$price', quantity='$quantity',
                   description='$description', image_path='$image_path', status='$status'
                   WHERE id='$id' AND seller_id='$vendor_id'";

    if ($conn->query($update_sql) === TRUE) {
        $message = "Seed updated successfully!";
        $seed = $conn->query($sql)->fetch_assoc(); // Refresh
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php
$page = 'seeds';
$page_title = 'Edit Seed';
include '../includes/main_header.php';
?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    
    <div style="max-width: 1000px; margin: 0 auto; display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
        
        <!-- Left Side: Form -->
        <div class="glass-panel" style="flex: 2; min-width: 320px; position: relative; overflow: hidden;">
            <div style="background: linear-gradient(90deg, #10b981, #34d399); height: 5px; width: 100%; position: absolute; top: 0; left: 0;"></div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 style="margin:0; color: #065f46; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-edit"></i> Edit Seed
                </h2>
                <a href="seeds.php" class="btn btn-outline" style="border-radius: 8px; padding: 5px 15px; font-size: 0.9rem;">Back to List</a>
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
                        <label>Seed Name</label>
                        <div class="input-group-glass">
                            <i class="fas fa-tag"></i>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($seed['name']); ?>" required>
                        </div>
                    </div>
                    <div class="form-group-dashboard">
                        <label>Category</label>
                        <div class="input-group-glass">
                            <i class="fas fa-list"></i>
                            <select name="category" required>
                                <?php $cats = ['Hybrid', 'Organic', 'Desi (Native)', 'Imported']; 
                                foreach($cats as $cat) {
                                    $sel = ($seed['category'] == $cat) ? 'selected' : '';
                                    echo "<option value='$cat' $sel>$cat</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-dashboard">
                        <label>Crop Type</label>
                        <div class="input-group-glass">
                            <i class="fas fa-leaf"></i>
                            <input type="text" name="crop_type" value="<?php echo htmlspecialchars($seed['crop_type']); ?>" required>
                        </div>
                    </div>
                    <div class="form-group-dashboard">
                        <label>Pack Weight</label>
                        <div class="input-group-glass">
                            <i class="fas fa-weight-hanging"></i>
                            <input type="text" name="weight" value="<?php echo htmlspecialchars($seed['weight']); ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Price & Stock -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-dashboard">
                        <label>Price (₹)</label>
                        <div class="input-group-glass">
                            <i class="fas fa-rupee-sign"></i>
                            <input type="number" step="0.01" name="price" value="<?php echo $seed['price']; ?>" required>
                        </div>
                    </div>
                    <div class="form-group-dashboard">
                        <label>Stock Qty</label>
                        <div class="input-group-glass">
                            <i class="fas fa-layer-group"></i>
                            <input type="number" name="quantity" value="<?php echo $seed['quantity']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-group-dashboard">
                    <label>Description & Features</label>
                    <div class="input-group-glass">
                        <textarea name="description" rows="4" required style="width: 100%; border: none; background: transparent; outline: none; padding: 10px;"><?php echo htmlspecialchars($seed['description']); ?></textarea>
                    </div>
                </div>

                <div class="form-group-dashboard">
                    <label>Status</label>
                    <div class="input-radio-group">
                        <label class="radio-card">
                            <input type="radio" name="status" value="Active" <?php echo ($seed['status'] == 'Active') ? 'checked' : ''; ?>>
                            <div class="radio-content">
                                <i class="fas fa-check-circle" style="color: #22c55e;"></i>
                                <span>Active</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="status" value="Inactive" <?php echo ($seed['status'] == 'Inactive') ? 'checked' : ''; ?>>
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
                                <img src="<?php echo '../' . $seed['image_path']; ?>" style="height: 100px; object-fit: contain; border-radius: 8px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));" alt="Current Image">
                            </div>
                            <div style="color: #64748b;">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 1.5rem; color: #34d399; margin-bottom: 8px;"></i>
                                <p style="margin: 0; font-size: 0.95rem;">Drag & Drop to replace or <span style="color: #10b981; font-weight: 700; text-decoration: underline;">Browse</span></p>
                                <span style="font-size: 0.8rem; opacity: 0.7;">Supports JPG, PNG</span>
                            </div>
                        </div>
                        <input type="file" name="image" id="seedImage" accept="image/*">
                        
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

                <button type="submit" class="btn-submit-glass" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                    <span>Update Seed</span>
                    <i class="fas fa-check"></i>
                </button>
            </form>
        </div>
        <!-- Right Side: Tips card -->
        <div style="flex: 1; min-width: 250px;">
            <div class="glass-panel" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border: 1px solid #6ee7b7;">
                <h4 style="color: #065f46; display: flex; align-items: center; gap: 8px;">
                     <i class="fas fa-magic"></i> Editing Tips
                </h4>
                <ul style="list-style: none; padding: 0; margin-top: 1rem; color: #064e3b; font-size: 0.9rem;">
                    <li style="margin-bottom: 10px;">
                         Update quantity immediately after selling offline.
                    </li>
                    <li style="margin-bottom: 10px;">
                         Keep descriptions detailed, mention germination rate updates.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling reused from Edit Chemical but consistent with Green Theme */
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
        border-color: #10b981; 
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); 
        background: rgba(255, 255, 255, 0.9); 
    }
    .input-group-glass i { color: #34d399; margin-right: 12px; font-size: 1.1rem; }
    .input-group-glass input, .input-group-glass select, .input-group-glass textarea { 
        border: none; background: transparent; width: 100%; padding: 12px 0; font-size: 1rem; outline: none; color: #334155; font-family: inherit; font-weight: 500;
    }
    .form-group-dashboard label {
        display: block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 0.9rem;
    }
    .form-group-dashboard { margin-bottom: 1.5rem; }
    .input-radio-group { display: flex; gap: 1rem; }
    .radio-card { flex: 1; cursor: pointer; position: relative; }
    .radio-card input { position: absolute; opacity: 0; }
    .radio-content { 
        border: 1px solid #e5e7eb; border-radius: 12px; padding: 12px; display: flex; align-items: center; justify-content: center; gap: 8px; background: rgba(255,255,255,0.5); transition: all 0.2s; font-weight: 600; color: #64748b; 
    }
    .radio-card input:checked + .radio-content { 
        border-color: #10b981; background: #d1fae5; color: #065f46; box-shadow: 0 4px 12px -2px rgba(16, 185, 129, 0.2); transform: translateY(-2px);
    }
    .btn-submit-glass { 
        width: 100%; padding: 16px; border: none; border-radius: 14px; background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: white; font-weight: 700; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 2.5rem; transition: all 0.3s; box-shadow: 0 4px 15px -3px rgba(16, 185, 129, 0.4); 
    }
    .btn-submit-glass:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -3px rgba(16, 185, 129, 0.5); }
    .alert-box { padding: 15px; border-radius: 12px; margin-bottom: 2rem; font-size: 0.95rem; text-align: center; font-weight: 500; animation: slideDown 0.4s ease; }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    @keyframes slideDown { from {opacity:0; transform:translateY(-10px);} to {opacity:1; transform:translateY(0);} }
    .upload-zone { 
        border: 2px dashed #6ee7b7; 
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
        border-color: #10b981; 
        background: rgba(209, 250, 229, 0.6); 
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
    }
    .upload-zone input[type="file"] { position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer; z-index: 5; }
    .image-preview { 
        position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(255, 255, 255, 0.95); 
        backdrop-filter: blur(5px);
        padding: 10px; z-index: 10; 
        display: flex; align-items: center; justify-content: center; 
    }
    .image-preview img { max-width: 100%; max-height: 100%; border-radius: 12px; object-fit: contain; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
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
    const fileInput = document.getElementById('seedImage');
    const preview = document.getElementById('imagePreview');
    const previewImg = preview.querySelector('img');
    const removeBtn = preview.querySelector('.remove-btn');
    const uploadContent = uploadZone.querySelector('.upload-content');

    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.style.borderColor = '#059669';
        uploadZone.style.background = 'rgba(209, 250, 229, 0.8)';
    });

    uploadZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadZone.style.borderColor = '#6ee7b7';
        uploadZone.style.background = 'rgba(255, 255, 255, 0.4)';
    });

    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'flex';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    removeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fileInput.value = '';
        preview.style.display = 'none';
        uploadContent.style.opacity = '1';
    });
</script>

<?php include '../includes/main_footer.php'; ?>
</html>
