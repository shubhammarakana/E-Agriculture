<?php
// includes/cart_helper.php

/**
 * Get or Create a Guest Token for persistent cart
 * @return string The guest token
 */
function get_guest_token() {
    if (isset($_COOKIE['guest_token'])) {
        return $_COOKIE['guest_token'];
    } else {
        // Generate new token
        $token = bin2hex(random_bytes(32));
        // Set cookie for 30 days (httponly recommended but we might need JS access later, let's keep simple)
        // Path is root.
        setcookie('guest_token', $token, time() + (86400 * 30), "/");
        // Also set in $_COOKIE for immediate use in same request
        $_COOKIE['guest_token'] = $token;
        return $token;
    }
}

/**
 * Merge Guest Cart items to User Cart after login
 * @param int $user_id The logged-in user ID
 * @param mysqli $conn Database connection
 */
function merge_guest_cart($user_id, $conn) {
    $guest_token = get_guest_token();

    // 1. Get Guest Items
    $sql = "SELECT * FROM cart WHERE guest_token_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $guest_token);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $cart_id = $row['id'];
        $crop_id = $row['crop_id']; // Can be NULL
        $chem_id = $row['chemical_id']; // Can be NULL
        $qty = $row['quantity'];

        // 2. Check if user already has this item
        if ($chem_id) {
            $check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND chemical_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ii", $user_id, $chem_id);
        } else {
             $check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND crop_id = ?";
             $check_stmt = $conn->prepare($check_sql);
             $check_stmt->bind_param("ii", $user_id, $crop_id);
        }
        
        $check_stmt->execute();
        $check_res = $check_stmt->get_result();

        if ($check_res->num_rows > 0) {
            // User has item -> Merge Quantity & Delete Guest Item
            $existing = $check_res->fetch_assoc();
            $new_qty = $existing['quantity'] + $qty;
            
            $upd = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $upd->bind_param("ii", $new_qty, $existing['id']);
            $upd->execute();

            $del = $conn->prepare("DELETE FROM cart WHERE id = ?");
            $del->bind_param("i", $cart_id);
            $del->execute();
        } else {
            // User doesn't have item -> Transfer ownership
            // Update user_id and remove guest_token_id to finalize transfer
            $trans = $conn->prepare("UPDATE cart SET user_id = ?, guest_token_id = NULL WHERE id = ?");
            $trans->bind_param("ii", $user_id, $cart_id);
            $trans->execute();
        }
    }
}
?>
