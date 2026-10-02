<?php
include 'db_connect.php';

$admins = [
    [
        'email' => 'darvora575@gmail.com',
        'password' => 'darvora7096503635',
        'fullname' => 'Darvora Admin'
    ],
    [
        'email' => 'shubhammarkana290@gmail.com',
        'password' => 'Shubham@#4409',
        'fullname' => 'Shubham Admin'
    ]
];

echo "<h2>Updating Admins...</h2>";

foreach ($admins as $admin) {
    $email = $admin['email'];
    $raw_pass = $admin['password'];
    $hash = password_hash($raw_pass, PASSWORD_DEFAULT);
    $name = $admin['fullname'];

    // Check if exists
    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        // Update
        $sql = "UPDATE users SET role='admin', password='$hash', status='Active' WHERE email='$email'";
        if ($conn->query($sql)) {
            echo "Updated existing admin: $email<br>";
        } else {
            echo "Error updating $email: " . $conn->error . "<br>";
        }
    } else {
        // Insert
        // Phone is required in DB structure (based on register.php logic), let's add dummy phone
        $phone = '0000000000'; 
        $sql = "INSERT INTO users (fullname, email, password, role, status, phone) VALUES ('$name', '$email', '$hash', 'admin', 'Active', '$phone')";
        if ($conn->query($sql)) {
            echo "Created new admin: $email<br>";
        } else {
            echo "Error creating $email: " . $conn->error . "<br>";
        }
    }
}
echo "<h3>Done. Please delete this file.</h3>";
?>
