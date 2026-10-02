<?php
// verify_otp.php
include 'db_connect.php';
session_start();

$type = $_GET['type'] ?? 'login'; // 'login' or 'register'
$message = "";
$msg_type = "";

$redirect = isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : (isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : '');

// Ensure we have an OTP session
if (!isset($_SESSION['otp'])) {
    $login_url = "login.php";
    if ($redirect) $login_url .= "?redirect=" . urlencode($redirect);
    header("Location: " . $login_url);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_otp = $_POST['otp'];

    if ($user_otp == $_SESSION['otp']) {
        // OTP Verified

        if ($type == 'register') {
            // Finalize Registration
            $data = $_SESSION['temp_user'];

            $sql = "INSERT INTO users (fullname, email, phone, role, password, profile_image) 
                    VALUES ('{$data['fullname']}', '{$data['email']}', '{$data['phone']}', '{$data['role']}', '{$data['password']}', '{$data['image']}')";

            if ($conn->query($sql) === TRUE) {
                // Clear session temp data
                unset($_SESSION['temp_user']);
                unset($_SESSION['otp']);
                if(isset($_SESSION['redirect_url'])) unset($_SESSION['redirect_url']);

                $message = "Account verified & created! Redirecting...";
                $msg_type = "success";
                
                $target = $redirect ? $redirect : 'login.php';
                echo "<script>setTimeout(()=>{ window.location.href='$target'; }, 500);</script>";
            } else {
                $message = "Error creating account: " . $conn->error;
                $msg_type = "error";
            }

        } elseif ($type == 'login') {
            // Finalize Login
            $data = $_SESSION['temp_login_user']; // Array from DB

            // Set Actual Session
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['fullname'] = $data['fullname'];
            $_SESSION['role'] = $data['role'];
            $_SESSION['profile_image'] = $data['profile_image'];

            unset($_SESSION['temp_login_user']);
            unset($_SESSION['otp']);
            if(isset($_SESSION['redirect_url'])) unset($_SESSION['redirect_url']);

            // Redirect based on role or explicit redirect param
            if ($redirect) {
                 header("Location: " . $redirect);
            } elseif ($data['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($data['role'] == 'farmer') {
                header("Location: farmer/dashboard.php");
            } elseif ($data['role'] == 'buyer') {
                header("Location: buyer/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        }
    } else {
        $message = "Invalid OTP. Please try again.";
        $msg_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verify OTP | E-Agriculture</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/auth.css">
    <style>
        .otp-display {
            background: rgba(255, 235, 59, 0.2);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px dashed rgba(255, 255, 255, 0.5);
        }

        .otp-code {
            font-size: 2rem;
            font-weight: bold;
            color: #fff;
            letter-spacing: 5px;
        }
    </style>
</head>

<body>

    <div class="glass-container">
        <div class="glass-header">
            <h2>Verify OTP</h2>
            <p>Enter the code sent to your email.</p>
        </div>

        <!-- SIMULATED OTP DISPLAY FOR LOCALHOST -->
        <div class="otp-display">
            <p style="margin:0; font-size:0.9rem; color:#ddd;">(Simulation) Your OTP is:</p>
            <div class="otp-code"><?php echo $_SESSION['otp']; ?></div>
        </div>

        <?php if ($message != ""): ?>
            <div
                style="background: <?php echo ($msg_type == 'success') ? 'rgba(0,128,0,0.3)' : 'rgba(255,0,0,0.2)'; ?>; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.2);">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="verify_otp.php?type=<?php echo $type; ?><?php echo $redirect ? '&redirect='.urlencode($redirect) : ''; ?>" method="POST" class="glass-form">
            <div class="form-group">
                <label>One-Time Password</label>
                <div class="glass-input-group">
                    <i class="fas fa-key"></i>
                    <input type="text" name="otp" required placeholder="Enter 4-digit OTP" maxlength="4"
                        pattern="[0-9]{4}">
                </div>
            </div>

            <button type="submit" class="glass-btn">Verify & Proceed</button>
        </form>

        <div class="glass-footer">
            <a href="login.php<?php echo $redirect ? '?redirect='.urlencode($redirect) : ''; ?>" style="color:#eee;">Cancel</a>
        </div>
    </div>

</body>

</html>