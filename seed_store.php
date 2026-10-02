<?php
// seed_store.php
include 'db_connect.php';
session_start();

$page = 'seed_store';
$page_title = 'Quality Seeds Marketplace';

// --- Filter & Sort Logic ---
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : 'All';
$type = isset($_GET['type']) ? $conn->real_escape_string($_GET['type']) : '';
$sort = isset($_GET['sort']) ? $conn->real_escape_string($_GET['sort']) : 'newest';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 10000;

// Build Query
$sql = "SELECT s.*, u.fullname as seller_name, u.role
        FROM seeds s 
        JOIN users u ON s.seller_id = u.id 
        WHERE s.status = 'Active' AND u.status = 'Active'";


if (!empty($search)) {
    $sql .= " AND (s.name LIKE '%$search%' OR s.description LIKE '%$search%' OR s.crop_type LIKE '%$search%')";
}
if ($category !== 'All' && !empty($category)) {
    $sql .= " AND s.category = '$category'";
}
if (!empty($type)) {
    $sql .= " AND s.crop_type LIKE '%$type%'";
}

if ($min_price > 0) {
    $sql .= " AND s.price >= $min_price";
}
if ($max_price < 10000) {
    $sql .= " AND s.price <= $max_price";
}

// Sorting
switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY s.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY s.price DESC";
        break;
    default: // newest
        $sql .= " ORDER BY s.created_at DESC";
        break;
}

$res_seeds = $conn->query($sql);

// Fetch Categories for Filter (Dynamic)
$cat_query = "SELECT DISTINCT category FROM seeds WHERE status = 'Active' ORDER BY category";
$res_cats = $conn->query($cat_query);
$categories = [];
while($row = $res_cats->fetch_assoc()) {
    $categories[] = $row['category'];
}

// Store page-specific CSS
$extra_css = "
<style>
    :root {
        --shop-bg: #f9fbfc;
        --sidebar-width: 280px;
        --primary-green: #059669;
        --hover-green: #047857;
    }

    body {
        background-color: var(--shop-bg);
    }

    .shop-layout {
        display: grid;
        grid-template-columns: var(--sidebar-width) 1fr;
        gap: 2rem;
        padding-top: 2rem;
        padding-bottom: 4rem;
        align-items: start;
        max-width: 96% !important; /* Make it nearly full-screen */
        margin: 0 auto;
    }

    .shop-sidebar {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        position: sticky;
        top: 100px;
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }

    .filter-section {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
    }
    .filter-section:last-child { border-bottom: none; margin-bottom: 0; }

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
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        border-radius: 8px;
        color: #64748b;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
        margin-bottom: 2px;
    }

    .filter-link:hover {
        background: #ecfdf5;
        color: var(--primary-green);
    }
    
    .filter-link.active {
        background: #d1fae5;
        color: #064e3b;
        font-weight: 600;
    }

    .form-input-sm {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.2s;
    }

    .form-input-sm:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .price-range-inputs {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

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
        cursor: pointer;
    }

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

    .product-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        cursor: pointer;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.15);
        border-color: #34d399;
    }

    .card-img-wrapper {
        position: relative;
        padding-top: 75%;
        overflow: hidden;
        background: white;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 200px;
    }

    .card-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        backface-visibility: hidden;
    }

    /* No zoom on hover as per user preference */

    .card-body {
        padding: 1.25rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        border-top: 1px solid #f3f4f6;
        background: #fff;
    }

    .product-cat {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #65a30d;
        font-weight: 600;
        margin-bottom: 0.25rem;
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
        font-weight: 700;
        color: #059669;
    }

    .btn-add {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background: #f0fdf4;
        color: #16a34a;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-add:hover {
        background: #16a34a;
        color: white;
        transform: rotate(90deg);
    }

    .btn-full {
        padding: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-primary { background: #10b981; color: white; border: none; }
    .btn-primary:hover { background: #059669; }
    
    .btn-outline { background: white; border: 1px solid #d1d5db; color: #374151; }
    .btn-outline:hover { background: #f3f4f6; }

    .sidebar-overlay { 
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(0,0,0,0.5); z-index: 2500; 
    }

    .mobile-filter-btn {
        display: none;
    }

    @media (max-width: 992px) {
        .shop-layout { display: block; padding: 1rem; }
        .shop-sidebar { 
            display: none; position: fixed; top: 0; left: 0; height: 100vh; width: 80%; max-width: 300px; 
            z-index: 3000; box-shadow: 4px 0 15px rgba(0,0,0,0.1); overflow-y: auto; 
            animation: slideIn 0.3s ease;
        }
        .sidebar-overlay.active { display: block; }
        .shop-sidebar.active { display: block; }
        .mobile-filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
        }
    }
    @keyframes slideIn { from { transform: translateX(-100%); } to { transform: translateX(0); } }
</style>
";

include 'includes/main_header.php';
?>

<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="container shop-layout">
    
    <!-- Sidebar -->
    <aside class="shop-sidebar" id="filterSidebar">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;" class="d-lg-none">
            <h3 style="margin: 0;">Filters</h3>
            <button onclick="toggleSidebar()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>

        <form action="" method="GET">
            <?php if(!empty($sort)) echo "<input type='hidden' name='sort' value='$sort'>"; ?>

            <!-- Search -->
            <div class="filter-section">
                <h4 class="filter-title">Search</h4>
                <input type="text" name="search" class="form-input-sm" placeholder="Seed name, type..." value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <!-- Categories -->
            <div class="filter-section">
                <h4 class="filter-title">Categories</h4>
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <a href="seed_store.php?category=All&search=<?php echo $search; ?>&type=<?php echo $type; ?>" 
                       class="filter-link <?php echo ($category === 'All') ? 'active' : ''; ?>">
                        All Categories <?php if($category === 'All') echo '<i class="fas fa-check"></i>'; ?>
                    </a>
                    <?php foreach($categories as $cat): 
                        $isActive = ($category === $cat) ? 'active' : '';
                        $checkIcon = ($category === $cat) ? '<i class="fas fa-check"></i>' : '';
                    ?>
                        <a href="seed_store.php?category=<?php echo urlencode($cat); ?>&search=<?php echo $search; ?>&type=<?php echo $type; ?>" 
                           class="filter-link <?php echo $isActive; ?>">
                            <?php echo $cat; ?> <?php echo $checkIcon; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
            </div>

            <!-- Crop Type (Text for now) -->
            <div class="filter-section">
                <h4 class="filter-title">Crop Type</h4>
                <input type="text" name="type" class="form-input-sm" placeholder="e.g. Wheat" value="<?php echo htmlspecialchars($type); ?>">
            </div>

            <!-- Price -->
            <div class="filter-section">
                <h4 class="filter-title">Price Range</h4>
                <div class="price-range-inputs">
                    <input type="number" name="min_price" class="form-input-sm" placeholder="Min" value="<?php echo $min_price; ?>">
                    <span>-</span>
                    <input type="number" name="max_price" class="form-input-sm" placeholder="Max" value="<?php echo $max_price; ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full" style="width: 100%; margin-top: 1rem; border-radius: 8px;">Apply Filters</button>
            <?php if (!empty($search) || $category !== 'All' || !empty($type)): ?>
                <a href="seed_store.php" class="btn btn-outline btn-full" style="display: block; width: 100%; margin-top: 0.5rem; text-align: center; border-radius: 8px;">Clear All</a>
            <?php endif; ?>
        </form>
    </aside>

    <!-- Main Content -->
    <main>
        <!-- Header -->
        <div class="shop-header">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="mobile-filter-btn" onclick="toggleSidebar()">
                    <i class="fas fa-filter"></i> Filters
                </button>
                <div class="d-none d-lg-block"></div> <!-- Spacer -->
                <span style="font-weight: 600; color: #4b5563;">
                    <?php echo $res_seeds->num_rows; ?> Seeds Found
                </span>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <label style="font-size: 0.9rem; color: #6b7280; display: none; @media(min-width: 768px){display: inline;}">Sort:</label>
                <select class="sort-select" onchange="updateSort(this.value)">
                    <option value="newest" <?php echo ($sort == 'newest') ? 'selected' : ''; ?>>Newest</option>
                    <option value="price_low" <?php echo ($sort == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo ($sort == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                </select>
            </div>
        </div>

        <!-- Grid -->
        <div class="products-grid">
            <?php if ($res_seeds && $res_seeds->num_rows > 0): ?>
                <?php while ($row = $res_seeds->fetch_assoc()): 
                    $img = !empty($row['image_path']) ? $row['image_path'] : 'images/default_seed.png';
                    // Fix path if needed
                    if(strpos($img, 'images/') === 0) $img = $img;

                    $seller = ($row['role'] === 'admin') ? 'AgriAI Verified' : $row['seller_name'];
                    $store = !empty($row['brand']) ? $row['brand'] : 'Independent Seller'; // fallback
                ?>
                <div class="product-card">
                    <a href="seed_details.php?id=<?php echo $row['id']; ?>" class="card-img-wrapper">
                         <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="card-img">
                    </a>
                    
                    <div class="card-body">
                         <div class="product-cat"><?php echo htmlspecialchars($row['category']); ?></div>
                         <h3 class="product-title">
                            <a href="seed_details.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </a>
                         </h3>
                         
                         <div class="seller-info">
                            <i class="fas fa-seedling" style="font-size: 0.8rem;"></i>
                            <span><?php echo htmlspecialchars($seller); ?></span>
                         </div>
                         
                         <!-- Details -->
                         <div style="margin-bottom: 1rem; display: flex; gap: 5px; flex-wrap: wrap;">
                             <span style="font-size: 0.7rem; background: #ecfdf5; color: #059669; padding: 2px 8px; border-radius: 4px;">
                                <?php echo htmlspecialchars($row['crop_type']); ?>
                             </span>
                             <span style="font-size: 0.7rem; background: #f3f4f6; color: #4b5563; padding: 2px 8px; border-radius: 4px;">
                                <?php echo htmlspecialchars($row['weight']); ?>
                             </span>
                         </div>

                         <div class="card-footer">
                             <div>
                                 <span class="price-tag">₹<?php echo number_format($row['price']); ?></span>
                             </div>

                             <div style="display: flex; gap: 0.5rem;">
                                <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                                 <form action="add_to_cart.php" method="POST" style="display:inline;">
                                     <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                     <input type="hidden" name="qty" value="1">
                                     <input type="hidden" name="type" value="seed">
                                     <input type="hidden" name="redirect" value="seed_store">
                                     <button type="submit" class="btn-add" title="Add to Cart">
                                         <i class="fas fa-shopping-basket"></i>
                                     </button>
                                 </form>
                                 <a href="checkout.php?product_id=<?php echo $row['id']; ?>&qty=1&type=seed" class="btn-add" style="background:#0284c7; color:white;" title="Buy Now">
                                     <i class="fas fa-bolt"></i>
                                 </a>
                                <?php endif; ?>
                             </div>
                         </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem;">
                     <h3>No Seeds Found</h3>
                     <p>Try adjusting your search criteria.</p>
                     <a href="seed_store.php" class="btn btn-primary">Reset Filters</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('filterSidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }
    
    function updateSort(val) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', val);
        window.location.href = url.toString();
    }
</script>

<?php include 'includes/main_footer.php'; ?>
</html>
