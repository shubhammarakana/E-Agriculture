<?php
// add_to_cart.php
include 'db_connect.php';
session_start();

function log_cart($msg) {
    $logFile = 'cart_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $msg" . PHP_EOL, FILE_APPEND);
}

log_cart("--- New Add Request ---");
log_cart("POST Data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $quantity = isset($_POST['qty']) ? intval($_POST['qty']) : 1;
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'shop';
    $item_type = isset($_POST['type']) ? $_POST['type'] : 'crop'; // 'crop' or 'chemical'

    log_cart("Parsed: ID=$item_id, Qty=$quantity, Type=$item_type, Redirect=$redirect");

    if ($item_id > 0 && $quantity > 0) {
        if (isset($_SESSION['user_id'])) {
            // LOGGED IN: Database Cart
            $user_id = $_SESSION['user_id'];
            log_cart("User Logged In: ID=$user_id");
            
            if ($item_type == 'chemical') {
                $check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND chemical_id = ?";
                $col = "chemical_id";
            } elseif ($item_type == 'seed') {
                $check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND seed_id = ?";
                $col = "seed_id";
            } else {
                $check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND crop_id = ?";
                $col = "crop_id";
            }

            $stmt = $conn->prepare($check_sql);
            if (!$stmt) log_cart("Prepare failed (Check): " . $conn->error);
            $stmt->bind_param("ii", $user_id, $item_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Update existing
                $row = $result->fetch_assoc();
                $new_qty = $row['quantity'] + $quantity;
                log_cart("Item exists. Updating ID {$row['id']} to Qty $new_qty");
                
                $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ii", $new_qty, $row['id']);
                if ($update_stmt->execute()) {
                    log_cart("Update Success");
                } else {
                    log_cart("Update Failed: " . $update_stmt->error);
                }
            } else {
                // Insert new
                log_cart("Item not found in cart. Inserting new.");
                if ($item_type == 'chemical') {
                    $insert_sql = "INSERT INTO cart (user_id, chemical_id, crop_id, seed_id, quantity) VALUES (?, ?, NULL, NULL, ?)";
                } elseif ($item_type == 'seed') {
                     $insert_sql = "INSERT INTO cart (user_id, chemical_id, crop_id, seed_id, quantity) VALUES (?, NULL, NULL, ?, ?)";
                } else {
                    $insert_sql = "INSERT INTO cart (user_id, chemical_id, crop_id, seed_id, quantity) VALUES (?, NULL, ?, NULL, ?)";
                }
                
                $insert_stmt = $conn->prepare($insert_sql);
                if (!$insert_stmt) log_cart("Prepare failed (Insert): " . $conn->error);
                
                $insert_stmt->bind_param("iii", $user_id, $item_id, $quantity);
                try {
                    if ($insert_stmt->execute()) {
                        log_cart("Insert Success. ID: " . $insert_stmt->insert_id);
                    } else {
                        log_cart("Insert Failed (Execute return false): " . $insert_stmt->error);
                    }
                } catch (Exception $e) {
                    log_cart("Insert Failed (Exception): " . $e->getMessage());
                }
            }
        } else {
            // GUEST: Database Cart (Persistent)
            include_once 'includes/cart_helper.php';
            $guest_token = get_guest_token();
            log_cart("Guest User. Using Token: $guest_token");
            
            // 1. Check if item exists for this guest token
            if ($item_type == 'chemical') {
                $check_sql = "SELECT id, quantity FROM cart WHERE guest_token_id = ? AND chemical_id = ?";
            } elseif ($item_type == 'seed') {
                $check_sql = "SELECT id, quantity FROM cart WHERE guest_token_id = ? AND seed_id = ?";
            } else {
                $check_sql = "SELECT id, quantity FROM cart WHERE guest_token_id = ? AND crop_id = ?";
            }

            $stmt = $conn->prepare($check_sql);
            if ($stmt) {
                $stmt->bind_param("si", $guest_token, $item_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Update existing
                    $row = $result->fetch_assoc();
                    $new_qty = $row['quantity'] + $quantity;
                    log_cart("Guest Item exists. Updating ID {$row['id']} to Qty $new_qty");
                    
                    $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("ii", $new_qty, $row['id']);
                    if ($update_stmt->execute()) {
                        log_cart("Guest Update Success");
                    } else {
                        log_cart("Guest Update Failed: " . $update_stmt->error);
                    }
                } else {
                    // Insert new
                    log_cart("Guest Item not found. Inserting new.");
                    // user_id MUST BE NULL
                    if ($item_type == 'chemical') {
                        $insert_sql = "INSERT INTO cart (user_id, guest_token_id, chemical_id, crop_id, seed_id, quantity) VALUES (NULL, ?, ?, NULL, NULL, ?)";
                    } elseif ($item_type == 'seed') {
                         $insert_sql = "INSERT INTO cart (user_id, guest_token_id, chemical_id, crop_id, seed_id, quantity) VALUES (NULL, ?, NULL, NULL, ?, ?)";
                    } else {
                        $insert_sql = "INSERT INTO cart (user_id, guest_token_id, chemical_id, crop_id, seed_id, quantity) VALUES (NULL, ?, NULL, ?, NULL, ?)";
                    }
                    
                    $insert_stmt = $conn->prepare($insert_sql);
                    if ($insert_stmt) {
                        $insert_stmt->bind_param("sii", $guest_token, $item_id, $quantity);
                        try {
                            if ($insert_stmt->execute()) {
                                log_cart("Guest Insert Success. ID: " . $insert_stmt->insert_id);
                            } else {
                                log_cart("Guest Insert Failed: " . $insert_stmt->error);
                            }
                        } catch (Exception $e) {
                            log_cart("Guest Insert Error: " . $e->getMessage());
                        }
                    } else {
                        log_cart("Prepare failed (Guest Insert): " . $conn->error);
                    }
                }
            } else {
                log_cart("Prepare failed (Guest Check): " . $conn->error);
            }
        }
    } else {
        log_cart("Invalid Item ID or Qty");
    }

    if ($redirect == 'index') {
        header("Location: index.php");
    } elseif ($redirect == 'cart') {
        header("Location: cart.php");
    } elseif ($redirect == 'chemical_store') {
        header("Location: chemical_store.php");
    } elseif ($redirect == 'seed_store') {
        header("Location: seed_store.php");
    } else {
        header("Location: shop_crops.php");
    }
    exit();
} else {
    log_cart("Invalid Request Method: " . $_SERVER['REQUEST_METHOD']);
    header("Location: shop_crops.php");
    exit();
}
?>
