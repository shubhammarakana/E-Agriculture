<?php
// admin/users.php
include '../db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$page = 'users';
$page_title = 'User Management';

// Handle Actions (Block/Unblock/Verify)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $uid = $conn->real_escape_string($_GET['id']);
    $action = $_GET['action'];
    
    if ($action == 'block') $sql = "UPDATE users SET status='Blocked' WHERE id='$uid'";
    elseif ($action == 'activate') $sql = "UPDATE users SET status='Active' WHERE id='$uid'";
    elseif ($action == 'verify') $sql = "UPDATE users SET verification_status='Verified' WHERE id='$uid'";
    elseif ($action == 'delete') $sql = "DELETE FROM users WHERE id='$uid'";
    
    if (isset($sql) && $conn->query($sql)) {
        header("Location: users.php?msg=updated");
        exit();
    }
}

// Fetch Users
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

// Stats for Header
$total_users = $result->num_rows;
$farmers = 0; $buyers = 0; $vendors = 0; $blocked = 0; $pending_vendors = 0;
// Reset pointer
$result->data_seek(0); 
while($r = $result->fetch_assoc()) {
    if($r['role']=='farmer' && $r['status']=='Active') $farmers++;
    if($r['role']=='buyer' && $r['status']=='Active') $buyers++;
    if($r['role']=='vendor' && $r['status']=='Active') $vendors++;
    if($r['status']=='Blocked') $blocked++;
    if($r['role']=='vendor' && $r['status']=='Pending') $pending_vendors++;
}
$result->data_seek(0);

include '../includes/main_header.php';
?>

<div class="container" style="padding: 2rem 0; min-height: 80vh;">
    <h1 style="margin-bottom: 1.5rem;">User Management</h1>

    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin-bottom: 2rem;">
        <div class="glass-panel" style="padding: 1.5rem; text-align: center;">
            <p style="color: #6b7280; font-size: 0.9rem;">Total Users</p>
            <h3 style="font-size: 1.8rem; margin: 0; color: #1f2937;"><?php echo $total_users; ?></h3>
        </div>
        <div class="glass-panel" style="padding: 1.5rem; text-align: center;">
            <p style="color: #6b7280; font-size: 0.9rem;">Active Farmers</p>
            <h3 style="font-size: 1.8rem; margin: 0; color: #16a34a;"><?php echo $farmers; ?></h3>
        </div>
        <div class="glass-panel" style="padding: 1.5rem; text-align: center;">
            <p style="color: #6b7280; font-size: 0.9rem;">Active Buyers</p>
            <h3 style="font-size: 1.8rem; margin: 0; color: #2563eb;"><?php echo $buyers; ?></h3>
        </div>
        <div class="glass-panel" style="padding: 1.5rem; text-align: center;">
            <p style="color: #6b7280; font-size: 0.9rem;">Pending Vendors</p>
             <h3 style="font-size: 1.8rem; margin: 0; color: #d97706;"><?php echo $pending_vendors; ?></h3>
        </div>
        <div class="glass-panel" style="padding: 1.5rem; text-align: center;">
            <p style="color: #6b7280; font-size: 0.9rem;">Blocked</p>
            <h3 style="font-size: 1.8rem; margin: 0; color: #dc2626;"><?php echo $blocked; ?></h3>
        </div>
    </div>

    <div class="glass-panel">
        <!-- Filter Bar -->
        <div style="display: flex; gap: 15px; margin-bottom: 1.5rem; align-items: center;">
            <input type="text" id="userSearch" placeholder="Search by name or email..." class="form-control-glass" style="max-width: 300px;" onkeyup="filterUsers()">
            <select id="roleFilter" class="form-control-glass" style="max-width: 200px;" onchange="filterUsers()">
                <option value="all">All Roles</option>
                <option value="farmer">Farmer</option>
                <option value="buyer">Buyer</option>
                <option value="vendor">Vendor</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div style="overflow-x: auto;">
            <table class="glass-table" id="userTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Verified</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="user-row" data-role="<?php echo $row['role']; ?>" data-name="<?php echo strtolower($row['fullname'] . ' ' . $row['email']); ?>">
                            <td>#<?php echo $row['id']; ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 35px; height: 35px; background: #e5e7eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                        <?php if($row['profile_image']): ?>
                                            <img src="../<?php echo $row['profile_image']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <i class="fas fa-user" style="color: #9ca3af;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600;"><?php echo htmlspecialchars($row['fullname']); ?></div>
                                        <div style="font-size: 0.8rem; color: #6b7280;"><?php echo htmlspecialchars($row['email']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge" style="background: #f3f4f6; color: #374151;">
                                    <?php echo ucfirst($row['role']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?php echo ($row['status'] == 'Active') ? 'status-active' : (($row['status'] == 'Pending') ? 'status-pending' : 'status-rejected'); ?>" 
                                      style="<?php echo ($row['status'] == 'Pending') ? 'background: #fef3c7; color: #d97706;' : ''; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if($row['verification_status'] == 'Verified'): ?>
                                    <span style="color: #16a34a;"><i class="fas fa-check-circle"></i> Yes</span>
                                <?php else: ?>
                                    <span style="color: #9ca3af;"><i class="fas fa-times-circle"></i> No</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <?php if($row['status'] == 'Active'): ?>
                                        <a href="users.php?action=block&id=<?php echo $row['id']; ?>" class="btn btn-outline" style="color: #dc2626; border-color: #dc2626; padding: 4px 8px;" title="Block">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="users.php?action=activate&id=<?php echo $row['id']; ?>" class="btn btn-outline" style="color: #16a34a; border-color: #16a34a; padding: 4px 8px;" title="Activate">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if($row['verification_status'] != 'Verified'): ?>
                                        <a href="users.php?action=verify&id=<?php echo $row['id']; ?>" class="btn btn-outline" style="color: #2563eb; border-color: #2563eb; padding: 4px 8px;" title="Verify">
                                            <i class="fas fa-certificate"></i>
                                        </a>
                                    <?php endif; ?>

                                    <a href="users.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-outline" style="color: red; border-color: red; padding: 4px 8px;" onclick="return confirm('Permanently delete user?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterUsers() {
    let input = document.getElementById("userSearch").value.toLowerCase();
    let role = document.getElementById("roleFilter").value;
    let rows = document.querySelectorAll(".user-row");

    rows.forEach(row => {
        let name = row.getAttribute("data-name");
        let userRole = row.getAttribute("data-role");
        
        let matchesSearch = name.includes(input);
        let matchesRole = (role === "all" || userRole === role);

        if (matchesSearch && matchesRole) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>

<?php include '../includes/main_footer.php'; ?>
