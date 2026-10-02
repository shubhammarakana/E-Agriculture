<?php
include 'db_connect.php';

$old_email = "shubhammarakna111@gmil.com";
$new_email = "shubhammarakna111@gmail.com";

$sql = "UPDATE users SET email='$new_email' WHERE email='$old_email'";
if ($conn->query($sql) === TRUE) {
    if ($conn->affected_rows > 0) {
        echo "Success: Email updated from '$old_email' to '$new_email'.";
    } else {
        echo "Notice: No user found with email '$old_email'. It might have been fixed already.";
    }
} else {
    echo "Error updating record: " . $conn->error;
}
?>
