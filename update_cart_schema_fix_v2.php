<?php
include 'db_connect.php';

// 1. Delete rows where user_id does not exist in users table (and user_id is not NULL)
// This cleans up "0" user_ids that were inserted before we made it nullable.
$clean_sql = "DELETE FROM cart WHERE user_id IS NOT NULL AND user_id NOT IN (SELECT id FROM users)";
if ($conn->query($clean_sql)) {
    echo "Cleaned up invalid user_id rows. Affected: " . $conn->affected_rows . "<br>";
} else {
    echo "Error cleaning rows: " . $conn->error . "<br>";
}

// 2. Try adding FK again
$add_fk = "ALTER TABLE `cart` ADD CONSTRAINT `cart_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE";
if ($conn->query($add_fk)) {
    echo "Added FK constraint cart_user_fk.<br>";
} else {
    echo "FK addition result (might preserve if exists): " . $conn->error . "<br>";
}

echo "Schema cleanup complete.";
?>
