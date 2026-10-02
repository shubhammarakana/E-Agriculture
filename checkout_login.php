<?php
// checkout_login.php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['phone'])) {
    $phone = $conn->real_escape_string($_POST['phone']);
    // OTP Verification OR Password Verification
    $input_otp = isset($_POST['otp']) ? $_POST['otp'] : (isset($_POST['otp_reg']) ? $_POST['otp_reg'] : '');
    $pass_login = isset($_POST['login_password']) ? $_POST['login_password'] : '';

    // Check if New User Registration
    $is_new = isset($_POST['is_new_user']) && $_POST['is_new_user'] == '1';

    if ($is_new) {
        // REGISTER (No OTP required for Cart flow, or if OTP provided check it)
        // If OTP provided (e.g. from checkout.php legacy), verify it. If removed (cart.php), skip.
        if (!empty($input_otp)) {
            if (isset($_SESSION['otp']) && isset($_SESSION['otp_phone']) && $phone === $_SESSION['otp_phone'] && $input_otp == $_SESSION['otp']) {
                unset($_SESSION['otp']);
                unset($_SESSION['otp_phone']);
            } else {
                echo "<script>alert('Invalid OTP.'); window.location.href='checkout.php';</script>";
                exit();
            }
        }

        $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : 'Guest User';
        $email = isset($_POST['email']) ? $_POST['email'] : ($phone . '@guest.com');
        $raw_pass = isset($_POST['password']) ? $_POST['password'] : 'guest123';
        $password = password_hash($raw_pass, PASSWORD_DEFAULT);
        $role = 'buyer';

        // Check if user already exists (by Phone)
        $chk = $conn->prepare("SELECT id FROM users WHERE phone=?");
        $chk->bind_param("s", $phone);
        $chk->execute();
        $res_chk = $chk->get_result();

        if ($res_chk->num_rows > 0) {
            $row = $res_chk->fetch_assoc();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $fullname;
            $_SESSION['role'] = 'buyer';
        } else {
            // NEW: Check if email already exists for another account
            $chk_email = $conn->prepare("SELECT id FROM users WHERE email=?");
            $chk_email->bind_param("s", $email);
            $chk_email->execute();
            if ($chk_email->get_result()->num_rows > 0) {
                echo "<script>alert('Error: This email address is already registered. Please login with your existing account or use a different email.'); window.history.back();</script>";
                exit();
            }

            $insert_sql = "INSERT INTO users (fullname, email, phone, password, role) VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sssss", $fullname, $email, $phone, $password, $role);
            if ($insert_stmt->execute()) {
                $_SESSION['user_id'] = $insert_stmt->insert_id;
                $_SESSION['username'] = $fullname;
                $_SESSION['role'] = $role;
            } else {
                header("Location: cart.php?error=db_error");
                exit();
            }
        }

    } else {
        // LOGIN (Existing)
        // Check if using Password or OTP
        $authenticated = false;

        // 1. Password Login
        if (!empty($pass_login)) {
            $stmt = $conn->prepare("SELECT id, fullname, role, password, email FROM users WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                if (password_verify($pass_login, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];

                    // Handle Profile Update (if editable fields changed)
                    $new_name = isset($_POST['login_fullname']) ? $_POST['login_fullname'] : $row['fullname'];
                    $new_email = isset($_POST['login_email']) ? $_POST['login_email'] : $row['email'];

                    if ($new_name !== $row['fullname'] || $new_email !== $row['email']) {
                        $upd = $conn->prepare("UPDATE users SET fullname=?, email=? WHERE id=?");
                        $upd->bind_param("ssi", $new_name, $new_email, $row['id']);
                        $upd->execute();
                        $_SESSION['username'] = $new_name;
                    } else {
                        $_SESSION['username'] = $row['fullname'];
                    }

                    $_SESSION['role'] = $row['role'];
                    $authenticated = true;
                } else {
                    echo "<script>alert('Incorrect Password.'); window.history.back();</script>";
                    exit();
                }
            } else {
                header("Location: checkout.php?error=user_not_found");
                exit();
            }
        }
        // 2. OTP Login
        elseif (!empty($input_otp)) {
            if (isset($_SESSION['otp']) && isset($_SESSION['otp_phone']) && $phone === $_SESSION['otp_phone'] && $input_otp == $_SESSION['otp']) {
                unset($_SESSION['otp']);
                unset($_SESSION['otp_phone']);
                // Fetch User
                $stmt = $conn->prepare("SELECT id, fullname, role FROM users WHERE phone = ?");
                $stmt->bind_param("s", $phone);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['fullname'];
                    $_SESSION['role'] = $row['role'];
                    $authenticated = true;
                }
            } else {
                echo "<script>alert('Invalid OTP.'); window.location.href='checkout.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Authentication required.'); window.location.href='checkout.php';</script>";
            exit();
        }

        if (!$authenticated) {
            exit();
        }
    }

    // Merge Guest Cart (Persistent DB)
    include_once 'includes/cart_helper.php';
    merge_guest_cart($_SESSION['user_id'], $conn);

    // Clear legacy session cart if any (cleanup)
    if (isset($_SESSION['guest_cart'])) {
        unset($_SESSION['guest_cart']);
    }

    // Redirect to Checkout with Direct Buy Params Preservation
    $redirect_url = "checkout.php";
    if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
        $pid = urlencode($_POST['product_id']);
        $qty = isset($_POST['qty']) ? urlencode($_POST['qty']) : 1;
        $type = isset($_POST['type']) ? urlencode($_POST['type']) : 'crop';
        $redirect_url .= "?product_id=$pid&qty=$qty&type=$type";
    }

    header("Location: " . $redirect_url);
    exit();
} else {
    header("Location: cart.php");
    exit();
}
