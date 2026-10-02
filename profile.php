<?php
// profile.php - Unified Profile Management
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$message = "";
$error = "";

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $location = $conn->real_escape_string($_POST['location']);
    
    // Role specific fields
    $business_name = isset($_POST['business_name']) ? $conn->real_escape_string($_POST['business_name']) : '';

    // Image Upload Logic
    $profile_image_path = "";
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "images/users/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_ext, $allowed)) {
            $new_filename = "user_" . $user_id . "_" . time() . "." . $file_ext;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $profile_image_path = $target_file;
                $_SESSION['profile_image'] = $profile_image_path; // Update Session
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, GIF allowed.";
        }
    }

    if (!$error) {
        $sql_update = "UPDATE users SET fullname='$fullname', phone='$phone', location='$location'";
        
        if ($business_name) {
            $sql_update .= ", business_name='$business_name'";
        }
        
        if ($profile_image_path) {
            $sql_update .= ", profile_image='$profile_image_path'";
        }
        
        $sql_update .= " WHERE id='$user_id'";

        if ($conn->query($sql_update)) {
            $_SESSION['fullname'] = $fullname; // Update Session
            $message = "Profile updated successfully!";
        } else {
            $error = "Database Error: " . $conn->error;
        }
    }
}

// Fetch Current User Data
$user = $conn->query("SELECT * FROM users WHERE id='$user_id'")->fetch_assoc();
$page = 'profile';
$page_title = 'My Profile';
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/main_header.php'; ?>

<style>
    .profile-container {
        max-width: 800px;
        margin: 3rem auto;
    }
    .profile-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
    }
    .profile-header {
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .profile-img-upload {
        position: relative;
        width: 120px;
        height: 120px;
    }
    .profile-img-preview {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .upload-btn {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: #16a34a;
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px solid white;
        transition: transform 0.2s;
    }
    .upload-btn:hover { transform: scale(1.1); }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    @media(max-width: 768px) {
        .profile-header { flex-direction: column; text-align: center; }
        .form-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="container profile-container">
    <div class="profile-card">
        
        <?php if ($message): ?>
            <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; text-align: center;">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; text-align: center;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="profile-header">
                <div class="profile-img-upload">
                    <img src="<?php echo !empty($user['profile_image']) ? $user['profile_image'] : 'images/default-user.png'; ?>" class="profile-img-preview" id="previewImg">
                    <label for="profileImgInput" class="upload-btn">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" name="profile_image" id="profileImgInput" style="display: none;" onchange="previewFile()">
                </div>
                <div>
                    <h2 style="margin: 0; color: #1e293b;"><?php echo htmlspecialchars($user['fullname']); ?></h2>
                    <p style="margin: 5px 0 0; color: #64748b; font-weight: 500; text-transform: capitalize;"><?php echo $user['role']; ?> Account</p>
                    <p style="margin: 5px 0 0; font-size: 0.9rem; color: #94a3b8;"><?php echo $user['email']; ?></p>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group-dashboard">
                    <label>Full Name</label>
                    <input type="text" name="fullname" class="form-control-glass" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                </div>
                <div class="form-group-dashboard">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control-glass" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
                
                <?php if ($role == 'farmer' || $role == 'vendor'): ?>
                <div class="form-group-dashboard">
                    <label>Business Name</label>
                    <input type="text" name="business_name" class="form-control-glass" value="<?php echo htmlspecialchars($user['business_name']); ?>">
                </div>
                <?php endif; ?>
                
                <div class="form-group-dashboard" style="grid-column: 1 / -1;">
                    <label>Address / Location</label>
                    <textarea name="location" class="form-control-glass" rows="3"><?php echo htmlspecialchars($user['location']); ?></textarea>
                </div>
            </div>

            <div style="margin-top: 2rem; text-align: right;">
                <button type="submit" class="btn-primary-glass" style="padding: 12px 30px;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewFile() {
        const preview = document.getElementById('previewImg');
        const file = document.getElementById('profileImgInput').files[0];
        const reader = new FileReader();

        reader.addEventListener("load", function () {
            preview.src = reader.result;
        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>

<?php include 'includes/main_footer.php'; ?>
</html>
