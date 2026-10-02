<?php
include 'db_connect.php';

// 1. Drop existing Foreign Keys if they exist (to allow modifying columns)
// We need to know the constraint names. Usually 'cart_ibfk_1'.
$drop_fk = "ALTER TABLE `cart` DROP FOREIGN KEY `cart_ibfk_1`"; 
$conn->query($drop_fk); // Attempt drop, might fail if name different or not exists, that's "okay" for now, we'll try to proceed.

// Better approach: Check if column exists, if not add it.
function addColumnIfNeeded($conn, $table, $col, $def) {
    $check = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$col'");
    if ($check->num_rows == 0) {
        if ($conn->query("ALTER TABLE `$table` ADD `$col` $def")) {
            echo "Added column $col.<br>";
        } else {
            echo "Error adding $col: " . $conn->error . "<br>";
        }
    } else {
        echo "Column $col already exists.<br>";
    }
}

// 2. Add guest_token_id
addColumnIfNeeded($conn, 'cart', 'guest_token_id', "VARCHAR(255) DEFAULT NULL");

// 3. Add chemical_id
addColumnIfNeeded($conn, 'cart', 'chemical_id', "INT(6) UNSIGNED DEFAULT NULL AFTER user_id");

// 4. Modify user_id to be NULLABLE
$mod_user = "ALTER TABLE `cart` MODIFY `user_id` INT(6) UNSIGNED NULL";
if ($conn->query($mod_user)) {
    echo "Modified user_id to be NULLABLE.<br>";
} else {
    echo "Error modifying user_id: " . $conn->error . "<br>";
}

// 5. Modify crop_id to be NULLABLE (since we can have chemical_id instead)
$mod_crop = "ALTER TABLE `cart` MODIFY `crop_id` INT(6) UNSIGNED NULL";
if ($conn->query($mod_crop)) {
    echo "Modified crop_id to be NULLABLE.<br>";
} else {
    echo "Error modifying crop_id: " . $conn->error . "<br>";
}

// 6. Re-add Foreign Key for user_id (optional, but good for integrity if user_id is set)
// Note: We perform this only if we want to enforce it for non-nulls. 
// MySQL ignores FK for NULL values, which is exactly what we want.
$add_fk = "ALTER TABLE `cart` ADD CONSTRAINT `cart_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE";
if ($conn->query($add_fk)) {
    echo "Added FK constraint cart_user_fk.<br>";
} else {
    // If it fails, it might be because it already exists or data inconsistency. 
    // We try to add it with a unique name to avoid conflict with old 'cart_ibfk_1' if it wasn't dropped.
    echo "Note: FK addition might have failed if constraint exists or data mismatch: " . $conn->error . "<br>";
}

// 7. Add Index for guest token
$idx_sql = "ALTER TABLE `cart` ADD INDEX(`guest_token_id`)";
$conn->query($idx_sql);

echo "Schema update complete.";
?>
