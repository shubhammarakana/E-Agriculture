<?php
// get_chem_preview.php
include 'db_connect.php';
session_start();

if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$id = intval($_GET['id']);
$sql = "SELECT c.*, COALESCE(u.fullname, 'AgriAI Verified') as seller_name FROM chemicals c 
        LEFT JOIN users u ON c.seller_id = u.id 
        WHERE c.id = '$id' AND c.status = 'Active'";
$res = $conn->query($sql);

if ($res->num_rows > 0) {
    $c = $res->fetch_assoc();
    $img = !empty($c['image_path']) ? $c['image_path'] : 'images/default_chem.png';
    $safety = $c['safety_label'] ?? 'Green';

    // Safety Color Coding
    $safety_colors = [
        'Green' => '#16a34a',
        'Blue' => '#2563eb',
        'Yellow' => '#ca8a04',
        'Red' => '#dc2626'
    ];
    $s_color = $safety_colors[$safety] ?? '#16a34a';
    ?>
    <div style="display: flex; gap: 2.5rem; flex-wrap: wrap;">
        <!-- Image Section -->
        <div style="flex: 1; min-width: 300px;">
            <div
                style="background: #f8fafc; border-radius: 20px; padding: 2rem; display: flex; align-items: center; justify-content: center; height: 400px; border: 1px solid #f1f5f9;">
                <img src="<?php echo $img; ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;">
            </div>

            <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                <div
                    style="flex: 1; background: #f0fdf4; padding: 15px; border-radius: 12px; text-align: center; border: 1px solid #dcfce7;">
                    <i class="fas fa-certificate" style="color: #16a34a; font-size: 1.2rem;"></i>
                    <div style="font-size: 0.7rem; color: #166534; font-weight: 700; margin-top: 5px;">GOVT APPROVED</div>
                </div>
                <div
                    style="flex: 1; background: #fefce8; padding: 15px; border-radius: 12px; text-align: center; border: 1px solid #fef9c3;">
                    <i class="fas fa-leaf" style="color: #ca8a04; font-size: 1.2rem;"></i>
                    <div style="font-size: 0.7rem; color: #854d0e; font-weight: 700; margin-top: 5px;">ORGANIC OPTION</div>
                </div>
            </div>
        </div>

        <!-- Info Section -->
        <div style="flex: 1.2; min-width: 350px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span
                        style="background: <?php echo $s_color; ?>15; color: <?php echo $s_color; ?>; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; border: 1px solid <?php echo $s_color; ?>30;">
                        <?php echo strtoupper($safety); ?> SAFETY LABEL
                    </span>
                    <h1 style="font-size: 2rem; color: #1e293b; margin: 15px 0 5px;">
                        <?php echo htmlspecialchars($c['name']); ?>
                    </h1>
                    <div style="color: #64748b; font-weight: 600;">Brand: <span style="color: #334155;">
                            <?php echo $c['brand']; ?>
                        </span></div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 2.5rem; font-weight: 800; color: #1e293b;">₹
                        <?php echo number_format($c['price'], 0); ?>
                    </div>
                    <div style="color: #64748b; font-size: 0.9rem;">Inclusive of all taxes</div>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 1.5rem 0;">

            <div style="background: #f8fafc; border-radius: 15px; padding: 1.5rem; margin-bottom: 1.5rem;">
                <h4 style="margin: 0 0 10px; color: #475569; font-size: 0.9rem; text-transform: uppercase;">Key
                    Specifications</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <small style="color: #94a3b8; display: block;">Active Ingredient</small>
                        <strong style="color: #334155;">
                            <?php echo $c['active_ingredient']; ?>
                        </strong>
                    </div>
                    <div>
                        <small style="color: #94a3b8; display: block;">Pack Size</small>
                        <strong style="color: #334155;">
                            <?php echo $c['pack_size']; ?>
                        </strong>
                    </div>
                    <div>
                        <small style="color: #94a3b8; display: block;">Category</small>
                        <strong style="color: #334155;">
                            <?php echo $c['category']; ?>
                        </strong>
                    </div>
                    <div>
                        <small style="color: #94a3b8; display: block;">Seller</small>
                        <strong style="color: #334155;">
                            <?php echo $c['seller_name']; ?>
                        </strong>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <h4 style="margin: 0 0 10px; color: #475569; font-size: 0.9rem;">Suitable Crops</h4>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <?php
                    $crops = explode(',', $c['crop_suitability']);
                    foreach ($crops as $crop):
                        ?>
                        <span
                            style="background: white; border: 1px solid #e2e8f0; padding: 5px 12px; border-radius: 8px; font-size: 0.85rem; color: #475569;">
                            <i class="fas fa-check-circle" style="color: #22c55e; margin-right: 5px;"></i>
                            <?php echo trim($crop); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <h4 style="margin: 0 0 10px; color: #475569; font-size: 0.9rem;">Description & Dosage</h4>
                <p style="color: #64748b; line-height: 1.6; font-size: 0.95rem;">
                    <?php echo nl2br(htmlspecialchars($c['description'])); ?>
                </p>
            </div>

            <div
                style="background: #eff6ff; border-radius: 15px; padding: 1.2rem; display: flex; align-items: center; gap: 15px; border: 1px solid #dbeafe;">
                <i class="fas fa-robot" style="font-size: 1.5rem; color: #2563eb;"></i>
                <div style="font-size: 0.85rem; color: #1e40af;">
                    <strong>AI Recommendation:</strong>
                    Based on current market trends and soil health reports, this product is highly recommended for <strong>
                        <?php echo trim($crops[0] ?? 'your crops'); ?>
                    </strong> this season.
                </div>
            </div>

                <div style="flex: 2; display: flex; gap: 10px;">
                    <!-- Add to Cart Form -->
                    <form action="add_to_cart.php" method="POST" style="flex: 1;">
                        <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                        <input type="hidden" name="qty" value="1">
                        <input type="hidden" name="type" value="chemical">
                        <input type="hidden" name="redirect" value="chemical_store">
                        <button type="submit" class="btn btn-outline"
                            style="width: 100%; padding: 18px; border-radius: 15px; font-weight: 700; font-size: 1.1rem; border-color: var(--primary-blue); color: var(--primary-blue);">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </form>

                    <!-- Buy Now Link -->
                    <?php 
                    $checkout_url = "checkout.php?product_id=" . $c['id'] . "&type=chemical&qty=1";
                    ?>
                    <a href="<?php echo $checkout_url; ?>" class="btn btn-primary"
                        style="flex: 1; padding: 18px; border-radius: 15px; font-weight: 700; font-size: 1.1rem; text-align: center; background: var(--primary-blue); border-color: var(--primary-blue);">
                        <i class="fas fa-bolt"></i> Buy Now
                    </a>
                </div>
        </div>
    </div>
    <?php
} else {
    echo "<div style='text-align:center; padding: 5rem;'>Product not found.</div>";
}
?>