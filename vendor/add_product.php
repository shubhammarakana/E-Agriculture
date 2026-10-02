<?php
// vendor/add_product.php
include '../db_connect.php';
session_start();

// Security Check for Vendor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $_POST['price'];
    $price_unit = $conn->real_escape_string($_POST['price_unit']);
    $stock_status = $_POST['stock_status'];

    // Image Upload
    $image_path = "";
    if (isset($_FILES['crop_image']) && $_FILES['crop_image']['error'] == 0) {
        $target_dir = "../images/"; // Root images folder
        $file_ext = strtolower(pathinfo($_FILES["crop_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid('crop_') . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        $db_image_path = "images/" . $new_filename; // Path stored in DB relative to root

        if (move_uploaded_file($_FILES["crop_image"]["tmp_name"], $target_file)) {
            $image_path = $db_image_path;
        }
    }

    // Get Vendor ID from Session (stored as farmer_id in crops table for ownership)
    $vendor_id = $_SESSION['user_id'];

    $sql = "INSERT INTO crops (farmer_id, name, category, description, price, price_unit, image_path, stock_status) 
            VALUES ('$vendor_id', '$name', '$category', '$description', '$price', '$price_unit', '$image_path', '$stock_status')";

    if ($conn->query($sql) === TRUE) {
        $message = "Crop listed successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

$page = 'add_product';
$page_title = 'Add New Crop';
include '../includes/main_header.php';
?>

<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    
    <div style="max-width: 900px; margin: 0 auto; display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
        
        <!-- Left Side: Form -->
        <div class="glass-panel" style="flex: 2; min-width: 320px; position: relative; overflow: hidden;">
            <div style="background: linear-gradient(90deg, #16a34a, #15803d); height: 5px; width: 100%; position: absolute; top: 0; left: 0;"></div>
            
            <h2 style="margin-bottom: 0.5rem; color: #166534; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-plus-circle"></i> List New Crop
            </h2>
            <p style="color: #64748b; margin-bottom: 2rem; font-size: 0.95rem;">Fill in the details to sell your produce on the marketplace.</p>

            <?php if ($message): ?>
                <div class="alert-box <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="add_product.php" method="POST" enctype="multipart/form-data" id="addCropForm">
                
                <!-- Name -->
                <div class="form-group-dashboard">
                    <label>Crop Name</label>
                    <div class="input-group-glass">
                        <i class="fas fa-seedling"></i>
                        <input type="text" name="name" placeholder="e.g. Organic Golden Wheat" required>
                    </div>
                </div>

                <!-- Category -->
                <div class="form-group-dashboard">
                    <label>Category</label>
                    <div class="input-group-glass">
                        <i class="fas fa-list"></i>
                        <select name="category" required>
                            <option value="" disabled selected>Select Category</option>
                            <option value="Grains">Grains</option>
                            <option value="Pulses">Pulses</option>
                            <option value="Fruits">Fruits</option>
                            <option value="Vegetables">Vegetables</option>
                        </select>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group-dashboard">
                    <label>Description</label>
                    <div class="input-group-glass">
                        <i class="fas fa-align-left" style="align-self: flex-start; margin-top: 10px;"></i>
                        <textarea name="description" rows="3" placeholder="Describe your crop (quality, origin, etc.)..." required style="resize: none;"></textarea>
                    </div>
                </div>

                <!-- Price & Unit -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group-dashboard">
                        <label>Price</label>
                        <div class="input-group-glass">
                            <i class="fas fa-tag"></i>
                            <input type="number" step="0.01" name="price" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="form-group-dashboard">
                        <label>Unit</label>
                        <div class="input-group-glass">
                            <i class="fas fa-weight-hanging"></i>
                            <select name="price_unit">
                                <option value="kg">per kg</option>
                                <option value="ton">per ton</option>
                                <option value="crate">per crate</option>
                                <option value="quintal">per quintal</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Stock Status -->
                <div class="form-group-dashboard">
                    <label>Availability</label>
                    <div class="input-radio-group">
                        <label class="radio-card">
                            <input type="radio" name="stock_status" value="In Stock" checked>
                            <div class="radio-content">
                                <i class="fas fa-check-circle" style="color: #22c55e;"></i>
                                <span>In Stock</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="stock_status" value="Low Stock">
                            <div class="radio-content">
                                <i class="fas fa-exclamation-circle" style="color: #f59e0b;"></i>
                                <span>Low Stock</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Image Upload (Drag & Drop) -->
                <div class="form-group-dashboard">
                    <label>Crop Image</label>
                    <div class="upload-zone" id="uploadZone">
                        <div class="upload-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Drag & Drop image here or <span style="color: #16a34a;">Browse</span></p>
                            <span class="file-info">Supports JPG, PNG</span>
                        </div>
                        <input type="file" name="crop_image" id="cropImage" accept="image/*" required>
                        <!-- Preview -->
                        <div id="imagePreview" class="image-preview" style="display: none;">
                            <img src="" alt="Preview">
                            <button type="button" class="remove-btn"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit-glass">
                    <span>Publish Listing</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>

        <!-- Right Side: Tips card -->
        <div style="flex: 1; min-width: 250px;">
            <div class="glass-panel" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0;">
                <h4 style="color: #166534; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-lightbulb"></i> Selling Tips
                </h4>
                <ul style="list-style: none; padding: 0; margin-top: 1rem; color: #14532d; font-size: 0.9rem;">
                    <li style="margin-bottom: 10px; display: flex; gap: 10px;">
                        <i class="fas fa-check" style="margin-top: 4px;"></i> 
                        <span>Use a clear, bright photo of your actual crop.</span>
                    </li>
                    <li style="margin-bottom: 10px; display: flex; gap: 10px;">
                        <i class="fas fa-check" style="margin-top: 4px;"></i> 
                        <span>Competitive pricing sells 2x faster.</span>
                    </li>
                    <li style="margin-bottom: 10px; display: flex; gap: 10px;">
                        <i class="fas fa-check" style="margin-top: 4px;"></i> 
                        <span>Mark "Low Stock" to create urgency.</span>
                    </li>
                </ul>
            </div>
            
            <div class="glass-panel" style="margin-top: 1.5rem; text-align: center;">
                 <i class="fas fa-chart-line" style="font-size: 2rem; color: #16a34a; margin-bottom: 1rem;"></i>
                 <h4 style="font-size: 0.9rem;">Market Trend</h4>
                 <p style="font-size: 0.8rem; color: #64748b; margin-top: 5px;">
                     Current demand for <strong style="color: #333;">Wheat</strong> is high in your region.
                 </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Styles for this page */
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
    .input-group-glass i {
        color: #9ca3af;
        margin-right: 10px;
    }
    .input-group-glass input, .input-group-glass select, .input-group-glass textarea {
        border: none;
        background: transparent;
        width: 100%;
        padding: 10px 0;
        font-size: 1rem;
        outline: none;
        color: #333;
        font-family: inherit;
    }

    /* Radio Cards */
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
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: rgba(255,255,255,0.5);
        transition: all 0.2s;
        font-weight: 500;
        color: #555;
    }
    .radio-card input:checked + .radio-content {
        border-color: #16a34a;
        background: #f0fdf4;
        color: #16a34a;
        box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.1);
    }

    /* Upload Zone */
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
        border-color: #16a34a;
        background: rgba(240, 253, 244, 0.5);
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

    /* Image Preview */
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
        background: rgba(0,0,0,0.6);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        z-index: 20;
    }

    /* Submit Button */
    .btn-submit-glass {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
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
        box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.3);
    }
    .btn-submit-glass:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(22, 163, 74, 0.4);
    }

    /* Alerts */
    .alert-box {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        text-align: center;
    }
    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
</style>

<script>
    // File Upload Preview
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('cropImage');
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
                uploadContent.style.opacity = '0'; // Hide text underneath
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    removeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Stop click from triggering file input
        fileInput.value = ''; // Clear input
        preview.style.display = 'none';
        uploadContent.style.opacity = '1';
    });
</script>

<?php include '../includes/main_footer.php'; ?>
