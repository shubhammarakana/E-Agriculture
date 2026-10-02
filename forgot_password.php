<?php
// forgot_password.php
include 'db_connect.php';
session_start();

$message = "";
$msg_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    
    // Check if email exists
    $sql = "SELECT id FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // In a real app, generate a secure token and email it.
        // Here, we SIMULATE the email by showing a success message with the link.
        $mock_token = base64_encode($row['id'] . "_SimulatedToken_" . time());
        
        $message = "Recovery link sent! (Simulation)<br> <a href='reset_password.php?token=$mock_token' style='color: yellow; text-decoration: underline; font-weight: bold;'>Click Here to Reset Password</a>";
        $msg_type = "success";
    } else {
        $message = "Email address not found.";
        $msg_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | E-Agriculture</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>

<div class="glass-container">
    <div class="glass-header">
        <h2>Forgot Password?</h2>
        <p>Enter your email to receive a recovery link.</p>
    </div>

    <?php if ($message != ""): ?>
        <div style="background: <?php echo ($msg_type=='success')?'rgba(0,128,0,0.3)':'rgba(255,0,0,0.2)'; ?>; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.2);">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="forgot_password.php" method="POST" class="glass-form">
        <div class="form-group">
            <label for="email">Email Address</label>
            <div class="glass-input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" required placeholder="Enter your registered email">
            </div>
        </div>

        <button type="submit" class="glass-btn">Send Link</button>
    </form>

    <div class="glass-footer">
        Remembered it? <a href="login.php">Back to Login</a>
    </div>
</div>

</body>
</html>
