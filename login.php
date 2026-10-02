<?php
// login.php
include 'db_connect.php';
session_start();

$message = "";
$msg_type = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Hardcoded Admin Login Removed - Using Database Authentication


    $redirect_script = "";

    $sql = "SELECT id, fullname, role, password, profile_image, phone, status FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Check Validation Status for Vendors
            if ($row['role'] == 'vendor' && $row['status'] == 'Blocked') {
                $message = "Your account has been " . $row['status'] . ".";
                $msg_type = "error";
            } elseif ($row['role'] == 'admin' && !in_array($email, ['darvora575@gmail.com', 'shubhammarakana290@gmail.com'])) {
                 $message = "Access restricted: Authorized personnel only.";
                 $msg_type = "error";
            } else {
                // Successful Login
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['fullname'] = $row['fullname'];
                $_SESSION['profile_image'] = $row['profile_image'];

                // Remember Me
                if (isset($_POST['remember_me'])) {
                    $token = bin2hex(random_bytes(32));
                    $conn->query("UPDATE users SET remember_token='$token' WHERE id='{$row['id']}'");
                    setcookie('remember_me', $token, time() + (86400 * 30), "/", "", false, true);
                }

                // Merge Guest Cart
                include_once 'includes/cart_helper.php';
                merge_guest_cart($row['id'], $conn);

                // Determine Redirect URL
                $url = "";
                if (isset($_POST['redirect']) && !empty($_POST['redirect'])) {
                    $url = urldecode($_POST['redirect']);
                } else {            
                    if ($row['role'] == 'farmer') $url = "farmer/dashboard.php";
                    elseif ($row['role'] == 'buyer') $url = "buyer/dashboard.php";
                    elseif ($row['role'] == 'admin') $url = "admin/dashboard.php";
                    elseif ($row['role'] == 'vendor') $url = "vendor/dashboard.php";
                }

                $message = "Login Successful! Redirecting...";
                $msg_type = "success";
                $redirect_script = "setTimeout(() => { window.location.href = '$url'; }, 1500);";
            }
        } else {
            $message = "Incorrect password.";
            $msg_type = "error";
        }
    } else {
        $message = "No account found with this email.";
        $msg_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | E-Agriculture</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/auth.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <style>
        .back-to-home {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            background: rgba(255, 255, 255, 0.95);
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            color: #16a34a;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid rgba(22, 163, 74, 0.2);
            transition: all 0.3s;
            z-index: 100;
        }
        .back-to-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            background: #ffffff;
        }
    </style>
    <a href="index.php" class="back-to-home">
        <i class="fas fa-arrow-left"></i> Home
    </a>

    <div class="glass-container-green">
        <div class="glass-header">
            <div class="brand-logo" style="width: 150px; height: auto; background: transparent; border-radius: 0; box-shadow: none;">
                <img src="<?php echo BASE_URL; ?>images/logo2.png" alt="AgriAI" style="width: 100%; height: auto; clip-path: inset(0 0 25% 0); margin-bottom: -20px;">
            </div>
            
            <?php if (isset($_GET['role']) && $_GET['role'] == 'vendor'): ?>
                <h2>Vendor Login</h2>
                <p>Admin & Vendor Portal</p>
            <?php else: ?>
                <h2>AgriAI Login</h2>
                <p>Welcome back to the future of farming</p>
            <?php endif; ?>
        </div>

        <?php if ($message != ""): ?>
            <script>
                Swal.fire({
                    icon: '<?php echo $msg_type; ?>',
                    title: '<?php echo ($msg_type == 'success') ? 'Success' : 'Error'; ?>',
                    text: '<?php echo $message; ?>',
                    showConfirmButton: false,
                    timer: <?php echo ($msg_type == 'success') ? 1500 : 3000; ?>
                });
                <?php echo $redirect_script; ?>
            </script>
        <?php endif; ?>

        <form action="login.php" method="POST" class="glass-form" id="loginForm">
            <!-- Hidden Fields -->
            <input type="hidden" name="role" id="roleInput" value="">
            <input type="hidden" name="redirect" value="<?php echo isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : ''; ?>">

            <div class="form-group">
                <div class="glass-input-group">
                    <input type="email" id="email" name="email" required placeholder="Email Address">
                    <i class="fas fa-envelope"></i>
                </div>
            </div>

            <div class="form-group">
                <div class="glass-input-group">
                    <input type="password" id="password" name="password" required placeholder="Password">
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 0.85rem; cursor: pointer;">
                    <input type="checkbox" name="remember_me" style="width: auto; margin: 0; accent-color: #16a34a;"> Remember me
                </label>
                <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
            </div>

            <button type="submit" class="glass-btn">
                Log In <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </form>

        <div class="glass-footer">
            <?php if (isset($_GET['role']) && $_GET['role'] == 'vendor'): ?>
                <a href="login.php" style="color: #4ade80; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-user"></i> Login as User
                </a>
                <div style="margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                     Don't have an account? <br>
                    <a href="register.php?role=vendor" style="color: #fbbf24; text-decoration: none; font-size: 0.9rem;">
                        Register as Vendor
                    </a>
                </div>
            <?php else: ?>
                <div style="margin-top: 15px;">
                    <a href="login.php?role=vendor" style="color: #fbbf24; text-decoration: none; font-size: 0.9rem;">
                        <i class="fas fa-store"></i> Login as Vendor
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/login.js"></script>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'logout'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Logged Out',
            text: 'You have been logged out successfully.',
            timer: 2000,
            showConfirmButton: false
        });
    </script>
    <?php endif; ?>
</body>

</html>