<?php
// reset_password.php
include 'db_connect.php';
session_start();

$message = "";
$msg_type = "";
$token = $_GET['token'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic Validation
    if ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
        $msg_type = "error";
    } else {
        // Decode Token to get ID (Mock Logic)
        // Format: ID_SimulatedToken_Time
        $decoded = base64_decode($token);
        $parts = explode('_', $decoded);
        
        if (count($parts) >= 1 && is_numeric($parts[0])) {
            $user_id = $parts[0];
            
            // Validate ID exists
            $check = $conn->query("SELECT id FROM users WHERE id='$user_id'");
            if ($check->num_rows > 0) {
                // Update Password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $conn->query("UPDATE users SET password='$hashed_password' WHERE id='$user_id'");
                
                $message = "Password Reset Successfully! <br> <a href='login.php' style='color: yellow;'>Login Now</a>";
                $msg_type = "success";
            } else {
                $message = "Invalid User.";
                $msg_type = "error";
            }
        } else {
            $message = "Invalid or Expired Token.";
            $msg_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | E-Agriculture</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>

<div class="glass-container">
    <div class="glass-header">
        <h2>Reset Password</h2>
        <p>Set a new secure password.</p>
    </div>

    <?php if ($message != ""): ?>
        <div style="background: <?php echo ($msg_type=='success')?'rgba(0,128,0,0.3)':'rgba(255,0,0,0.2)'; ?>; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.2);">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($msg_type !== 'success'): ?>
    <form action="reset_password.php" method="POST" class="glass-form">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <div class="form-group">
            <label>New Password</label>
            <div class="glass-input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="new_password" required placeholder="New Password">
            </div>
        </div>
        
        <div class="form-group">
            <label>Confirm Password</label>
            <div class="glass-input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" required placeholder="Confirm Password">
            </div>
        </div>

        <button type="submit" class="glass-btn">Update Password</button>
    </form>
    <?php endif; ?>
</div>

</body>
</html>
