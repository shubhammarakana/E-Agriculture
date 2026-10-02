<?php
// send_otp.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['phone'])) {
    $phone = trim($_POST['phone']);

    // Basic Validation
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Phone Number']);
        exit();
    }

    // Generate Safe OTP
    $otp = rand(1000, 9999);
    
    // Store in Session for Verification
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_phone'] = $phone;
    $_SESSION['otp_expiry'] = time() + 300; // 5 minutes validity

    // --- SMS API INTEGRATION START ---
    // This is where you would integrate an SMS Gateway like Twilio, MSG91, TextLocal, etc.
    // Example:
    // sendSMS($phone, "Your AgriAI Verification Code is: $otp");
    // -------------------------------
    
    // For DEMO/TESTING purposes, we return the OTP in the response
    // In production, you would NEVER do this. You would just return 'success'.
    echo json_encode([
        'status' => 'success', 
        'message' => 'OTP Sent Successfully',
        'debug_otp' => $otp // REMOVE THIS IN PRODUCTION
    ]);
            
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
}
?>
