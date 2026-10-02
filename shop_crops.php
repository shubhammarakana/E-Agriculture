<?php
// shop_crops.php - PUBLIC
include 'db_connect.php';
session_start();

$page = 'shop'; // Active nav item
$page_title = 'Marketplace - Buy Fresh Crops';

// --- Filter & Sort Logic ---
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$location = isset($_GET['location']) ? $conn->real_escape_string($_GET['location']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : 'All';
$sort = isset($_GET['sort']) ? $conn->real_escape_string($_GET['sort']) : 'newest';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 10000;

// Build Query
$sql = "SELECT c.*, u.fullname as farmer_name, u.role, u.state, u.district, fp.verification_status 
        FROM crops c 
        JOIN users u ON c.farmer_id = u.id 
        LEFT JOIN farmer_profiles fp ON u.id = fp.user_id
        WHERE u.status = 'Active'";


if (!empty($search)) {
    $sql .= " AND (c.name LIKE '%$search%' OR c.description LIKE '%$search%')";
}
if (!empty($location)) {
    $sql .= " AND (u.state LIKE '%$location%' OR u.district LIKE '%$location%' OR u.location LIKE '%$location%')";
}
if ($category !== 'All' && !empty($category)) {
    $sql .= " AND c.category = '$category'";
}

if ($min_price > 0) {
    $sql .= " AND c.price >= $min_price";
}
if ($max_price < 10000) {
    $sql .= " AND c.price <= $max_price";
}

// Sorting
switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY c.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY c.price DESC";
        break;
    case 'oldest':
        $sql .= " ORDER BY c.created_at ASC";
        break;
    default: // newest
        $sql .= " ORDER BY c.created_at DESC";
        break;
}

$result = $conn->query($sql);

// Store page-specific CSS
$extra_css = "
<style>
    :root {
        --shop-bg: #f9fbfc;
        --sidebar-width: 280px;
        --primary-green: #16a34a;
        --hover-green: #15803d;
    }

    body {
        background-color: var(--shop-bg);
    }

    /* Layout Grid */
    .shop-layout {
        display: grid;
        grid-template-columns: var(--sidebar-width) 1fr;
        gap: 2rem;
        padding-top: 2rem;
        padding-bottom: 4rem;
        align-items: start; /* Important for sticky sidebar */
        max-width: 96% !important; /* Make it nearly full-screen */
        margin: 0 auto;
    }

    /* Sidebar Styles */
    .shop-sidebar {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        position: sticky;
        top: 90px; /* Adjust based on header height */
        height: fit-content;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .filter-section {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .filter-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .filter-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filter-link {
        display: block;
        padding: 6px 0;
        color: #4b5563;
        font-size: 0.9rem;
        transition: all 0.2s;
        text-decoration: none;
        display: flex;
        justify-content: space-between;
    }

    .filter-link:hover, .filter-link.active {
        color: var(--primary-green);
        font-weight: 500;
    }
    
    .filter-link.active i {
        opacity: 1;
    }

    /* Custom Input Styles */
    .form-input-sm {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.2s;
    }

    .form-input-sm:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
    }

    .price-range-inputs {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    /* Main Content Area */
    .shop-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }

    .sort-select {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0.5rem 2rem 0.5rem 0.75rem;
        font-size: 0.9rem;
        color: #374151;
        background: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e\") no-repeat right 0.5rem center/1.5em 1.5em;
        appearance: none;
        cursor: pointer;
    }

    /* Product Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.5rem;
    }

    @media (max-width: 1400px) {
        .products-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    
    @media (max-width: 1200px) {
        .products-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 480px) {
        .products-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Product Card Redesign */
    .product-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border-color: #d1d5db;
    }

    .card-img-wrapper {
        position: relative;
        padding-top: 75%; /* 4:3 Aspect Ratio */
        overflow: hidden;
        background: #f3f4f6;
    }

    .card-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .card-img {
        transform: scale(1.08);
    }

    .card-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--primary-green);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .card-badge.out {
        color: #ef4444;
        background: #fef2f2;
    }

    .card-body {
        padding: 1.25rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-cat {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 0.25rem;
        letter-spacing: 0.5px;
    }

    .product-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .seller-info {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }

    .verified-badge {
        color: #2563eb;
        font-size: 0.8rem;
    }

    .card-footer {
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .price-tag {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--primary-green);
    }

    .price-unit {
        font-size: 0.8rem;
        font-weight: 500;
        color: #9ca3af;
    }

    .btn-add {
        background: #f3f4f6;
        color: #374151;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-add:hover {
        background: var(--primary-green);
        color: white;
    }

    /* Mobile Responsive */
    .mobile-filter-btn {
        display: none;
    }

    @media (max-width: 992px) {
        .shop-layout {
            grid-template-columns: 1fr; /* Full width */
        }
        
        .shop-sidebar {
            display: none; /* Hidden by default on mobile */
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 80%;
            max-width: 320px;
            z-index: 3000;
            border-radius: 0;
            max-height: 100vh;
        }
        
        .shop-sidebar.active {
            display: block;
            animation: slideIn 0.3s forwards;
        }

        .mobile-filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
        }
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 2999;
            backdrop-filter: blur(2px);
        }
        
        .sidebar-overlay.active {
            display: block;
        }
    }
    
    @keyframes slideIn {
        from { transform: translateX(-100%); }
        to { transform: translateX(0); }
    }
</style>
";

include 'includes/main_header.php';
?>

<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="container shop-layout">
    
    <!-- Sidebar -->
    <aside class="shop-sidebar" id="filterSidebar">
        
        <!-- Mobile Close Button -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;" class="d-lg-none">
            <h3 style="margin: 0;">Filters</h3>
            <button onclick="toggleSidebar()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>

        <form action="" method="GET" id="filterForm">
            <!-- Retain current sort -->
            <?php if(!empty($sort)) echo "<input type='hidden' name='sort' value='$sort'>"; ?>

            <!-- Search -->
            <div class="filter-section">
                <h4 class="filter-title">Search</h4>
                <div class="input-wrapper">
                    <i class="fas fa-search" style="font-size: 0.8rem; left: 0.8rem;"></i>
                    <input type="text" name="search" class="form-input-sm" style="padding-left: 2.2rem;" placeholder="Search crops..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>

            <!-- Categories -->
            <div class="filter-section">
                <h4 class="filter-title">Categories</h4>
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <?php
                    $cats = [
                        'All' => 'All Categories',
                        'Vegetables' => 'Vegetables', 
                        'Fruits' => 'Fruits', 
                        'Grains' => 'Grains', 
                        'Pulses' => 'Pulses', 
                        'Oilseeds' => 'Oilseeds', 
                        'Other' => 'Others'
                    ];
                    foreach ($cats as $val => $label) {
                        $isActive = ($category === $val) ? 'active' : '';
                        $checkIcon = ($category === $val) ? '<i class="fas fa-check"></i>' : '';
                        
                        // Handle 'All' logic for the link
                        $catParam = ($val === 'All') ? 'All' : $val;
                        
                        echo "<a href='shop_crops.php?category=$catParam&search=$search&location=$location' class='filter-link $isActive'>
                                $label $checkIcon
                              </a>";
                    }
                    ?>
                </div>
                <!-- Hidden input for form submission if they use other inputs -->
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
            </div>

            <!-- Price Range -->
            <div class="filter-section">
                <h4 class="filter-title">Price Range</h4>
                <div class="price-range-inputs">
                    <input type="number" name="min_price" class="form-input-sm" placeholder="Min" value="<?php echo $min_price; ?>">
                    <span style="color: #9ca3af;">-</span>
                    <input type="number" name="max_price" class="form-input-sm" placeholder="Max" value="<?php echo $max_price; ?>">
                </div>
            </div>

            <!-- Location -->
            <div class="filter-section">
                <h4 class="filter-title">Location</h4>
                <div class="input-wrapper">
                    <i class="fas fa-map-marker-alt" style="font-size: 0.8rem; left: 0.8rem;"></i>
                    <input type="text" name="location" class="form-input-sm" style="padding-left: 2.2rem;" placeholder="City or District" value="<?php echo htmlspecialchars($location); ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full" style="margin-top: 1rem; border-radius: 8px;">Apply Filters</button>
            <?php if (!empty($search) || !empty($location) || $category !== 'All' || $min_price > 0 || $max_price < 10000): ?>
                <a href="shop_crops.php" class="btn btn-outline btn-full" style="margin-top: 0.5rem; border-radius: 8px; text-align: center;">Clear All</a>
            <?php endif; ?>
        </form>
    </aside>

    <!-- Main Content -->
    <main>
        <!-- Header & Sorting -->
        <div class="shop-header">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="mobile-filter-btn" onclick="toggleSidebar()">
                    <i class="fas fa-filter"></i> Filters
                </button>
                <span style="font-weight: 600; color: #4b5563;">
                    <?php echo $result->num_rows; ?> Results Found
                </span>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <label for="sort" style="font-size: 0.9rem; color: #6b7280; display: none; @media(min-width: 768px){display: inline;}">Sort by:</label>
                <select id="sort" class="sort-select" onchange="updateSort(this.value)">
                    <option value="newest" <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                    <option value="oldest" <?php echo ($sort == 'oldest') ? 'selected' : ''; ?>>Oldest First</option>
                    <option value="price_low" <?php echo ($sort == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo ($sort == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                </select>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="products-grid">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $img_path = !empty($row["image_path"]) ? $row["image_path"] : 'images/default-crop.png';
                    // Path fix
                    if (strpos($img_path, 'images/') === 0) {
                        $img_path = $img_path; 
                    }
                    
                    $is_admin = ($row['role'] === 'admin');
                    $seller_name = $is_admin ? "AgriAI Verified" : $row['farmer_name'];
                    $stock_class = ($row['stock_status'] == 'Out of Stock') ? 'out' : 'in';
                    ?>
                    
                    <div class="product-card">
                        <a href="crop_details.php?id=<?php echo $row['id']; ?>" class="card-img-wrapper">
                            <img src="<?php echo $img_path; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="card-img">
                            <span class="card-badge <?php echo $stock_class; ?>">
                                <?php echo $row['stock_status']; ?>
                            </span>
                        </a>
                        
                        <div class="card-body">
                            <div class="product-cat"><?php echo htmlspecialchars($row['category']); ?></div>
                            <h3 class="product-title">
                                <a href="crop_details.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </a>
                            </h3>
                            
                            <div class="seller-info">
                                <i class="fas fa-store-alt" style="font-size: 0.8rem;"></i> 
                                <span><?php echo htmlspecialchars($seller_name); ?></span>
                                <?php if ($is_admin || (!empty($row['verification_status']) && $row['verification_status'] == 'verified')): ?>
                                    <i class="fas fa-check-circle verified-badge" title="Verified Seller"></i>
                                <?php endif; ?>
                            </div>

                            <div class="card-footer">
                                <div>
                                    <span class="price-tag">₹<?php echo number_format($row['price']); ?></span>
                                    <span class="price-unit">/<?php echo $row['price_unit']; ?></span>
                                </div>
                                
                                <div style="display: flex; gap: 0.5rem;">
                                    <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                                        <form action="add_to_cart.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="qty" value="1">
                                            <button type="submit" class="btn-add" title="Add to Cart">
                                                <i class="fas fa-shopping-basket"></i>
                                            </button>
                                        </form>
                                        <a href="checkout.php?product_id=<?php echo $row['id']; ?>&qty=1&type=crop" class="btn-add" style="background:#0284c7; color:white;" title="Buy Now">
                                            <i class="fas fa-bolt"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                }
            } else {
                ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem;">
                    <div style="background: #f3f4f6; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                        <i class="fas fa-search" style="font-size: 2rem; color: #9ca3af;"></i>
                    </div>
                    <h3 style="color: #374151; margin-bottom: 0.5rem;">No crops found</h3>
                    <p style="color: #6b7280; margin-bottom: 1.5rem;">We couldn't find what you're looking for.</p>
                    <a href="shop_crops.php" class="btn btn-primary">Clear All Filters</a>
                </div>
                <?php
            }
            ?>
        </div>
        
    </main>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('filterSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }

    function updateSort(value) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('sort', value);
        window.location.search = urlParams.toString();
    }
</script>

<?php include 'includes/main_footer.php'; ?>