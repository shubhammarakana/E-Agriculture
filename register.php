<?php
// register.php
include 'db_connect.php';
session_start();

$message = "";
$msg_type = "";
$redirect_script = "";

// Check for Role in URL (e.g. register.php?role=farmer)
$url_role = isset($_GET['role']) ? $_GET['role'] : 'farmer';

// Whitelist Allowed Roles (Security Fix: Prevent Admin Registration)
$allowed_roles = ['farmer', 'buyer', 'vendor'];
if (!in_array($url_role, $allowed_roles)) {
    $url_role = 'farmer'; // Default fallback
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = !empty($_POST['role']) ? $_POST['role'] : $url_role;
    // Double check POST data
    if (!in_array($role, $allowed_roles)) {
        die("Invalid role selected.");
    }

    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; // Will be hashed
    $phone = $conn->real_escape_string($_POST['phone']);

    // Validation
    // Check if email exists
    $checkEmail = "SELECT id FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        $message = "Email already registered.";
        $msg_type = "error";
    } else {
        // Handle File Upload
        $profile_image = "";
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            // Use absolute path
            $target_dir = __DIR__ . "/images/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_ext = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $new_filename;
            
            // For DB storage (relative path)
            $db_image_path = "images/" . $new_filename;

            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_ext, $allowed)) {
                if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                    $profile_image = $db_image_path;
                }
            }
        }

        // Determine Status
        $status = ($role == 'vendor') ? 'Pending' : 'Active';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert User
        $sql = "INSERT INTO users (fullname, email, password, role, phone, profile_image, status) 
                VALUES ('$fullname', '$email', '$hashed_password', '$role', '$phone', '$profile_image', '$status')";

        if ($conn->query($sql) === TRUE) {
            if ($role == 'vendor') {
                $message = "Registration successfully wait for admin response";
                $msg_type = "success";
                $redirect_script = "setTimeout(() => window.location.href='login.php', 3000);";
            } else {
                // Auto-Login
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['role'] = $role;
                $_SESSION['fullname'] = $fullname;
                $_SESSION['profile_image'] = $profile_image; // Empty or path

                // Merge Guest Cart
                include_once 'includes/cart_helper.php';
                merge_guest_cart($_SESSION['user_id'], $conn);

                // Redirect
                $redirect_url = isset($_REQUEST['redirect']) ? urldecode($_REQUEST['redirect']) : 'login.php';
                
                // If redirect is login.php (default), send to dashboard instead since we are logged in
                if ($redirect_url == 'login.php') {
                    if ($role == 'vendor') $redirect_url = 'vendor/dashboard.php';
                    elseif ($role == 'buyer') $redirect_url = 'buyer/dashboard.php';
                    elseif ($role == 'admin') $redirect_url = 'admin/dashboard.php';
                    
                    // For dashboard redirection, keep the success message
                    $message = "Registration successful! Redirecting...";
                    $msg_type = "success";
                    $redirect_script = "setTimeout(() => window.location.href='$redirect_url', 1500);";
                } else {
                    // For Checkout or other specific flows, Redirect INSTANTLY (skip message)
                    header("Location: " . $redirect_url);
                    exit();
                }
            }
        } else {
            $message = "Error: " . $conn->error;
            $msg_type = "error";
        }
    }
}
$redirect_param = isset($_REQUEST['redirect']) ? '?redirect=' . urlencode($_REQUEST['redirect']) : (isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join E-Agriculture</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <!-- Using the new Green Glass Container -->
    <div class="glass-container-green">
        <!-- Floating Home Button -->
        <a href="index.php" class="back-home-btn" title="Back to Home">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="glass-header">
            <div class="brand-logo" style="width: 150px; height: auto; background: transparent; border-radius: 0; box-shadow: none;">
                <img src="<?php echo BASE_URL; ?>images/logo2.png" alt="AgriAI" style="width: 100%; height: auto; clip-path: inset(0 0 25% 0); margin-bottom: -20px;">
            </div>
            
            <?php if ($url_role == 'vendor'): ?>
                <h2>Vendor Registration</h2>
                <p>Join as a partner</p>
            <?php else: ?>
                <h2>Create Account</h2>
                <p>Join the future of agriculture</p>
            <?php endif; ?>
        </div>

        <?php if ($message != ""): ?>
            <script>
                Swal.fire({
                    icon: '<?php echo $msg_type; ?>',
                    title: '<?php echo ($msg_type == 'success') ? 'Success' : 'Error'; ?>',
                    text: '<?php echo $message; ?>',
                    timer: <?php echo ($msg_type == 'success') ? 1500 : 3000; ?>,
                    showConfirmButton: false
                });
                <?php echo $redirect_script; ?>
            </script>
        <?php endif; ?>

        <form action="register.php<?php echo $redirect_param; ?>" method="POST" enctype="multipart/form-data" class="glass-form" id="registerForm">
            
            <!-- Role (Hidden) -->
            <?php if ($url_role == 'vendor'): ?>
                <input type="hidden" name="role" id="roleInput" value="vendor">
            <?php else: ?>
                <input type="hidden" name="role" id="roleInput" value="farmer">
            <?php if (isset($_REQUEST['redirect'])): ?>
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_REQUEST['redirect']); ?>">
            <?php endif; ?>
            <?php endif; ?>

            <!-- Profile Image Upload -->
            <div class="image-upload-wrapper">
                <div class="preview-box">
                    <i class="fas fa-user-circle" id="defaultIcon"></i>
                    <img id="imagePreview" src="#" alt="Profile" style="display: none;">
                </div>
                <div class="upload-btn-wrapper">
                    <button type="button" class="btn-upload">Upload Photo</button>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                </div>
            </div>

            <!-- Vertical Layout (No Grid) -->
            <div class="form-group">
                <label id="nameLabel" for="fullname">Full Name</label>
                <div class="glass-input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="fullname" name="fullname" required placeholder="John Doe">
                </div>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="glass-input-group">
                    <i class="fas fa-phone"></i>
                    <input type="tel" id="phone" name="phone" required placeholder="10-digit number" pattern="[0-9]{10}">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="glass-input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" required placeholder="name@example.com">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="glass-input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required placeholder="Create password">
                </div>
            </div>
            
            <!-- Password Strength Bar -->
            <div id="pwd-strength-bar" style="height: 4px; border-radius: 2px; margin-top: -10px; margin-bottom: 20px; transition: width 0.3s; width: 0%;"></div>

            <button type="submit" class="glass-btn">
                Create Account <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </form>

        <div class="glass-footer">
            Already have an account? <a href="login.php">Login Here</a>
        </div>
    </div>

    <script src="js/register.js"></script>

</body>
</html>