<?php
// vendor/profile.php
include '../db_connect.php';
session_start();

// Check if user is logged in and is a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$msg_type = "";

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $business = $conn->real_escape_string($_POST['business_name']);
    $location = $conn->real_escape_string($_POST['location']);

    // Handle Image Upload
    $image_query = "";
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = "vendor_" . time() . "." . $filetype;
            $target_dir = dirname(__DIR__) . "/uploads/";
            
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target = $target_dir . $new_filename;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
                $db_image_path = "uploads/" . $new_filename;
                $image_query = ", profile_image='$db_image_path'";
                $_SESSION['profile_image'] = $db_image_path; 
            }
        }
    }

    $sql = "UPDATE users SET fullname='$fullname', phone='$phone', business_name='$business', location='$location' $image_query WHERE id='$user_id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['fullname'] = $fullname;
        $message = "Profile updated successfully!";
        $msg_type = "success";
    } else {
        $message = "Error updating profile: " . $conn->error;
        $msg_type = "error";
    }
}

// Handle Password Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $result = $conn->query("SELECT password FROM users WHERE id='$user_id'");
    $row = $result->fetch_assoc();

    if (password_verify($current_password, $row['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            if ($conn->query("UPDATE users SET password='$hashed_password' WHERE id='$user_id'") === TRUE) {
                $message = "Password changed successfully!";
                $msg_type = "success";
            } else {
                $message = "Error updating password.";
                $msg_type = "error";
            }
        } else {
            $message = "New passwords do not match.";
            $msg_type = "error";
        }
    } else {
        $message = "Incorrect current password.";
        $msg_type = "error";
    }
}

// Fetch Current User Data
$user = $conn->query("SELECT * FROM users WHERE id='$user_id'")->fetch_assoc();
$page = 'profile';
$page_title = 'Vendor Profile';
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../includes/main_header.php'; ?>
<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    <div style="max-width: 900px; margin: 0 auto;">
        
        <h2 style="margin-bottom: 20px; color: #1f2937;">My Profile</h2>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            
            <!-- Left Column: Profile Card -->
            <div class="glass-card" style="text-align: center; padding: 2rem;">
                <div style="position: relative; width: 140px; height: 140px; margin: 0 auto 1.5rem;">
                    <img id="profile_preview" src="<?php echo !empty($user['profile_image']) ? '../uploads/' . $user['profile_image'] : '../images/default_user.png'; ?>" 
                         alt="Profile" 
                         style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 4px solid rgba(255,255,255,0.8); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <label for="profile_upload" style="position: absolute; bottom: 5px; right: 5px; background: #4f46e5; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                        <i class="fas fa-camera"></i>
                    </label>
                </div>
                
                <h3 style="margin: 0; color: #1e293b;"><?php echo htmlspecialchars($user['business_name'] ?? 'My Business'); ?></h3>
                <p style="color: #64748b; margin-top: 5px;"><?php echo htmlspecialchars($user['fullname']); ?></p>
                
                <div style="margin-top: 1.5rem; text-align: left; background: rgba(255,255,255,0.5); padding: 15px; border-radius: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: #475569;">
                        <i class="fas fa-envelope" style="width: 20px; color: #4f46e5;"></i> 
                        <span style="font-size: 0.9rem;"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: #475569;">
                        <i class="fas fa-phone" style="width: 20px; color: #4f46e5;"></i>
                        <span style="font-size: 0.9rem;"><?php echo htmlspecialchars($user['phone']); ?></span>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 10px; color: #475569;">
                        <i class="fas fa-map-marker-alt" style="width: 20px; color: #4f46e5; margin-top: 3px;"></i>
                        <span style="font-size: 0.9rem;"><?php echo htmlspecialchars($user['location']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Edit Forms -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                
                <!-- Edit Details Form -->
                <div class="glass-card">
                    <h4 style="margin-bottom: 1.5rem; color: #1e293b; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 10px;">Edit Details</h4>
                    
                    <form action="profile.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="profile_image" id="profile_upload" style="display: none;" onchange="previewImage(this)">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" class="glass-input" required>
                            </div>
                            <div class="form-group">
                                <label>Business Name</label>
                                <input type="text" name="business_name" value="<?php echo htmlspecialchars($user['business_name']); ?>" class="glass-input" required>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label>Phone Number</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="glass-input" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label>Address / Location</label>
                            <textarea name="location" rows="3" class="glass-input" required><?php echo htmlspecialchars($user['location']); ?></textarea>
                        </div>

                        <button type="submit" class="btn-primary-glass">Save Changes</button>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="glass-card">
                    <h4 style="margin-bottom: 1.5rem; color: #1e293b; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 10px;">Change Password</h4>
                    
                    <form action="profile.php" method="POST">
                        <input type="hidden" name="update_password" value="1">
                        
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="glass-input" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" class="glass-input" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" class="glass-input" required minlength="6">
                            </div>
                        </div>

                        <button type="submit" class="btn-primary-glass" style="background: #4f46e5;">Update Password</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .form-group label {
        display: block; margin-bottom: 5px; color: #475569; font-weight: 500; font-size: 0.9rem;
    }
    .glass-input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid rgba(0,0,0,0.1);
        border-radius: 8px;
        background: rgba(255,255,255,0.5);
        outline: none;
        transition: all 0.3s;
        color: #334155;
    }
    .glass-input:focus {
        border-color: #4f46e5;
        background: white;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    .btn-primary-glass {
        padding: 10px 20px;
        background: var(--primary-green);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-primary-glass:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
</style>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile_preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php if ($message != ""): ?>
<script>
    Swal.fire({
        icon: '<?php echo $msg_type; ?>',
        title: '<?php echo ucfirst($msg_type); ?>',
        text: '<?php echo $message; ?>',
        timer: 2000,
        showConfirmButton: false
    });
</script>
<?php endif; ?>

<?php include '../includes/main_footer.php'; ?>
</html>
