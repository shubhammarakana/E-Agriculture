<?php
// chemical_store.php
include 'db_connect.php';
session_start();

$page = 'chemical_store';
$page_title = 'AI Chemical Marketplace';

// --- Filter & Sort Logic ---
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : 'All';
$safety = isset($_GET['safety']) ? $conn->real_escape_string($_GET['safety']) : 'All';
$crop = isset($_GET['crop']) ? $conn->real_escape_string($_GET['crop']) : '';
$sort = isset($_GET['sort']) ? $conn->real_escape_string($_GET['sort']) : 'newest';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 20000;

// Build Query
$sql = "SELECT c.*, u.fullname as seller_name, u.role 
        FROM chemicals c 
        JOIN users u ON c.seller_id = u.id 
        WHERE c.status = 'Active' AND u.status = 'Active'";


if (!empty($search)) {
    $sql .= " AND (c.name LIKE '%$search%' OR c.description LIKE '%$search%' OR c.brand LIKE '%$search%')";
}
if ($category !== 'All' && !empty($category)) {
    $sql .= " AND c.category = '$category'";
}
if ($safety !== 'All' && !empty($safety)) {
    $sql .= " AND c.safety_label = '$safety'";
}
if (!empty($crop)) {
    $sql .= " AND c.crop_suitability LIKE '%$crop%'";
}

if ($min_price > 0) {
    $sql .= " AND c.price >= $min_price";
}
if ($max_price < 20000) {
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

$res_chems = $conn->query($sql);

// Fetch Categories for Filter (Dynamic)
$cat_query = "SELECT DISTINCT category FROM chemicals WHERE status = 'Active' ORDER BY category";
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
        --primary-blue: #2563eb;
        --hover-blue: #1d4ed8;
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
        align-items: start;
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
        top: 90px;
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
        color: var(--primary-blue);
        font-weight: 500;
    }
    
    /* Inputs */
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
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .price-range-inputs {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    /* Main Content */
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

    /* Product Card */
    .product-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* Smooth bounce */
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
        cursor: pointer; /* Fix: Add pointer */
    }

    .product-card:hover {
        transform: translateY(-8px); /* slightly more lift */
        box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.15); /* deeper shadow */
        border-color: #6366f1; /* subtle highlight */
    }

    .card-img-wrapper {
        position: relative;
        padding-top: 75%; /* 4:3 */
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
        /* transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94); Combined transition removed */
        backface-visibility: hidden; 
    }

    /* .product-card:hover .card-img {
        transform: scale(1.1); 
    } Zoom effect removed as requested */

    /* Badges */
    .safety-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .badge-green { background: #dcfce7; color: #166534; }
    .badge-blue { background: #dbeafe; color: #1e40af; }
    .badge-yellow { background: #fef9c3; color: #854d0e; }
    .badge-red { background: #fee2e2; color: #991b1b; }

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
        color: #6b7280;
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
        font-weight: 800;
        color: var(--primary-blue);
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
        background: var(--primary-blue);
        color: white;
    }
    
    /* Mobile Responsive */
    .mobile-filter-btn { display: none; }
    .sidebar-overlay { display: none; }

    @media (max-width: 992px) {
        .shop-layout { grid-template-columns: 1fr; }
        .shop-sidebar {
            display: none;
            position: fixed; top: 0; left: 0;
            height: 100vh; width: 80%; max-width: 320px;
            z-index: 3000;
            border-radius: 0;
        }
        .shop-sidebar.active { display: block; animation: slideIn 0.3s forwards; }
        .mobile-filter-btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 1rem; background: white;
            border: 1px solid #d1d5db; border-radius: 8px;
            font-weight: 600; color: #374151; cursor: pointer;
        }
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 2999;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.active { display: block; }
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
        <!-- Maintain Mobile Close Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;" class="d-lg-none">
            <h3 style="margin: 0;">Filters</h3>
            <button onclick="toggleSidebar()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>

        <form action="" method="GET">
            <?php if(!empty($sort)) echo "<input type='hidden' name='sort' value='$sort'>"; ?>

            <!-- Search -->
            <div class="filter-section">
                <h4 class="filter-title">Search</h4>
                <input type="text" name="search" class="form-input-sm" placeholder="Chemical name, brand..." value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <!-- Categories -->
            <div class="filter-section">
                <h4 class="filter-title">Categories</h4>
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <a href="chemical_store.php?category=All&search=<?php echo $search; ?>&safety=<?php echo $safety; ?>" 
                       class="filter-link <?php echo ($category === 'All') ? 'active' : ''; ?>">
                        All Categories <?php if($category === 'All') echo '<i class="fas fa-check"></i>'; ?>
                    </a>
                    <?php foreach($categories as $cat): 
                        $isActive = ($category === $cat) ? 'active' : '';
                        $checkIcon = ($category === $cat) ? '<i class="fas fa-check"></i>' : '';
                    ?>
                        <a href="chemical_store.php?category=<?php echo urlencode($cat); ?>&search=<?php echo $search; ?>&safety=<?php echo $safety; ?>" 
                           class="filter-link <?php echo $isActive; ?>">
                            <?php echo $cat; ?> <?php echo $checkIcon; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <!-- Hidden Input -->
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
            </div>

            <!-- Safety Level -->
            <div class="filter-section">
                <h4 class="filter-title">Safety Level</h4>
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                     <?php 
                     $levels = ['All', 'Green', 'Blue', 'Yellow', 'Red'];
                     foreach($levels as $lvl):
                        $isActive = ($safety === $lvl) ? 'active' : '';
                        $lbl = ($lvl === 'All') ? 'Any Level' : $lvl . ' Level';
                     ?>
                        <a href="chemical_store.php?safety=<?php echo $lvl; ?>&category=<?php echo urlencode($category); ?>&search=<?php echo $search; ?>" 
                           class="filter-link <?php echo $isActive; ?>">
                           <?php echo $lbl; ?> 
                           <?php if($isActive) echo '<i class="fas fa-check"></i>'; ?>
                        </a>
                     <?php endforeach; ?>
                </div>
                 <input type="hidden" name="safety" value="<?php echo htmlspecialchars($safety); ?>">
            </div>

            <!-- Suitable Crop (Text for now) -->
            <div class="filter-section">
                <h4 class="filter-title">Suitable For (Crop)</h4>
                <input type="text" name="crop" class="form-input-sm" placeholder="e.g. Wheat" value="<?php echo htmlspecialchars($crop); ?>">
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
            <?php if (!empty($search) || $category !== 'All' || $safety !== 'All' || !empty($crop)): ?>
                <a href="chemical_store.php" class="btn btn-outline btn-full" style="display: block; width: 100%; margin-top: 0.5rem; text-align: center; border-radius: 8px;">Clear All</a>
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
                <span style="font-weight: 600; color: #4b5563;">
                    <?php echo $res_chems->num_rows; ?> Chemicals Found
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
            <?php if ($res_chems && $res_chems->num_rows > 0): ?>
                <?php while ($row = $res_chems->fetch_assoc()): 
                    $img = !empty($row['image_path']) ? $row['image_path'] : 'images/default_chem.png';
                    // Fix path if needed
                    if(strpos($img, 'images/') === 0) $img = $img;

                    $safetyVal = $row['safety_label'] ?? 'Green';
                    $badgeClass = 'badge-' . strtolower($safetyVal);
                    $seller = ($row['role'] === 'admin') ? 'AgriAI Verified' : $row['seller_name'];
                ?>
                <div class="product-card">
                    <a href="chemical_details.php?id=<?php echo $row['id']; ?>" class="card-img-wrapper">
                         <span class="safety-badge <?php echo $badgeClass; ?>">
                            <?php echo $safetyVal; ?> Safety
                         </span>
                         <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="card-img">
                    </a>
                    
                    <div class="card-body">
                         <div class="product-cat"><?php echo htmlspecialchars($row['category']); ?></div>
                         <h3 class="product-title">
                            <a href="chemical_details.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </a>
                         </h3>
                         
                         <div class="seller-info">
                            <i class="fas fa-flask" style="font-size: 0.8rem;"></i>
                            <span><?php echo htmlspecialchars($seller); ?> @ <?php echo $row['brand']; ?></span>
                         </div>
                         
                         <!-- Crop Tags (First 2) -->
                         <div style="margin-bottom: 1rem; display: flex; gap: 5px; flex-wrap: wrap;">
                            <?php 
                            $crops = explode(',', $row['crop_suitability']);
                            foreach(array_slice($crops, 0, 2) as $c):
                            ?>
                             <span style="font-size: 0.7rem; background: #eff6ff; color: #2563eb; padding: 2px 6px; border-radius: 4px;">
                                <?php echo trim($c); ?>
                             </span>
                            <?php endforeach; ?>
                         </div>

                         <div class="card-footer">
                             <div>
                                 <span class="price-tag">₹<?php echo number_format($row['price']); ?></span>
                                 <span style="font-size: 0.75rem; color: #6b7280;">/<?php echo $row['pack_size']; ?></span>
                             </div>

                             <div style="display: flex; gap: 0.5rem;">
                                <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                                 <form action="add_to_cart.php" method="POST" style="display:inline;">
                                     <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                     <input type="hidden" name="qty" value="1">
                                     <input type="hidden" name="type" value="chemical">
                                     <input type="hidden" name="redirect" value="chemical_store">
                                     <button type="submit" class="btn-add" title="Add to Cart">
                                         <i class="fas fa-shopping-basket"></i>
                                     </button>
                                 </form>
                                 <a href="checkout.php?product_id=<?php echo $row['id']; ?>&qty=1&type=chemical" class="btn-add" style="background:#0284c7; color:white;" title="Buy Now">
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
                     <h3>No Chemicals Found</h3>
                     <p>Try adjusting your search or safety level filters.</p>
                     <a href="chemical_store.php" class="btn btn-primary">Reset Filters</a>
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