<?php
session_start();
include 'db_connect.php';

// --- Filter Logic ---
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$location = isset($_GET['location']) ? $conn->real_escape_string($_GET['location']) : '';

// Build Query
$sql = "SELECT c.*, u.fullname as farmer_name, u.state, u.district, fp.verification_status 
        FROM crops c 
        JOIN users u ON c.farmer_id = u.id 
        LEFT JOIN farmer_profiles fp ON u.id = fp.user_id
        WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (c.name LIKE '%$search%' OR c.description LIKE '%$search%')";
}
if (!empty($location)) {
    $sql .= " AND (u.state LIKE '%$location%' OR u.district LIKE '%$location%' OR u.location LIKE '%$location%')";
}

$sql .= " ORDER BY c.created_at DESC";
$result = $conn->query($sql);

$page_title = 'Browse Crops - Public Marketplace';

// Store page-specific CSS
$extra_css = "
<style>
    .browse-hero {
        position: relative;
        background: url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=1932&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        padding: 6rem 0;
        color: white;
        text-align: center;
        margin-bottom: 4rem;
        border-radius: 0 0 50% 50% / 4%; /* Subtle curve at the bottom */
        overflow: hidden;
    }

    .browse-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(22, 163, 74, 0.85), rgba(17, 24, 39, 0.9));
        z-index: 1;
    }

    .browse-hero .container {
        position: relative;
        z-index: 2;
    }

    .hero-title {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        font-weight: 800;
        letter-spacing: -1px;
        text-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }

    .filter-bar {
        background: white;
        padding: 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 3rem;
        border: 1px solid rgba(0,0,0,0.05); /* Cleaner border */
        position: relative;
        top: -30px; /* Float overlapping text */
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
        position: relative;
    }

    /* Stats Widgets */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 4rem;
        position: relative;
        z-index: 10;
        margin-top: -50px; /* Overlap with hero */
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 1rem;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        display: inline-block;
        padding: 10px;
        border-radius: 12px;
    }

    /* Filter Inputs */
    .filter-input {
        width: 100%;
        padding: 0.85rem 1rem;
        padding-left: 2.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 50px; /* Pill shape */
        outline: none;
        transition: all 0.2s;
        background: #f9fafb;
    }

    .filter-input:focus {
        border-color: var(--primary-color);
        background: white;
        box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1);
    }

    /* Crop Cards */
    .crop-card {
        background: white;
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        border: 1px solid #f3f4f6;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .crop-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: rgba(22, 163, 74, 0.2);
    }

    .crop-image-container {
        height: 220px;
        overflow: hidden;
        position: relative;
    }

    .crop-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .crop-card:hover .crop-image {
        transform: scale(1.05);
    }

    .badge-verified {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255, 255, 255, 0.95);
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #16a34a;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(4px);
    }

    .price-tag {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary-color);
        letter-spacing: -0.5px;
    }

    .ai-insight-badge {
        background: linear-gradient(135deg, #818cf8, #c084fc);
        color: white;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
    }

    /* Force Solid Header on this page */
    .glass-header-nav, #main-header {
        background: #ffffff !important;
        backdrop-filter: none !important;
        border-bottom: 1px solid #e5e7eb !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        z-index: 10000 !important; /* Ensure it stays above everything */
        position: sticky !important;
    }
</style>
";

include 'includes/main_header.php';
?>

<!-- Hero Section -->
<header class="browse-hero">
    <div class="container">
        <h1 class="hero-title">Browse <span class="highlight" style="color: #bef264;">Fresh Crops</span></h1>
        <p
            style="font-size: 1.3rem; opacity: 0.95; max-width: 600px; margin: 0 auto; line-height: 1.6; font-weight: 300;">
            Connect directly with verified farmers. <br>Fresh produce, transparent pricing, and quality guaranteed.
        </p>
    </div>
</header>

<div class="container">

    <!-- AI Insights Mockup -->
    <!-- AI Insights Layout -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #ecfccb; color: #4d7c0f;">
                <i class="fas fa-fire"></i>
            </div>
            <h4 style="color: #1f2937; margin-bottom: 0.25rem;">Trending Now</h4>
            <p style="color: #6b7280; font-size: 0.9rem; line-height: 1.4;">
                <strong style="color: #4d7c0f;">Wheat</strong> is currently in high demand across Punjab.
            </p>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #e0f2fe; color: #0369a1;">
                <i class="fas fa-chart-line"></i>
            </div>
            <h4 style="color: #1f2937; margin-bottom: 0.25rem;">Market Insight</h4>
            <p style="color: #6b7280; font-size: 0.9rem; line-height: 1.4;">
                <strong style="color: #0369a1;">Rice</strong> prices are stable. Good time for bulk purchase.
            </p>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fae8ff; color: #a21caf;">
                <i class="fas fa-leaf"></i>
            </div>
            <h4 style="color: #1f2937; margin-bottom: 0.25rem;">Seasonal Pick</h4>
            <p style="color: #6b7280; font-size: 0.9rem; line-height: 1.4;">
                <strong style="color: #a21caf;">Chilli</strong> harvest is peaking. Expect fresh stock.
            </p>
        </div>
    </div>

    <!-- Filters -->
    <form action="" method="GET" class="filter-bar">
        <div class="filter-group">
            <i class="fas fa-search filter-icon"></i>
            <input type="text" name="search" class="filter-input" placeholder="Search crops (e.g. Wheat)..."
                value="<?php echo htmlspecialchars($search); ?>">
        </div>

        <!-- Category Filter Removed temporarily as DB column is missing -->

        <div class="filter-group">
            <i class="fas fa-map-marker-alt filter-icon"></i>
            <input type="text" name="location" class="filter-input" placeholder="Location (State/District)..."
                value="<?php echo htmlspecialchars($location); ?>">
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 0.5rem;">
            Apply Filters
        </button>
        <?php if (!empty($search) || !empty($category) || !empty($location)): ?>
            <a href="browse_crops.php" class="btn btn-outline"
                style="padding: 0.75rem 1.5rem; border-radius: 0.5rem;">Reset</a>
        <?php endif; ?>
    </form>

    <!-- Crop Grid -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; padding-bottom: 4rem;">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $img_path = !empty($row["image_path"]) ? $row["image_path"] : 'images/default-crop.png';
                // Fix path if needed
                if (strpos($img_path, 'images/') === FALSE && strpos($img_path, 'http') === FALSE) {
                    $img_path = 'images/' . $img_path;
                }

                // Location string
                $loc_str = "Location N/A";
                if (!empty($row['district']) || !empty($row['state'])) {
                    $loc_str = $row['district'] . ', ' . $row['state'];
                }
                ?>
                <div class="crop-card">
                    <div class="crop-image-container">
                        <img src="<?php echo $img_path; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>"
                            class="crop-image">
                        <?php if ($row['verification_status'] == 'Verified'): ?>
                            <div class="badge-verified">
                                <i class="fas fa-check-circle"></i> Verified
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="padding: 1.75rem; flex-grow: 1; display: flex; flex-direction: column;">
                        <div style="margin-bottom: 0.75rem;">
                            <!-- Mock AI Badge based on generic logic for demo visual -->
                            <?php if (rand(0, 1) == 1): ?>
                                <span class="ai-insight-badge"><i class="fas fa-bolt"></i> High Demand</span>
                            <?php endif; ?>
                        </div>

                        <h3
                            style="font-size: 1.35rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; text-transform: capitalize; letter-spacing: -0.5px;">
                            <?php echo htmlspecialchars($row['name']); ?>
                        </h3>

                        <div
                            style="font-size: 0.95rem; color: #6b7280; margin-bottom: 1.5rem; display:flex; align-items:center;">
                            <i class="fas fa-map-marker-alt" style="color: #9ca3af; margin-right: 0.5rem;"></i>
                            <?php echo htmlspecialchars($loc_str); ?>
                        </div>

                        <div style="font-size: 0.9rem; color: #16a34a; font-weight: 600; margin-bottom: 1.5rem;">
                            <i class="fas fa-store-alt" style="margin-right: 5px;"></i>
                            <a href="farmer_shop.php?id=<?php echo $row['farmer_id']; ?>"
                                style="color: inherit; text-decoration: none; border-bottom: 1px dashed transparent; transition: all 0.2s;"
                                onmouseover="this.style.borderBottomColor='#16a34a'"
                                onmouseout="this.style.borderBottomColor='transparent'">
                                Sold by: <?php echo htmlspecialchars($row['farmer_name']); ?>
                            </a>
                        </div>

                        <div style="margin-top: auto;">
                            <div
                                style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.25rem;">
                                <div>
                                    <span
                                        style="font-size: 0.8rem; text-transform:uppercase; letter-spacing:1px; color: #9ca3af; font-weight:600;">Price</span>
                                    <div class="price-tag">
                                        ₹<?php echo number_format($row['price'], 0); ?><span
                                            style="font-size: 0.9rem; font-weight: 500; color: #9ca3af;">/<?php echo $row['price_unit']; ?></span>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div
                                        style="font-size: 0.85rem; font-weight: 600; padding:4px 10px; border-radius:6px; background: <?php echo ($row['stock_status'] == 'Out of Stock') ? '#fee2e2' : '#dcfce7'; ?>; color: <?php echo ($row['stock_status'] == 'Out of Stock') ? '#ef4444' : '#16a34a'; ?>;">
                                        <?php echo $row['stock_status']; ?>
                                    </div>
                                </div>
                            </div>

                            <a href="register.php" class="btn btn-primary"
                                style="width: 100%; text-align: center; border-radius: 50px; padding: 0.8rem;">
                                Login to Buy <i class="fas fa-arrow-right" style="margin-left: 5px; font-size: 0.9em;"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; color: #6b7280;">
                <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem; color: #d1d5db;"></i>
                <h3>No crops found matching your filters.</h3>
                <p>Try adjusting your search criteria or checking back later.</p>
                <a href="browse_crops.php" class="btn btn-link">Clear Filters</a>
            </div>
        <?php } ?>
    </div>

</div>

<?php include 'includes/main_footer.php'; ?>