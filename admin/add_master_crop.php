<?php
// admin/add_master_crop.php
include '../db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$page = 'crops';
$page_title = 'Add Master Crop';
$message = "";
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitization
    $name = $conn->real_escape_string($_POST['name']);
    $local_name = $conn->real_escape_string($_POST['local_name']);
    $category = $_POST['category'];
    $season = $_POST['season'];
    $duration = (int)$_POST['growth_duration'];
    $soil_type = $conn->real_escape_string($_POST['soil_type']);
    $water_req = $_POST['water_req'];
    $description = $conn->real_escape_string($_POST['description']);
    $fertilizer = $conn->real_escape_string($_POST['fertilizer_rec']);
    $pests = $conn->real_escape_string($_POST['pests_diseases']);
    
    // Toggles
    $status = $_POST['status'];
    $is_visible_farmers = isset($_POST['is_visible_farmers']) ? 1 : 0;
    $is_visible_buyers = isset($_POST['is_visible_buyers']) ? 1 : 0;
    
    // AI
    $is_ai = isset($_POST['is_ai_enabled']) ? 1 : 0;
    $ai_model = $conn->real_escape_string($_POST['ai_model_id']);
    $ai_formula = $conn->real_escape_string($_POST['ai_yield_formula']);

    // Image Upload
    $image_path = "";
    if (isset($_FILES['crop_image']) && $_FILES['crop_image']['error'] == 0) {
        $target_dir = "../images/crops/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_ext = strtolower(pathinfo($_FILES["crop_image"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        
        if(in_array($file_ext, $allowed)) {
            $new_filename = uniqid('master_') . '.' . $file_ext;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["crop_image"]["tmp_name"], $target_file)) {
                $image_path = "images/crops/" . $new_filename;
            } else {
                $message = "Error uploading image.";
                $error = true;
            }
        } else {
            $message = "Invalid file type. Only JPG, PNG, WEBP allowed.";
            $error = true;
        }
    }

    if (!$error) {
        $sql = "INSERT INTO master_crops (
            name, local_name, category, image, season, growth_duration, soil_type, water_req, 
            description, fertilizer_rec, pests_diseases, status, 
            is_visible_farmers, is_visible_buyers, is_ai_enabled, ai_model_id, ai_yield_formula
        ) VALUES (
            '$name', '$local_name', '$category', '$image_path', '$season', '$duration', '$soil_type', '$water_req',
            '$description', '$fertilizer', '$pests', '$status',
            '$is_visible_farmers', '$is_visible_buyers', '$is_ai', '$ai_model', '$ai_formula'
        )";

        if ($conn->query($sql) === TRUE) {
            $message = "Master Crop added successfully!";
        } else {
            $message = "Database Error: " . $conn->error;
        }
    }
}

include '../includes/main_header.php';
?>

<style>
    /* Reuse consistent form styles */
    body { background-color: #f8fafc; }
    
    .form-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-label {
        font-weight: 500;
        font-size: 0.9rem;
        color: #475569;
        margin-bottom: 0.5rem;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }
    
    /* Toggle Switch */
    .form-switch .form-check-input {
        width: 3rem;
        height: 1.5rem;
        cursor: pointer;
    }
    .form-switch .form-check-input:checked {
        background-color: #059669;
        border-color: #059669;
    }

</style>

<div class="container py-5" style="max-width: 900px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Add New Master Crop</h2>
            <p class="text-muted">Register a new standardize crop in the system.</p>
        </div>
        <a href="crops.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo strpos($message, 'Error') !== false ? 'danger' : 'success'; ?> shadow-sm rounded-3 mb-4">
            <i class="fas fa-info-circle me-2"></i> <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        
        <!-- Basic Info -->
        <div class="form-section">
            <h4 class="section-title"><i class="fas fa-seedling text-success"></i> Basic Information</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Crop Name (English) *</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Wheat">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Regional Name *</label>
                    <input type="text" name="local_name" class="form-control" placeholder="e.g. Gehu">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Category *</label>
                    <select name="category" class="form-select" required>
                        <option value="Cereal">Cereal</option>
                        <option value="Vegetable">Vegetable</option>
                        <option value="Fruit">Fruit</option>
                        <option value="Pulse">Pulse</option>
                        <option value="Oilseed">Oilseed</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Primary Season *</label>
                    <select name="season" class="form-select" required>
                        <option value="Kharif">Kharif (Monsoon)</option>
                        <option value="Rabi">Rabi (Winter)</option>
                        <option value="Zaid">Zaid (Summer)</option>
                        <option value="All-Season">All-Season</option>
                    </select>
                </div>
                <div class="col-12">
                     <label class="form-label">Description</label>
                     <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>

        <!-- Agri Details -->
        <div class="form-section">
            <h4 class="section-title"><i class="fas fa-leaf text-success"></i> Agricultural Details</h4>
            <div class="row g-3">
                 <div class="col-md-4">
                    <label class="form-label">Avg Duration (Days)</label>
                    <input type="number" name="growth_duration" class="form-control" placeholder="120">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Water Requirement</label>
                    <select name="water_req" class="form-select">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Soil Type</label>
                    <input type="text" name="soil_type" class="form-control" placeholder="e.g. Loamy, Sandy">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fertilizer Recommendation</label>
                    <textarea name="fertilizer_rec" class="form-control" rows="2" placeholder="e.g. NPK 120:60:40"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Common Pests/Diseases</label>
                    <textarea name="pests_diseases" class="form-control" rows="2" placeholder="e.g. Rust, Aphids"></textarea>
                </div>
            </div>
        </div>

        <!-- AI & Settings -->
        <div class="row">
            <div class="col-md-6">
                <!-- AI Integration -->
                <div class="form-section h-100 bg-light border-0">
                    <h4 class="section-title text-primary"><i class="fas fa-robot"></i> AI Integration</h4>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="aiToggle" name="is_ai_enabled" value="1">
                        <label class="form-check-label fw-bold" for="aiToggle">Enable AI Features</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">AI Model ID (Mapping)</label>
                        <input type="text" name="ai_model_id" class="form-control" placeholder="e.g. model_wheat_v2">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yield Formula</label>
                        <input type="text" name="ai_yield_formula" class="form-control" placeholder="e.g. (area * 3.5) * efficiency">
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Visibility -->
                <div class="form-section h-100">
                    <h4 class="section-title"><i class="fas fa-eye text-success"></i> Visibility & Status</h4>
                    
                    <div class="mb-3">
                        <label class="form-label">Platform Status</label>
                        <select name="status" class="form-select">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_visible_farmers" checked>
                        <label class="form-check-label">Visible to Farmers</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                         <input class="form-check-input" type="checkbox" name="is_visible_buyers" checked>
                        <label class="form-check-label">Visible to Buyers</label>
                    </div>
                    
                    <div class="mt-4">
                        <label class="form-label">Upload Image</label>
                        <input type="file" name="crop_image" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-success btn-lg fw-bold shadow-sm">
                <i class="fas fa-save me-2"></i> Save Master Crop
            </button>
        </div>

    </form>
</div>

<?php include '../includes/main_footer.php'; ?>
