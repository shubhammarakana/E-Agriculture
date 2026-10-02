<?php
session_start();
include 'db_connect.php';

// Fetch Crops
$sql_crops = "SELECT c.*, u.fullname as seller_name, u.role FROM crops c JOIN users u ON c.farmer_id = u.id WHERE u.status = 'Active' ORDER BY c.id DESC LIMIT 10";
$res_crops = $conn->query($sql_crops);

// Fetch Chemicals
$sql_chems = "SELECT c.*, u.fullname as seller_name, u.role FROM chemicals c JOIN users u ON c.seller_id = u.id WHERE c.status = 'Active' AND u.status = 'Active' ORDER BY c.id DESC LIMIT 10";
$res_chems = $conn->query($sql_chems);

// Fetch Seeds
$sql_seeds = "SELECT s.*, u.fullname as seller_name, u.role FROM seeds s JOIN users u ON s.seller_id = u.id WHERE s.status = 'Active' AND u.status = 'Active' ORDER BY s.id DESC LIMIT 10";
$res_seeds = $conn->query($sql_seeds);
?>
<!DOCTYPE html>
<html lang="en">
<?php
$page_title = 'AI-Powered Agriculture E-Marketplace';
$hide_sub_header = true;
$extra_css = '
<style>
    /* Hero Dropdown Styles */
    .hero-dropdown { position: relative; display: inline-block; }
    .hero-dropdown-content { display: none; position: absolute; background: #ffffff; min-width: 220px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); border-radius: 12px; border: 1px solid #f3f4f6; z-index: 9999; top: 110%; left: 0; padding: 8px; animation: slideDown 0.3s ease-out; }
    .hero-dropdown-content.show { display: block; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .hero-dropdown:hover .hero-dropdown-content { display: block; }
    .hero-dropdown-content a { color: #374151 !important; padding: 12px 16px; text-decoration: none; display: block; border-radius: 8px; font-size: 0.95rem; font-weight: 500; transition: all 0.2s; }
    .hero-dropdown-content a:hover { background: #f0fdf4; color: #16a34a !important; padding-left: 20px; }
    .hero-dropdown-content a i { width: 20px; margin-right: 10px; color: #16a34a; opacity: 1; }
    .hero-section { overflow: visible !important; z-index: 20; position: relative; }

    /* Products Grid & Cards */
    .products-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1.5rem; }
    @media (max-width: 1400px) { .products-grid { grid-template-columns: repeat(4, 1fr); } }
    @media (max-width: 1200px) { .products-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px)  { .products-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px)  { .products-grid { grid-template-columns: 1fr; } }

    .product-card { background: white; border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden; transition: all 0.3s ease; display: flex; flex-direction: column; height: 100%; text-decoration: none; color: inherit; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); }
    .card-img-wrapper { position: relative; padding-top: 75%; overflow: hidden; background: #fff; border-bottom: 1px solid #f3f4f6; }
    .card-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; padding: 1rem; }
    .card-body { padding: 1.25rem; flex-grow: 1; display: flex; flex-direction: column; }
    .product-cat { font-size: 0.75rem; text-transform: uppercase; color: #6b7280; font-weight: 600; margin-bottom: 0.25rem; }
    .product-title { font-size: 1.1rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .seller-info { display: flex; align-items: center; gap: 6px; font-size: 0.85rem; color: #6b7280; margin-bottom: 1rem; }
    .card-footer { margin-top: auto; padding-top: 1rem; border-top: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center; }
    .price-tag { font-size: 1.25rem; font-weight: 800; color: #16a34a; }
    .show-more-btn { display: inline-block; padding: 10px 28px; background-color: white; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.3s ease; }
    .btn-outline-green { border: 2px solid #16a34a; color: #16a34a; }
    .btn-outline-green:hover { background: #16a34a; color: white; }
    .btn-outline-blue { border: 2px solid #2563eb; color: #2563eb; }
    .btn-outline-blue:hover { background: #2563eb; color: white; }
    .btn-outline-emerald { border: 2px solid #059669; color: #059669; }
    .btn-outline-emerald:hover { background: #059669; color: white; }

    /* Brand Section Styles */
    .brand-section {
        background: #f0f7f3;
        padding: 50px 0;
        margin-bottom: 3rem;
        border-radius: 20px;
        overflow: hidden; /* Hide overflow for marquee */
    }
    .brand-header {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        margin-bottom: 30px;
        padding: 0 40px;
    }
    .brand-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }
    .brand-view-all {
        position: absolute;
        right: 40px;
        color: #16a34a;
        font-weight: 600;
        text-decoration: none;
        font-size: 0.95rem;
    }
    .brand-view-all:hover {
        text-decoration: underline;
    }
    
    /* Marquee Styles */
    .brand-marquee-container {
        width: 100%;
        overflow: hidden;
        position: relative;
    }
    
    .brand-track {
        display: flex;
        width: calc(200px * 14); /* Adjust based on card width + gap * total items */
        animation: scroll 30s linear infinite;
        gap: 20px;
        padding: 10px 0;
    }
    
    .brand-track:hover {
        animation-play-state: paused;
    }
    
    @keyframes scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(calc(-200px * 7 - 140px)); } /* Scroll half of the total cards (original set) */
    }

    .brand-card {
        background: white;
        flex: 0 0 180px; /* Fixed width for cards */
        height: 110px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        padding: 15px;
        cursor: pointer;
    }
    .brand-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        background: #fdfdfd;
    }
    .brand-card img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
</style>
';
include 'includes/main_header.php';
?>

<div class="container" style="max-width: 96% !important; margin: 0 auto; padding-top: 2rem; padding-bottom: 4rem;">

    <!-- BRAND LOGOS SECTION -->
    <div class="brand-section">
        <div class="brand-header">
            <h2 class="brand-title">Our Trusted Partners</h2>
            <a href="#" class="brand-view-all">View All Brands</a>
        </div>
        <div class="brand-marquee-container">
            <div class="brand-track">
                <!-- Original Set (7 Brands) -->
                <div class="brand-card"><img src="images/brands/greenleaf.png" alt="GreenLeaf Agri"></div>
                <div class="brand-card"><img src="images/brands/pureharvest.png" alt="PureHarvest"></div>
                <div class="brand-card"><img src="images/brands/terragrow.png" alt="TerraGrow"></div>
                <div class="brand-card"><img src="images/brands/agropulse.png" alt="AgroPulse"></div>
                <div class="brand-card"><img src="images/brands/fieldmaster.png" alt="FieldMaster"></div>
                <div class="brand-card"><img src="images/brands/biofert.png" alt="BioFert"></div>
                <div class="brand-card"><img src="images/logo2.png" alt="AgriAI Premium"></div>

                <!-- Duplicate Set for Seamless Scrolling -->
                <div class="brand-card"><img src="images/brands/greenleaf.png" alt="GreenLeaf Agri"></div>
                <div class="brand-card"><img src="images/brands/pureharvest.png" alt="PureHarvest"></div>
                <div class="brand-card"><img src="images/brands/terragrow.png" alt="TerraGrow"></div>
                <div class="brand-card"><img src="images/brands/agropulse.png" alt="AgroPulse"></div>
                <div class="brand-card"><img src="images/brands/fieldmaster.png" alt="FieldMaster"></div>
                <div class="brand-card"><img src="images/brands/biofert.png" alt="BioFert"></div>
                <div class="brand-card"><img src="images/logo2.png" alt="AgriAI Premium"></div>
            </div>
        </div>
    </div>


    <!-- CROPS SECTION -->
    <div
        style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem; margin-top: 2rem;">
        <h2 style="font-size: 1.75rem; font-weight: 700; color: #111827;">Fresh Crops</h2>
        <a href="shop_crops.php" style="color: #16a34a; font-weight: 600; text-decoration: none;">View All <i
                class="fas fa-arrow-right"></i></a>
    </div>
    <div class="products-grid" id="crops-grid">
        <?php if ($res_crops && $res_crops->num_rows > 0): ?>
            <?php while ($row = $res_crops->fetch_assoc()):
                $img = !empty($row["image_path"]) ? $row["image_path"] : 'images/default-crop.png';
                if (strpos($img, 'images/') === 0)
                    $img = $img;
                $seller = ($row['role'] === 'admin') ? 'AgriAI Verified' : $row['seller_name'];
                ?>
                <a href="crop_details.php?id=<?php echo $row['id']; ?>" class="product-card">
                    <div class="card-img-wrapper">
                        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="card-img"
                            style="border-radius: 32px; object-fit: cover;">
                    </div>
                    <div class="card-body">
                        <div class="product-cat"><?php echo htmlspecialchars($row['category']); ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <div class="seller-info"><i class="fas fa-store-alt"></i> <?php echo htmlspecialchars($seller); ?></div>
                        <div class="card-footer">
                            <span class="price-tag">₹<?php echo number_format($row['price']); ?> <span
                                    style="font-size: 0.8rem; color: #6b7280; font-weight: 500;">/<?php echo $row['price_unit']; ?></span></span>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No crops presently available.</p>
        <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 2.5rem;">
        <button class="show-more-btn btn-outline-green" data-type="crops" data-offset="10">Show More Crops</button>
    </div>

    <!-- CHEMICALS SECTION -->
    <div
        style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem; margin-top: 4rem;">
        <h2 style="font-size: 1.75rem; font-weight: 700; color: #111827;">Agricultural Chemicals</h2>
        <a href="chemical_store.php" style="color: #2563eb; font-weight: 600; text-decoration: none;">View All <i
                class="fas fa-arrow-right"></i></a>
    </div>
    <div class="products-grid" id="chemicals-grid">
        <?php if ($res_chems && $res_chems->num_rows > 0): ?>
            <?php while ($row = $res_chems->fetch_assoc()):
                $img = !empty($row["image_path"]) ? $row["image_path"] : 'images/default_chem.png';
                if (strpos($img, 'images/') === 0)
                    $img = $img;
                $seller = ($row['role'] === 'admin') ? 'AgriAI Verified' : $row['seller_name'];
                ?>
                <a href="chemical_details.php?id=<?php echo $row['id']; ?>" class="product-card">
                    <div class="card-img-wrapper">
                        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="card-img">
                    </div>
                    <div class="card-body">
                        <div class="product-cat"><?php echo htmlspecialchars($row['category']); ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <div class="seller-info"><i class="fas fa-flask"></i> <?php echo htmlspecialchars($seller); ?></div>
                        <div class="card-footer">
                            <span class="price-tag" style="color:#2563eb;">₹<?php echo number_format($row['price']); ?></span>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No chemicals presently available.</p>
        <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 2.5rem;">
        <button class="show-more-btn btn-outline-blue" data-type="chemicals" data-offset="10">Show More
            Chemicals</button>
    </div>

    <!-- SEEDS SECTION -->
    <div
        style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem; margin-top: 4rem;">
        <h2 style="font-size: 1.75rem; font-weight: 700; color: #111827;">Quality Seeds</h2>
        <a href="seed_store.php" style="color: #059669; font-weight: 600; text-decoration: none;">View All <i
                class="fas fa-arrow-right"></i></a>
    </div>
    <div class="products-grid" id="seeds-grid">
        <?php if ($res_seeds && $res_seeds->num_rows > 0): ?>
            <?php while ($row = $res_seeds->fetch_assoc()):
                $img = !empty($row["image_path"]) ? $row["image_path"] : 'images/default_seed.png';
                if (strpos($img, 'images/') === 0)
                    $img = $img;
                $seller = ($row['role'] === 'admin') ? 'AgriAI Verified' : $row['seller_name'];
                ?>
                <a href="seed_details.php?id=<?php echo $row['id']; ?>" class="product-card">
                    <div class="card-img-wrapper">
                        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="card-img">
                    </div>
                    <div class="card-body">
                        <div class="product-cat"><?php echo htmlspecialchars($row['category']); ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <div class="seller-info"><i class="fas fa-seedling"></i> <?php echo htmlspecialchars($seller); ?></div>
                        <div class="card-footer">
                            <span class="price-tag" style="color:#059669;">₹<?php echo number_format($row['price']); ?></span>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No seeds presently available.</p>
        <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 2.5rem;">
        <button class="show-more-btn btn-outline-emerald" data-type="seeds" data-offset="10">Show More Seeds</button>
    </div>

</div>

<?php include 'includes/main_footer.php'; ?>
<script src="js/script.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const showMoreBtns = document.querySelectorAll('.show-more-btn');

        showMoreBtns.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const type = this.getAttribute('data-type');
                const offset = parseInt(this.getAttribute('data-offset'));
                const limit = 10;
                const btnEl = this;
                const originalText = btnEl.innerText;

                btnEl.innerText = 'Loading...';
                btnEl.disabled = true;

                fetch(`load_more_products.php?type=${type}&offset=${offset}&limit=${limit}`)
                    .then(response => response.text())
                    .then(html => {
                        if (html.trim() !== '') {
                            const gridId = `${type}-grid`;
                            const grid = document.getElementById(gridId);
                            grid.insertAdjacentHTML('beforeend', html);

                            btnEl.setAttribute('data-offset', offset + limit);
                            btnEl.innerText = originalText;
                            btnEl.disabled = false;
                        } else {
                            btnEl.innerText = 'No more products';
                            btnEl.style.opacity = '0.5';
                            btnEl.style.cursor = 'not-allowed';
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching products:', err);
                        btnEl.innerText = originalText;
                        btnEl.disabled = false;
                    });
            });
        });
    });
</script>
</body>

</html>