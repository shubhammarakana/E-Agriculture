<?php
// test_vendor_simple.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: text/plain");

echo "START_TEST\n";

if (file_exists('db_connect.php')) {
    echo "Files exists: db_connect.php\n";
    include 'db_connect.php';
    echo "DB Connected.\n";
} else {
    echo "ERROR: db_connect.php not found.\n";
    exit;
}

if (!$conn) {
    echo "ERROR: Connection failed: " . mysqli_connect_error() . "\n";
    exit;
}

echo "DB object is valid.\n";

// Vendor Test
$email = "test_v_" . rand(1000,9999) . "@test.com";
$pass = "123456";
$hash = password_hash($pass, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (fullname, email, password, role, phone, status) VALUES ('TestV', '$email', '$hash', 'vendor', '0000', 'Pending')";
if ($conn->query($sql)) {
    echo "INSERT SUCCESS. ID: " . $conn->insert_id . "\n";
    $uid = $conn->insert_id;
    
    // Approve
    $new_pass = "abcdefg";
    $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
    $upd = "UPDATE users SET status='Active', password='$new_hash' WHERE id=$uid";
    if ($conn->query($upd)) {
         echo "UPDATE SUCCESS.\n";
         
         // Login Check
         $res = $conn->query("SELECT * FROM users WHERE id=$uid");
         $row = $res->fetch_assoc();
         if (password_verify($new_pass, $row['password'])) {
             echo "LOGIN VERIFY SUCCESS.\n";
         } else {
             echo "LOGIN VERIFY FAIL.\n";
         }
    } else {
        echo "UPDATE FAIL: " . $conn->error . "\n";
    }
} else {
    echo "INSERT FAIL: " . $conn->error . "\n";
}

echo "END_TEST\n";
?>
