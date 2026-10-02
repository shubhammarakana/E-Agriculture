<?php
// vendor/add_seeds.php
include '../db_connect.php';
session_start();

// Role Check: Vendor only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $seller_id = $_SESSION['user_id']; 
    
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']); // Hybrid, Organic
    $crop_type = $conn->real_escape_string($_POST['crop_type']); // Wheat, Rice
    $pack_size = $conn->real_escape_string($_POST['weight']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $description = $conn->real_escape_string($_POST['description']);
    $status = $_POST['status'];

    $image_path = "";

    // Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../images/"; // Shared images directory
        $file_ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid('seed_') . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = "images/" . $new_filename;
        }
    }

    $sql = "INSERT INTO seeds (seller_id, name, category, crop_type, weight, price, quantity, description, image_path, status) 
            VALUES ('$seller_id', '$name', '$category', '$crop_type', '$pack_size', '$price', '$quantity', '$description', '$image_path', '$status')";

    if ($conn->query($sql) === TRUE) {
        $message = "Seed listed successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php
$page = 'add_seeds';
$page_title = 'Add New Seed';
include '../includes/main_header.php';
?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    
    <div style="max-width: 1000px; margin: 0 auto; display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
        
        <!-- Left Side: Form -->
        <div class="glass-panel" style="flex: 2; min-width: 320px; position: relative; overflow: hidden;">
            <div style="background: linear-gradient(90deg, #10b981, #34d399); height: 5px; width: 100%; position: absolute; top: 0; left: 0;"></div>
            
            <h2 style="margin-bottom: 0.5rem; color: #065f46; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-seedling"></i> List New Seed
            </h2>
            <p style="color: #64748b; margin-bottom: 2rem; font-size: 0.95rem;">Add variety of seeds for farmers.</p>

            <?php if ($message): ?>
                <div class="alert-box <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" id="addSeedForm">
                
                <!-- Basic Info -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-dashboard">
                        <label>Seed Name</label>
                        <div class="input-group-glass">
                            <i class="fas fa-tag"></i>
                            <input type="text" name="name" placeholder="e.g. Sharbati Wheat" required>
                        </div>
                    </div>
                    <div class="form-group-dashboard">
                        <label>Category</label>
                        <div class="input-group-glass">
                            <i class="fas fa-list"></i>
                            <select name="category" required>
                                <option value="">Select Category</option>
                                <option value="Hybrid">Hybrid</option>
                                <option value="Organic">Organic</option>
                                <option value="Desi (Native)">Desi (Native)</option>
                                <option value="Imported">Imported</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-dashboard">
                        <label>Crop Type</label>
                        <div class="input-group-glass">
                            <i class="fas fa-leaf"></i>
                            <input type="text" name="crop_type" placeholder="e.g. Wheat, Tomato, Cotton" required>
                        </div>
                    </div>
                    <div class="form-group-dashboard">
                        <label>Pack Weight</label>
                        <div class="input-group-glass">
                            <i class="fas fa-weight-hanging"></i>
                            <input type="text" name="weight" placeholder="e.g. 1 kg, 500g" required>
                        </div>
                    </div>
                </div>

                <!-- Price & Stock -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-dashboard">
                        <label>Price (₹)</label>
                        <div class="input-group-glass">
                            <i class="fas fa-rupee-sign"></i>
                            <input type="number" step="0.01" name="price" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="form-group-dashboard">
                        <label>Stock Qty</label>
                        <div class="input-group-glass">
                            <i class="fas fa-layer-group"></i>
                            <input type="number" name="quantity" placeholder="Available Packs" required>
                        </div>
                    </div>
                </div>

                <div class="form-group-dashboard">
                    <label>Description & Features</label>
                    <div class="input-group-glass">
                        <textarea name="description" rows="4" placeholder="Enter seed germination rate, maturity time, etc..." required style="width: 100%; border: none; background: transparent; outline: none; padding: 10px;"></textarea>
                    </div>
                </div>

                <!-- Status -->
                <div class="form-group-dashboard">
                    <label>Listing Status</label>
                    <div class="input-radio-group">
                        <label class="radio-card">
                            <input type="radio" name="status" value="Active" checked>
                            <div class="radio-content">
                                <i class="fas fa-check-circle" style="color: #22c55e;"></i>
                                <span>Active</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="status" value="Inactive">
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
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag & Drop image here or <span style="color: #10b981;">Browse</span></p>
                            <span class="file-info">Supports JPG, PNG</span>
                        </div>
                        <input type="file" name="image" id="seedImage" accept="image/*" required>
                        <!-- Preview -->
                        <div id="imagePreview" class="image-preview" style="display:none;">
                            <img src="" alt="Preview">
                            <button type="button" class="remove-btn"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit-glass" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                    <span>Publish Seed Listing</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>

        <!-- Right Side: Tips card -->
        <div style="flex: 1; min-width: 250px;">
            <div class="glass-panel" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border: 1px solid #6ee7b7;">
                <h4 style="color: #065f46; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-info-circle"></i> Seed Selling Tips
                </h4>
                <ul style="list-style: none; padding: 0; margin-top: 1rem; color: #064e3b; font-size: 0.9rem;">
                    <li style="margin-bottom: 10px;">
                        Farmers prefer verified germination rates. Mention it in description.
                    </li>
                    <li style="margin-bottom: 10px;">
                        Clearly specify if the seeds are Hybrid or Organic certified.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    /* Reusing Styles from Add Chemical but modifying colors for Seeds (Green Theme) */
    .glass-panel {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
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
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        background: white;
    }
    .input-group-glass i { color: #9ca3af; margin-right: 10px; }
    .input-group-glass input, .input-group-glass select { border: none; background: transparent; width: 100%; padding: 10px 0; font-size: 1rem; outline: none; color: #333; }
    .input-radio-group { display: flex; gap: 1rem; }
    .radio-card { flex: 1; cursor: pointer; position: relative; }
    .radio-card input { position: absolute; opacity: 0; }
    .radio-content { border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 8px; background: rgba(255,255,255,0.5); transition: all 0.2s; font-weight: 500; color: #555; }
    .radio-card input:checked + .radio-content { border-color: #10b981; background: #d1fae5; color: #065f46; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1); }
    .upload-zone { border: 2px dashed #d1d5db; border-radius: 15px; padding: 2rem; text-align: center; background: rgba(255, 255, 255, 0.4); cursor: pointer; transition: all 0.3s; position: relative; }
    .upload-zone:hover { border-color: #10b981; background: rgba(209, 250, 229, 0.5); }
    .upload-zone input[type="file"] { position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer; }
    .upload-content i { font-size: 2.5rem; color: #9ca3af; margin-bottom: 10px; }
    .file-info { display: block; font-size: 0.8rem; color: #9ca3af; margin-top: 5px; }
    .image-preview { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: white; border-radius: 13px; padding: 5px; z-index: 10; display: flex; align-items: center; justify-content: center; }
    .image-preview img { max-width: 100%; max-height: 100%; border-radius: 8px; object-fit: contain; }
    .remove-btn { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.6); color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; z-index: 20; }
    .btn-submit-glass { width: 100%; padding: 14px; border: none; border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: white; font-weight: 600; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 2rem; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); }
    .btn-submit-glass:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.4); }
    .alert-box { padding: 12px; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.95rem; text-align: center; }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .form-group-dashboard label { display: block; margin-bottom: 8px; font-weight: 500; color: #4b5563; }
</style>

<script>
    // File Upload Preview
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('seedImage');
    const preview = document.getElementById('imagePreview');
    const previewImg = preview.querySelector('img');
    const removeBtn = preview.querySelector('.remove-btn');
    const uploadContent = uploadZone.querySelector('.upload-content');

    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'flex';
                uploadContent.style.opacity = '0'; 
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
