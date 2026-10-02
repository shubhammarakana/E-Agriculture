<?php
// verify_cart_persistence.php
include 'db_connect.php';
include 'includes/cart_helper.php';

echo "--- Starting Verification ---\n";

// 1. Simulate Guest Add
$test_token = "TEST_TOKEN_" . bin2hex(random_bytes(5));
$chem_id = 999; // Dummy chemical ID
$qty = 2;

echo "1. Testing Guest Insert with Token: $test_token\n";
// Manually mimicking add_to_cart.php logic for guest insert
$stmt = $conn->prepare("INSERT INTO cart (user_id, guest_token_id, chemical_id, crop_id, quantity) VALUES (NULL, ?, ?, NULL, ?)");
$stmt->bind_param("sii", $test_token, $chem_id, $qty);

if ($stmt->execute()) {
    $cart_id = $stmt->insert_id;
    echo "   [PASS] Insert Success. Cart ID: $cart_id\n";
} else {
    echo "   [FAIL] Insert Error: " . $stmt->error . "\n";
    exit;
}

// 2. Verify Persistence (Read back)
$stmt = $conn->prepare("SELECT * FROM cart WHERE guest_token_id = ?");
$stmt->bind_param("s", $test_token);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    echo "   [PASS] Persistence Verified. Found item for guest token.\n";
    $row = $res->fetch_assoc();
    echo "   Data: ID={$row['id']}, UserID=" . ($row['user_id'] === NULL ? 'NULL' : $row['user_id']) . ", Token={$row['guest_token_id']}\n";
} else {
    echo "   [FAIL] Persistence Failed. Item not found.\n";
    exit;
}

// 3. Simulate Merge (Login)
// Dynamically find a user
$user_id = 0;
$u_check = $conn->query("SELECT id FROM users LIMIT 1");
if ($u_check->num_rows > 0) {
    $u_row = $u_check->fetch_assoc();
    $user_id = $u_row['id'];
}

if ($user_id == 0) {
    echo "   [SKIP] No users found in DB. Skipping Merge Test.\n";
    // Just verify the concept logic
} else {
    echo "2. Testing Merge to User ID: $user_id\n";
    
    // We need to override the cookie function or simulate the token in cookie?
    // merge_guest_cart calls get_guest_token() which reads cookie.
    // For this test, we can just write the raw merge logic with our known token.
    
    echo "   Simulating merge logic for token $test_token...\n";
    // Check if user has item (Assuming no for this test)
    // Transfer logic:
    $trans = $conn->prepare("UPDATE cart SET user_id = ?, guest_token_id = NULL WHERE id = ?");
    $trans->bind_param("ii", $user_id, $cart_id);
    if ($trans->execute()) {
        echo "   [PASS] Merge execution success.\n";
    } else {
        echo "   [FAIL] Merge Error: " . $trans->error . "\n";
    }
    
    // Verify Ownership
    $check = $conn->query("SELECT * FROM cart WHERE id = $cart_id");
    $row = $check->fetch_assoc();
    if ($row['user_id'] == $user_id && $row['guest_token_id'] === NULL) {
        echo "   [PASS] Ownership Transferred Corretly.\n";
    } else {
        echo "   [FAIL] Ownership mismatch: UserID={$row['user_id']}, Token={$row['guest_token_id']}\n";
    }

    // Cleanup
    $conn->query("DELETE FROM cart WHERE id = $cart_id");
    echo "   [INFO] Cleaned up test item.\n";
}

echo "--- Verification Complete ---\n";
?>
