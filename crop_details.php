<?php
// crop_details.php
include 'db_connect.php';
session_start();

$page_title = 'Product Details';

// Get Crop ID
if (!isset($_GET['id'])) {
    header("Location: shop_crops.php");
    exit();
}
$id = intval($_GET['id']);

$sql = "SELECT c.*, u.fullname as farmer_name, u.state, u.district, u.location, fp.verification_status 
        FROM crops c 
        JOIN users u ON c.farmer_id = u.id 
        LEFT JOIN farmer_profiles fp ON u.id = fp.user_id
        WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Product not found.";
    exit();
}

$product = $result->fetch_assoc();

// Images
$img_path = !empty($product["image_path"]) ? $product["image_path"] : 'images/default-crop.png';
if (!preg_match('/^https?:\/\//', $img_path) && strpos($img_path, '../') !== 0) {
    // If we're already in the root, 'images/...' works directly, no '../' needed for crop_details.php as it's in the root
}

// Location
$loc_str = "Location N/A";
if (!empty($product['district']) || !empty($product['state'])) {
    $loc_str = $product['district'] . ', ' . $product['state'];
}

// CSS
$extra_css = "
<style>
    body {
        background-color: #f9fafb;
    }
    .details-container {
        max-width: 1200px;
        margin: 4rem auto;
        padding: 0 1.5rem;
    }
    .breadcrumb {
        font-size: 0.9rem;
        color: #6b7280;
        margin-bottom: 2rem;
    }
    .breadcrumb a {
        color: #16a34a;
        text-decoration: none;
    }
    .product-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .image-gallery {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.6);
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        transition: all 0.3s ease;
    }

    .image-gallery:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.1);
        border-bottom: 3px solid #16a34a; /* primary-green */
    }
    .main-image {
        width: 100%;
        max-height: 400px;
        display: block;
        object-fit: contain;
    }
    .product-info h1 {
        font-size: 2.25rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    .farmer-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f0fdf4;
        color: #15803d;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 1.5rem;
    }
    .price-tag {
        font-size: 2rem;
        font-weight: 700;
        color: #16a34a;
        margin-bottom: 1.5rem;
    }
    .price-unit {
        font-size: 1rem;
        color: #6b7280;
        font-weight: 500;
    }
    .description {
        color: #4b5563;
        line-height: 1.7;
        margin-bottom: 2rem;
        font-size: 1.05rem;
    }
    .meta-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: #f8fafc;
        border-radius: 12px;
    }
    .meta-item label {
        display: block;
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .meta-item span {
        font-weight: 600;
        color: #334155;
    }
    .action-area {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }
    .qty-input {
        width: 80px;
        padding: 0 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 1.1rem;
        text-align: center;
        font-weight: 600;
    }
    .btn-add-cart {
        flex: 1;
        background: white;
        border: 2px solid #16a34a;
        color: #16a34a;
        padding: 1rem;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-add-cart:hover {
        background: #f0fdf4;
    }
    .btn-buy-now {
        flex: 1;
        background: #16a34a;
        color: white;
        border: none;
        padding: 1rem;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-buy-now:hover {
        background: #15803d;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
    }
    
    @media (max-width: 768px) {
        .product-grid {
            grid-template-columns: 1fr;
            padding: 1.5rem;
            gap: 2rem;
        }
    }
</style>
";

include 'includes/main_header.php';
?>

<div class="details-container">
    <div class="breadcrumb">
        <a href="index.php">Home</a> / <a href="shop_crops.php">Marketplace</a> / <span><?php echo htmlspecialchars($product['name']); ?></span>
    </div>

    <div class="product-grid">
        <!-- Left: Image -->
        <div style="display: flex; justify-content: center; align-items: flex-start;">
            <img src="<?php echo htmlspecialchars($img_path); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; max-height: 400px; object-fit: contain; border-radius: 16px;">
        </div>

        <!-- Right: Info -->
        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="farmer-badge">
                <i class="fas fa-user-circle"></i>
                <span>Sold by <?php echo htmlspecialchars($product['farmer_name']); ?></span>
                <?php if ($product['verification_status'] == 'verified'): ?>
                    <i class="fas fa-check-circle" style="color: #16a34a;" title="Verified Farmer"></i>
                <?php endif; ?>
            </div>

            <div class="price-tag">
                ₹<?php echo number_format($product['price'], 0); ?>
                <span class="price-unit">/ <?php echo htmlspecialchars($product['price_unit']); ?></span>
            </div>

            <div class="description">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>

            <div class="meta-grid">
                <div class="meta-item">
                    <label>Category</label>
                    <span><?php echo htmlspecialchars($product['category']); ?></span>
                </div>
                <div class="meta-item">
                    <label>Harvest Season</label>
                    <span><?php echo htmlspecialchars($product['season'] ?? 'N/A'); ?></span>
                </div>
                <div class="meta-item">
                    <label>Location</label>
                    <span><i class="fas fa-map-marker-alt" style="color: #ef4444; margin-right: 4px;"></i> <?php echo htmlspecialchars($loc_str); ?></span>
                </div>
                <div class="meta-item">
                    <label>Stock Status</label>
                    <?php 
                    $color = ($product['stock_status'] == 'Out of Stock') ? '#ef4444' : '#16a34a';
                    ?>
                    <span style="color: <?php echo $color; ?>"><?php echo htmlspecialchars($product['stock_status']); ?></span>
                </div>
            </div>

            <!-- Actions -->
            <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="redirect" value="shop_crops"> 
                    
                    <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 2rem;">
                        <span style="font-weight: 600; color: #374151;">Quantity:</span>
                        <input type="number" name="qty" class="qty-input" value="1" min="1">
                    </div>

                    <div class="action-area">
                        <button type="submit" class="btn-add-cart">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        
                        <a href="#" onclick="buyNow(event)" class="btn-buy-now">
                            Buy Now
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function buyNow(e) {
        e.preventDefault();
        const qty = document.querySelector('input[name="qty"]').value;
        const id = '<?php echo $product['id']; ?>';
        window.location.href = `checkout.php?product_id=${id}&qty=${qty}`;
    }
</script>

<?php include 'includes/main_footer.php'; ?>
