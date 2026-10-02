<?php
// admin/edit_master_crop.php
include '../db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: crops.php");
    exit();
}

$id = intval($_GET['id']);
$page = 'crops';
$page_title = 'Edit Master Crop';
$message = "";

// Fetch Existing Data
$stmt = $conn->prepare("SELECT * FROM master_crops WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$crop = $stmt->get_result()->fetch_assoc();

if (!$crop) {
    echo "Crop not found.";
    exit();
}

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
    
    $status = $_POST['status'];
    $is_visible_farmers = isset($_POST['is_visible_farmers']) ? 1 : 0;
    $is_visible_buyers = isset($_POST['is_visible_buyers']) ? 1 : 0;
    
    $is_ai = isset($_POST['is_ai_enabled']) ? 1 : 0;
    $ai_model = $conn->real_escape_string($_POST['ai_model_id']);
    $ai_formula = $conn->real_escape_string($_POST['ai_yield_formula']);

    $update_sql = "UPDATE master_crops SET 
        name='$name', local_name='$local_name', category='$category', season='$season', 
        growth_duration='$duration', soil_type='$soil_type', water_req='$water_req', 
        description='$description', fertilizer_rec='$fertilizer', pests_diseases='$pests', 
        status='$status', is_visible_farmers='$is_visible_farmers', is_visible_buyers='$is_visible_buyers', 
        is_ai_enabled='$is_ai', ai_model_id='$ai_model', ai_yield_formula='$ai_formula'";

    // Image Upload
    if (isset($_FILES['crop_image']) && $_FILES['crop_image']['error'] == 0) {
        $target_dir = "../images/crops/";
        $file_ext = strtolower(pathinfo($_FILES["crop_image"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        
        if(in_array($file_ext, $allowed)) {
            $new_filename = uniqid('master_') . '.' . $file_ext;
            if (move_uploaded_file($_FILES["crop_image"]["tmp_name"], $target_dir . $new_filename)) {
                $img_path = "images/crops/" . $new_filename;
                $update_sql .= ", image='$img_path'";
            }
        }
    }
    
    $update_sql .= " WHERE id = $id";

    if ($conn->query($update_sql) === TRUE) {
        $message = "Crop updated successfully!";
        // Refresh data
        $stmt->execute();
        $crop = $stmt->get_result()->fetch_assoc();
    } else {
        $message = "Error: " . $conn->error;
    }
}

include '../includes/main_header.php';
?>

<style>
    body { background-color: #f8fafc; }
    .form-section { background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid #e2e8f0; }
    .section-title { font-size: 1.1rem; font-weight: 600; color: #1e293b; margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; }
    .form-switch .form-check-input { width: 3rem; height: 1.5rem; }
     .current-img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; }
</style>

<div class="container py-5" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Edit Crop: <?php echo htmlspecialchars($crop['name']); ?></h2>
            <p class="text-muted">Update details and settings.</p>
        </div>
        <a href="crops.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success shadow-sm rounded-3 mb-4"><i class="fas fa-check-circle me-2"></i> <?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <!-- Basic Info -->
        <div class="form-section">
            <h4 class="section-title">Basic Information</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Crop Name (English)</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($crop['name']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Regional Name</label>
                    <input type="text" name="local_name" class="form-control" value="<?php echo htmlspecialchars($crop['local_name']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <?php 
                        $cats = ['Cereal', 'Vegetable', 'Fruit', 'Pulse', 'Oilseed', 'Other'];
                        foreach($cats as $c) echo "<option value='$c' ".($crop['category']==$c?'selected':'').">$c</option>";
                        ?>
                    </select>
                </div>
                <!-- ... (Repeat logic for fields with pre-filled values) ... -->
                <div class="col-md-6">
                    <label class="form-label">Season</label>
                    <select name="season" class="form-select">
                         <?php 
                        $seasons = ['Kharif', 'Rabi', 'Zaid', 'All-Season'];
                        foreach($seasons as $s) echo "<option value='$s' ".($crop['season']==$s?'selected':'').">$s</option>";
                        ?>
                    </select>
                </div>
                 <div class="col-12">
                     <label class="form-label">Description</label>
                     <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($crop['description']); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Agri Details -->
        <div class="form-section">
            <h4 class="section-title">Agricultural Details</h4>
            <div class="row g-3">
                 <div class="col-md-4">
                    <label class="form-label">Avg Duration (Days)</label>
                    <input type="number" name="growth_duration" class="form-control" value="<?php echo $crop['growth_duration']; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Water Requirement</label>
                    <select name="water_req" class="form-select">
                         <?php 
                        $reqs = ['Low', 'Medium', 'High'];
                        foreach($reqs as $r) echo "<option value='$r' ".($crop['water_req']==$r?'selected':'').">$r</option>";
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Soil Type</label>
                    <input type="text" name="soil_type" class="form-control" value="<?php echo htmlspecialchars($crop['soil_type']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fertilizer Rec</label>
                    <textarea name="fertilizer_rec" class="form-control" rows="2"><?php echo htmlspecialchars($crop['fertilizer_rec']); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pests/Diseases</label>
                    <textarea name="pests_diseases" class="form-control" rows="2"><?php echo htmlspecialchars($crop['pests_diseases']); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-section h-100">
                    <h4 class="section-title">Settings & Image</h4>
                    
                    <?php if($crop['image']): 
                         $imgShow = (strpos($crop['image'], 'http')===0 || strpos($crop['image'], 'images')===0) ? $crop['image'] : '../'.$crop['image'];
                         if(strpos($imgShow, 'images') === 0) $imgShow = '../'.$imgShow;
                    ?>
                        <div class="mb-3">
                            <label class="d-block form-label">Current Image</label>
                            <img src="<?php echo $imgShow; ?>" class="current-img">
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Update Image</label>
                        <input type="file" name="crop_image" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Active" <?php echo $crop['status']=='Active'?'selected':''; ?>>Active</option>
                            <option value="Inactive" <?php echo $crop['status']=='Inactive'?'selected':''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                 <div class="form-section h-100 bg-light">
                    <h4 class="section-title text-primary">AI & Visibility</h4>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_ai_enabled" value="1" <?php echo $crop['is_ai_enabled']?'checked':''; ?>>
                        <label class="form-check-label fw-bold">Enable AI</label>
                    </div>
                    <input type="text" name="ai_model_id" class="form-control mb-2" value="<?php echo htmlspecialchars($crop['ai_model_id']); ?>" placeholder="AI Model ID">
                    
                    <hr>
                    
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_visible_farmers" value="1" <?php echo $crop['is_visible_farmers']?'checked':''; ?>>
                        <label class="form-check-label">Visible to Farmers</label>
                    </div>
                 </div>
            </div>
        </div>
        
        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg fw-bold">Update Crop </button>
        </div>
    </form>
</div>

<?php include '../includes/main_footer.php'; ?>
