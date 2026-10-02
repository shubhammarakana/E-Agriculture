<?php
// admin/vendor_requests.php
include '../db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle Actions (Approve/Reject)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $uid = $conn->real_escape_string($_GET['id']);
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        // 1. Fetch User Details to get Email
        $user_query = $conn->query("SELECT email, fullname FROM users WHERE id='$uid'");
        if ($user_query->num_rows > 0) {
            $user_data = $user_query->fetch_assoc();
            $email_to = $user_data['email'];
            $vendor_name = $user_data['fullname'];

            // 2. Generate Random Password
            $raw_password = substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789'), 0, 8);
            $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

            // 3. Update Database with new Password & Status
            $sql = "UPDATE users SET status='Active', verification_status='Verified', password='$hashed_password' WHERE id='$uid'";
            
            if ($conn->query($sql)) {
                // 4. Send Email
                $subject = "Vendor Application Approved - AgriAI";
                $message = "Dear $vendor_name,\n\nYour vendor application has been approved.\n\nHere are your login credentials:\nEmail: $email_to\nPassword: $raw_password\n\nPlease login at: " . BASE_URL . "login.php\n\nBest Regards,\nAgriAI Team";
                $headers = "From: no-reply@agriai.com";

                // Attempt to send email (suppress errors for local dev)
                @mail($email_to, $subject, $message, $headers);

                // Success Message (Show Password for Admin Reference/Testing)
                $msg = "Vendor approved successfully! Email sent.";
                $icon = "success";
                
                // Pass password and email in URL to show in SweetAlert
                header("Location: vendor_requests.php?msg=" . urlencode($msg) . "&icon=" . $icon . "&new_pass=" . urlencode($raw_password) . "&email=" . urlencode($email_to));
                exit();
            }
        }
    } elseif ($action == 'reject') {
        $sql = "UPDATE users SET status='Rejected' WHERE id='$uid'";
        $msg = "Vendor request rejected.";
        $icon = "warning";
        
        if ($conn->query($sql)) {
            header("Location: vendor_requests.php?msg=" . urlencode($msg) . "&icon=" . $icon);
            exit();
        }
    } elseif ($action == 'delete') {
        // Delete User
        $sql = "DELETE FROM users WHERE id='$uid'";
        $msg = "Vendor deleted successfully.";
        $icon = "info";
        
        if ($conn->query($sql)) {
            header("Location: vendor_requests.php?msg=" . urlencode($msg) . "&icon=" . $icon);
            exit();
        }
    }
}

$page = 'vendor_requests'; // For sidebar active state if used
$page_title = 'Vendor Requests';
include '../includes/main_header.php';

// Count Pending for Badge
$count_pending = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='vendor' AND status='Pending'")->fetch_assoc()['c'];

// Fetch ALL Vendors (Sorted: Pending first)
$sql = "SELECT * FROM users WHERE role='vendor' ORDER BY CASE WHEN status = 'Pending' THEN 0 ELSE 1 END, created_at DESC";
$result = $conn->query($sql);
if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>

<div class="container" style="padding: 2rem 0;">
    <h1 style="margin-bottom: 2rem; color: #1f2937;">Vendor Requests</h1>

    <!-- Stats Card -->
    <div class="glass-panel" style="margin-bottom: 2rem; padding: 1.5rem; display: flex; align-items: center; gap: 20px; border-left: 5px solid #d97706;">
        <div style="background: #fffbeb; padding: 15px; border-radius: 50%; color: #d97706;">
            <i class="fas fa-user-clock fa-2x"></i>
        </div>
        <div>
            <h3 style="margin: 0; font-size: 1.5rem; color: #1e293b;"><?php echo $count_pending; ?></h3>
            <p style="margin: 0; color: #64748b;">Pending Requests</p>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="glass-panel">
        <?php if ($result->num_rows > 0): ?>
            <div style="overflow-x: auto;">
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>Vendor Name</th>
                            <th>Contact Info</th>
                            <th>Status</th>
                            <th>Date Requested</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr style="background: rgba(255,255,255,0.3); <?php echo ($row['status']!='Pending') ? 'opacity:0.7;' : ''; ?>">
                                <td style="color: #333;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 35px; height: 35px; background: #f1f5f9; border-radius: 50%; overflow: hidden;">
                                            <?php if ($row['profile_image']): ?>
                                                <img src="../<?php echo htmlspecialchars($row['profile_image']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #cbd5e1;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div style="font-weight: 600; color: #1e293b;"><?php echo htmlspecialchars($row['fullname']); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 0.9rem;">
                                        <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" style="color: #64748b; text-decoration: none; display:block;">
                                            <i class="fas fa-envelope" style="width:15px;"></i> <?php echo htmlspecialchars($row['email']); ?>
                                        </a>
                                        <a href="tel:<?php echo htmlspecialchars($row['phone']); ?>" style="color: #64748b; text-decoration: none; display:block; margin-top:2px;">
                                            <i class="fas fa-phone" style="width:15px;"></i> <?php echo htmlspecialchars($row['phone']); ?>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                        $badge_color = 'bg-gray-100 text-gray-800';
                                        if($row['status'] == 'Pending') $badge_color = 'background:#fef3c7; color:#d97706;';
                                        if($row['status'] == 'Active') $badge_color = 'background:#dcfce7; color:#16a34a;';
                                        if($row['status'] == 'Rejected') $badge_color = 'background:#fee2e2; color:#991b1b;';
                                    ?>
                                    <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; <?php echo $badge_color; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="color: #64748b; font-size: 0.9rem;">
                                        <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                        <a href="vendor_requests.php?action=approve&id=<?php echo $row['id']; ?>" 
                                           class="btn btn-action btn-approve"
                                           onclick="return confirm('Approve this vendor account?');"
                                           title="Approve">
                                            <i class="fas fa-check"></i> 
                                            <?php echo ($row['status']=='Pending') ? 'Approve' : 'Re-Approve'; ?>
                                        </a>
                                        <a href="vendor_requests.php?action=reject&id=<?php echo $row['id']; ?>" 
                                           class="btn btn-action btn-reject"
                                           onclick="return confirm('Are you sure you want to REJECT this vendor?');"
                                           title="Reject / Ban Vendor">
                                            <i class="fas fa-times"></i> Reject
                                        </a>
                                        <a href="vendor_requests.php?action=delete&id=<?php echo $row['id']; ?>" 
                                           class="btn btn-action btn-delete"
                                           onclick="return confirm('DANGER: This will permanently DELETE the vendor account and all associated data.\nAre you absolutely sure?');"
                                           title="Permanently Delete Vendor">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem 2rem;">
                <!-- Empty State -->
                <div style="background: #f1f5f9; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: #94a3b8;">
                    <i class="fas fa-users-slash fa-3x"></i>
                </div>
                <h3 style="color: #334155; margin-bottom: 0.5rem;">No Vendors Found</h3>
                <p style="color: #64748b;">There are no registered vendors in the system.</p>
            </div>
        <?php endif; ?>
    </div>
    </div>
</div>

<style>
    .btn-action {
        padding: 6px 15px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-approve {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .btn-approve:hover {
        background-color: #16a34a;
        color: white;
        border-color: #16a34a;
        box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.2);
    }

    .btn-reject {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    .btn-reject:hover {
        background-color: #ef4444;
        color: white;
        border-color: #ef4444;
        box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);
    }

    .btn-delete {
        background-color: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
        padding: 6px 10px; /* Smaller padding for icon only or compact */
    }
    .btn-delete:hover {
        background-color: #475569;
        color: white;
        border-color: #475569;
    }
</style>

<?php if (isset($_GET['msg'])): ?>
<script>
    <?php if (isset($_GET['new_pass'])): ?>
        // Persistent Alert for New Password
        Swal.fire({
            icon: 'success',
            title: 'Vendor Approved!',
            html: `
                <p>The vendor has been approved.</p>
                <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; margin-top: 15px; border: 1px dashed #cbd5e1; text-align: left;">
                    <small style="color: #64748b; display: block; margin-bottom: 5px; font-weight:600;">Username / Email:</small>
                    <div style="background:white; padding:8px; border-radius:4px; border:1px solid #e2e8f0; margin-bottom:10px; color:#333;">
                        <?php echo !empty($_GET['email']) ? htmlspecialchars($_GET['email']) : 'N/A'; ?>
                    </div>
                    
                    <small style="color: #64748b; display: block; margin-bottom: 5px; font-weight:600;">Password:</small>
                    <div style="display:flex; gap:5px;">
                        <input type="text" id="genPass" value="<?php echo !empty($_GET['new_pass']) ? htmlspecialchars($_GET['new_pass']) : ''; ?>" readonly 
                            style="flex:1; background:white; padding:8px; border-radius:4px; border:1px solid #e2e8f0; color:#16a34a; font-weight:700;">
                        <button onclick="copyPassword()" style="background:#e2e8f0; border:none; padding:0 12px; border-radius:4px; cursor:pointer;" title="Copy">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <p style="margin-top: 10px; font-size: 0.9rem; color: #94a3b8;">Share these credentials with the vendor.</p>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Done',
            confirmButtonColor: '#16a34a',
            allowOutsideClick: false
        });

        function copyPassword() {
            var copyText = document.getElementById("genPass");
            copyText.select();
            copyText.setSelectionRange(0, 99999); 
            navigator.clipboard.writeText(copyText.value).then(() => {
                const btn = document.querySelector('button[title="Copy"]');
                btn.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(() => btn.innerHTML = '<i class="fas fa-copy"></i>', 2000);
            });
        }
    <?php else: ?>
        // Standard Toast for Rejections/Other messages
        Swal.fire({
            icon: '<?php echo isset($_GET['icon']) ? $_GET['icon'] : 'success'; ?>',
            title: '<?php echo ucfirst(isset($_GET['icon']) ? $_GET['icon'] : 'success'); ?>',
            text: '<?php echo htmlspecialchars($_GET['msg']); ?>',
            timer: 3000,
            showConfirmButton: false
        });
    <?php endif; ?>
</script>
<?php endif; ?>

<?php include '../includes/main_footer.php'; ?>
