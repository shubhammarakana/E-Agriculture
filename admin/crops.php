<?php
// admin/crops.php
include '../db_connect.php';
session_start();

// Admin Security
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$page = 'crops';
$page_title = 'Crop Management';

// Handle AJAX Status Update (Master Crops Only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    $id = intval($_POST['id']);
    $status = $_POST['status']; 
    
    $stmt = $conn->prepare("UPDATE master_crops SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit;
}

// Handle Delete (Generic for both tables)
if (isset($_GET['delete']) && isset($_GET['type'])) {
    $id = intval($_GET['delete']);
    $type = $_GET['type'];
    
    if($type == 'master') {
        $conn->query("DELETE FROM master_crops WHERE id = $id");
    } elseif ($type == 'farmer') {
         // Optionally delete image file here
        $conn->query("DELETE FROM crops WHERE id = $id");
    }
    
    header("Location: crops.php?msg=deleted&tab=" . ($type=='master'?'master':'farmer'));
    exit();
}

include '../includes/main_header.php';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'master';
?>

<style>
    /* Styling */
    body { background-color: #f8fafc; }
    
    .page-header {
        background: white;
        padding: 2rem 2rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }
    
    .header-actions { display: flex; gap: 10px; }
    
    .btn-primary-custom {
        background: #059669;
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: 0.2s;
    }
    .btn-primary-custom:hover { background: #047857; color: white; }
    
    /* Tabs */
    .custom-tabs .nav-link {
        color: #64748b;
        font-weight: 600;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 1rem 1.5rem;
        transition: all 0.2s;
    }
    .custom-tabs .nav-link:hover { color: #059669; }
    .custom-tabs .nav-link.active {
        color: #059669;
        background: transparent;
        border-bottom: 2px solid #059669;
    }

    /* Table */
    .card-table {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .table-controls { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; gap: 1rem; flex-wrap: wrap; }
    
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: #f8fafc; color: #475569; padding: 1rem 1.5rem; text-align: left; font-size: 0.85rem; text-transform: uppercase; font-weight: 600; }
    .data-table td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
    
    .crop-img-thumb { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; margin-right: 12px; }
    
    .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .status-active { background: #dcfce7; color: #166534; }
    .status-inactive { background: #f1f5f9; color: #64748b; }

    .action-btn { width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; margin-right: 4px; border: none; cursor: pointer; text-decoration: none; }
    .action-edit { background: #f0fdf4; color: #16a34a; }
    .action-delete { background: #fef2f2; color: #dc2626; }
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold m-0" style="color: #1e293b;">Crop Management</h1>
            <p class="text-muted small mb-0">Manage master catalog and farmer listings.</p>
        </div>
        <div class="header-actions">
            <a href="add_master_crop.php" class="btn-primary-custom">
                <i class="fas fa-plus"></i> Add Master Crop
            </a>
        </div>
    </div>
    
    <!-- Tabs -->
    <ul class="nav nav-tabs custom-tabs mt-4" id="cropTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab == 'master' ? 'active' : ''; ?>" id="master-tab" data-bs-toggle="tab" data-bs-target="#master" type="button" role="tab">
                <i class="fas fa-database me-2"></i> Master Catalog
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab == 'farmer' ? 'active' : ''; ?>" id="farmer-tab" data-bs-toggle="tab" data-bs-target="#farmer" type="button" role="tab">
                <i class="fas fa-tractor me-2"></i> Farmer Listings
            </button>
        </li>
    </ul>
</div>

<div class="container-fluid px-4 pb-5">
    <div class="tab-content" id="myTabContent">
        
        <!-- MASTER CROPS TAB -->
        <div class="tab-pane fade <?php echo $active_tab == 'master' ? 'show active' : ''; ?>" id="master" role="tabpanel">
            <div class="card-table">
                <div class="table-controls">
                    <input type="text" id="searchMaster" class="form-control" style="width: 300px;" placeholder="Search Master Crops...">
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Season</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="masterTableBody">
                            <?php
                            $res = $conn->query("SELECT * FROM master_crops ORDER BY id DESC");
                            if ($res->num_rows > 0) {
                                while($row = $res->fetch_assoc()) {
                                    $img = !empty($row['image']) ? $row['image'] : 'images/default-crop.png';
                                    if (strpos($img, 'images/') !== 0 && strpos($img, 'http') !== 0) $img = '../'.$img;
                                    if (strpos($img, 'images/') === 0) $img = '../'.$img;
                                    $checked = $row['status'] == 'Active' ? 'checked' : '';
                                    echo "<tr class='master-row' data-search='{$row['name']}'>
                                        <td>#{$row['id']}</td>
                                        <td>
                                            <div class='d-flex align-items-center'>
                                                <img src='$img' class='crop-img-thumb'>
                                                <div>
                                                    <div class='fw-bold'>{$row['name']}</div>
                                                    <div class='small text-muted'>{$row['local_name']}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{$row['category']}</td>
                                        <td>{$row['season']}</td>
                                        <td>
                                            <div class='form-check form-switch'>
                                                <input class='form-check-input' type='checkbox' onchange='toggleStatus({$row['id']}, this)' $checked>
                                            </div>
                                        </td>
                                        <td class='text-end'>
                                            <a href='edit_master_crop.php?id={$row['id']}' class='action-btn action-edit'><i class='fas fa-pen'></i></a>
                                            <a href='?delete={$row['id']}&type=master' class='action-btn action-delete' onclick='return confirm(\"Delete master crop?\")'><i class='fas fa-trash'></i></a>
                                        </td>
                                    </tr>";
                                }
                            } else { echo "<tr><td colspan='6' class='text-center py-4'>No master crops found.</td></tr>"; }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- FARMER LISTINGS TAB -->
        <div class="tab-pane fade <?php echo $active_tab == 'farmer' ? 'show active' : ''; ?>" id="farmer" role="tabpanel">
            <div class="card-table">
                <div class="table-controls">
                    <input type="text" id="searchFarmer" class="form-control" style="width: 300px;" placeholder="Search Listings or Farmers...">
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Listing ID</th>
                                <th>Farmer</th>
                                <th>Crop Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="farmerTableBody">
                            <?php
                            // Fetch Farmer Listings Join Users
                            $sql = "SELECT c.*, u.fullname as farmer_name, u.location 
                                    FROM crops c 
                                    LEFT JOIN users u ON c.farmer_id = u.id 
                                    ORDER BY c.created_at DESC";
                            $res = $conn->query($sql);
                            
                            if ($res && $res->num_rows > 0) {
                                while($row = $res->fetch_assoc()) {
                                    $img = !empty($row['image']) ? $row['image'] : 'images/default-crop.png';
                                    if (strpos($img, 'images/') !== 0 && strpos($img, 'http') !== 0) $img = '../'.$img;
                                    if (strpos($img, 'images/') === 0) $img = '../'.$img;
                                    
                                    echo "<tr class='farmer-row' data-search='{$row['name']} {$row['farmer_name']}'>
                                        <td>#{$row['id']}</td>
                                        <td>
                                            <div class='fw-bold'>{$row['farmer_name']}</div>
                                            <div class='small text-muted'><i class='fas fa-map-marker-alt'></i> {$row['location']}</div>
                                        </td>
                                        <td>
                                            <div class='d-flex align-items-center'>
                                                <img src='$img' class='crop-img-thumb'>
                                                <span class='fw-bold'>{$row['name']}</span>
                                            </div>
                                        </td>
                                        <td class='fw-bold text-success'>₹{$row['price']} / {$row['price_unit']}</td>
                                        <td>
                                            <span class='badge bg-light text-dark border'>{$row['quantity']} {$row['quantity_unit']}</span>
                                        </td>
                                        <td class='text-end'>
                                            <a href='?delete={$row['id']}&type=farmer' class='action-btn action-delete' onclick='return confirm(\"Remove this listing?\")'><i class='fas fa-trash'></i></a>
                                        </td>
                                    </tr>";
                                }
                            } else { echo "<tr><td colspan='6' class='text-center py-4'>No farmer listings found.</td></tr>"; }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Search Filters
    document.getElementById('searchMaster').addEventListener('keyup', function() {
        let term = this.value.toLowerCase();
        document.querySelectorAll('.master-row').forEach(row => {
            row.style.display = row.dataset.search.toLowerCase().includes(term) ? '' : 'none';
        });
    });

    document.getElementById('searchFarmer').addEventListener('keyup', function() {
        let term = this.value.toLowerCase();
        document.querySelectorAll('.farmer-row').forEach(row => {
            row.style.display = row.dataset.search.toLowerCase().includes(term) ? '' : 'none';
        });
    });

    // Toggle Status (Master Only)
    function toggleStatus(id, checkbox) {
        const newStatus = checkbox.checked ? 'Active' : 'Inactive';
        const formData = new FormData();
        formData.append('action', 'toggle_status');
        formData.append('id', id);
        formData.append('status', newStatus);

        fetch('crops.php', { method: 'POST', body: formData })
        .catch(err => {
            alert('Error updating status');
            checkbox.checked = !checkbox.checked;
        });
    }
</script>

<?php include '../includes/main_footer.php'; ?>
