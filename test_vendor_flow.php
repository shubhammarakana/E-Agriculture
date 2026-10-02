<?php
// test_vendor_flow.php
// A standalone script to verify the vendor approval logic without UI interaction
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain');

include 'db_connect.php';

echo "VENDOR FLOW VERIFICATION\n";
echo "========================\n";

// 1. Create a Mock Pending Vendor
$test_email = "test_vendor_" . time() . "@example.com";
$test_pass = "initial_password";
$hashed_test_pass = password_hash($test_pass, PASSWORD_DEFAULT);
$role = "vendor";
$status = "Pending";
$fullname = "Test Vendor Bot";
$phone = "1234567890";

// Cleanup previous test
$conn->query("DELETE FROM users WHERE email LIKE 'test_vendor_%'");

echo "Creating pending vendor...\n";
$stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role, phone, status) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $fullname, $test_email, $hashed_test_pass, $role, $phone, $status);

if ($stmt->execute()) {
    $uid = $stmt->insert_id;
    echo "[PASS] Created Pending Vendor (ID: $uid)\n";
} else {
    die("[FAIL] Could not create vendor: " . $conn->error . "\n");
}

// 2. Simulate Admin Approval (Logic copied from vendor_requests.php)
echo "\nSimulating Admin Approval...\n";

// Logic from vendor_requests.php lines 28-32
$raw_password = substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789'), 0, 8);
$hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

echo "Generated Password: $raw_password\n";

// Update DB
$sql = "UPDATE users SET status='Active', verification_status='Verified', password='$hashed_password' WHERE id='$uid'";
if ($conn->query($sql)) {
    echo "[PASS] SQL Update executed.\n";
} else {
    die("[FAIL] SQL Update failed: " . $conn->error . "\n");
}

// 3. Verify DB State
$check = $conn->query("SELECT * FROM users WHERE id='$uid'")->fetch_assoc();
if ($check['status'] == 'Active' && $check['verification_status'] == 'Verified') {
    echo "[PASS] Database Status is Active/Verified.\n";
} else {
    echo "[FAIL] Database Status is " . $check['status'] . "\n";
}

// 4. Verify Login Logic
echo "\nVerifying Login with New Credentials...\n";
// Logic from login.php
// Mocking the inputs
$input_email = $test_email;
$input_password = $raw_password;

$l_sql = "SELECT id, fullname, role, password, status FROM users WHERE email='$input_email'";
$l_result = $conn->query($l_sql);

if ($l_result->num_rows > 0) {
    $row = $l_result->fetch_assoc();
    if (password_verify($input_password, $row['password'])) {
        if ($row['status'] == 'Pending') {
             echo "[FAIL] Login blocked (Pending).\n";
        } else {
             echo "[PASS] Login Successful! User is Active.\n";
        }
    } else {
        echo "[FAIL] Password verification failed.\n";
    }
} else {
    echo "[FAIL] User not found during login check.\n";
}

// Cleanup
echo "\nCleaning up...\n";
// $conn->query("DELETE FROM users WHERE id='$uid'");
// echo "Test user deleted.\n";

?>

