<?php
// admin/profile.php
include '../db_connect.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$msg_type = "";

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    error_log("Profile Update Started: User ID " . $user_id);
    
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    
    error_log("POST Data: Name=$fullname, Email=$email, Phone=$phone");

    // Handle Image Upload
    $image_query = "";
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        error_log("File Upload Detected: " . $_FILES['profile_image']['name']);
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = "admin_" . time() . "." . $filetype;
            $target_dir = dirname(__DIR__) . "/uploads/";
            
            // Ensure dir exists
            if (!file_exists($target_dir)) {
                if (!mkdir($target_dir, 0777, true)) {
                    error_log("Failed to create directory: " . $target_dir);
                }
            }

            $target = $target_dir . $new_filename;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
                $db_image_path = "uploads/" . $new_filename;
                $image_query = ", profile_image='$db_image_path'";
                $_SESSION['profile_image'] = $db_image_path; 
                error_log("File Uploaded Successfully to: " . $target);
            } else {
                error_log("move_uploaded_file failed. Temp: " . $_FILES['profile_image']['tmp_name'] . " To: " . $target);
            }
        } else {
            error_log("Invalid file type: " . $filetype);
        }
    } else {
        error_log("No file uploaded or error code: " . ($_FILES['profile_image']['error'] ?? 'Unset'));
    }

    $sql = "UPDATE users SET fullname='$fullname', email='$email', phone='$phone', address='$address' $image_query WHERE id='$user_id'";
    error_log("SQL Query: " . $sql);

    if ($conn->query($sql) === TRUE) {
        error_log("Database Update Successful");
        $_SESSION['fullname'] = $fullname; 
        $message = "Profile updated successfully!";
        $msg_type = "success";
    } else {
        error_log("Database Update Failed: " . $conn->error);
        $message = "Error updating profile: " . $conn->error;
        $msg_type = "error";
    }
}

// Handle Password Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify current password
    $sql = "SELECT password FROM users WHERE id='$user_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if (password_verify($current_password, $row['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password='$hashed_password' WHERE id='$user_id'";
            if ($conn->query($update_sql) === TRUE) {
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
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<?php
$page_title = 'Admin Profile';
include '../includes/main_header.php';
?>

<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    <div style="max-width: 900px; margin: 0 auto;">
        
        <!-- Header -->
        <h2 style="margin-bottom: 20px; color: #4338ca;">My Profile</h2>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            
            <!-- Left Column: Profile Card -->
            <div class="glass-card" style="text-align: center; padding: 2rem;">
                <div style="position: relative; width: 150px; height: 150px; margin: 0 auto 1.5rem;">
                    <img src="<?php echo !empty($user['profile_image']) ? '../' . $user['profile_image'] : '../images/default_user.png'; ?>" 
                         alt="Profile" 
                         style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 4px solid rgba(255,255,255,0.5); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <label for="profile_upload" style="position: absolute; bottom: 5px; right: 5px; background: #4338ca; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                        <i class="fas fa-camera"></i>
                    </label>
                </div>
                
                <h3 style="margin: 0; color: #1e293b;"><?php echo htmlspecialchars($user['fullname'] ?? 'Admin User'); ?></h3>
                <p style="color: #64748b; margin-top: 5px;">Administrator</p>
                <div style="margin-top: 1.5rem; text-align: left;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: #475569;">
                        <i class="fas fa-envelope" style="width: 20px; color: #4338ca;"></i> <?php echo htmlspecialchars($user['email'] ?? 'admin@gmail.com'); ?>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px; color: #475569;">
                        <i class="fas fa-phone" style="width: 20px; color: #4338ca;"></i> <?php echo htmlspecialchars($user['phone'] ?? 'Not Set'); ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Edit Forms -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                
                <!-- Edit Details Form -->
                <div class="glass-card">
                    <h4 style="margin-bottom: 1.5rem; color: #1e293b; border-bottom: 1px solid rgba(0,0,0,0.05); padding-bottom: 10px;">Edit Details</h4>
                    
                    <form action="profile.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="profile_image" id="profile_upload" style="display: none;" onchange="this.form.submit()">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 5px; color: #475569; font-weight: 500;">Full Name</label>
                                <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname'] ?? 'Admin User'); ?>" class="glass-input" required>
                            </div>
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 5px; color: #475569; font-weight: 500;">Phone Number</label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="glass-input">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label style="display: block; margin-bottom: 5px; color: #475569; font-weight: 500;">Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? 'admin@gmail.com'); ?>" class="glass-input" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 5px; color: #475569; font-weight: 500;">Address</label>
                            <textarea name="address" rows="2" class="glass-input"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
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
                            <label style="display: block; margin-bottom: 5px; color: #475569; font-weight: 500;">Current Password</label>
                            <input type="password" name="current_password" class="glass-input" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 5px; color: #475569; font-weight: 500;">New Password</label>
                                <input type="password" name="new_password" class="glass-input" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 5px; color: #475569; font-weight: 500;">Confirm Password</label>
                                <input type="password" name="confirm_password" class="glass-input" required minlength="6">
                            </div>
                        </div>

                        <button type="submit" class="btn-primary-glass" style="background: #4338ca;">Update Password</button>
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
    .glass-input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid rgba(0,0,0,0.1);
        border-radius: 8px;
        background: rgba(255,255,255,0.5);
        outline: none;
        transition: all 0.3s;
    }
    .glass-input:focus {
        border-color: #4338ca;
        background: white;
        box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.1);
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
