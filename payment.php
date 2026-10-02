<?php
// payment.php - PUBLIC REDESIGN
include 'db_connect.php';
session_start();

$is_checkout = ($_SERVER['REQUEST_METHOD'] === 'POST');

// Data for Checkout Mode
if ($is_checkout) {
    if (!isset($_POST['total_amount'])) {
        header("Location: shop_crops.php");
        exit();
    }
    $total_amount = $_POST['total_amount'];
}

$page = 'payment';
$page_title = $is_checkout ? 'Secure Payment | AgriAI' : 'Payment Information | AgriAI';

// Premium Styles
$extra_css = "
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.4);
        --premium-blue: #0284c7;
        --premium-green: #16a34a;
        --dark-glass: rgba(15, 23, 42, 0.8);
    }

    body {
        background: radial-gradient(circle at bottom left, #ecfdf5, #f0f9ff, #ffffff);
    }

    .hero-section {
        text-align: center;
        padding: 4rem 0 2rem;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        padding: 2.5rem;
        margin-bottom: 2rem;
        transition: transform 0.3s ease;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }

    .step-card {
        text-align: center;
        padding: 2rem;
    }

    .step-num {
        width: 40px;
        height: 40px;
        background: var(--premium-green);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-weight: 800;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
    }

    .method-tile {
        background: white;
        border: 2px solid transparent;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .method-tile:hover {
        border-color: var(--premium-green);
        background: #f0fdf4;
        transform: translateY(-5px);
    }

    .method-tile input { display: none; }
    .method-tile input:checked + div { border-color: var(--premium-green); background: #f0fdf4; }

    .faq-item {
        margin-bottom: 1rem;
        border-radius: 12px;
        overflow: hidden;
    }

    .faq-trigger {
        width: 100%;
        text-align: left;
        padding: 1.2rem;
        background: rgba(255,255,255,0.5);
        border: 1px solid var(--glass-border);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        color: #1e293b;
    }

    .faq-content {
        padding: 0 1.2rem;
        max-height: 0;
        overflow: hidden;
        transition: all 0.3s ease-out;
        background: rgba(255,255,255,0.3);
    }

    .faq-item.active .faq-content {
        padding: 1.2rem;
        max-height: 200px;
    }

    .security-banner {
        background: var(--dark-glass);
        color: white;
        padding: 3rem;
        border-radius: 24px;
        text-align: center;
        margin-top: 4rem;
    }

    .trust-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        border-radius: 50px;
        background: rgba(255,255,255,0.1);
        margin: 10px;
    }
</style>
";

$hide_sub_header = true;
include 'includes/main_header.php';
?>

<div class="container" style="padding-bottom: 5rem;">

    <?php if ($is_checkout): ?>
        <!-- CHECKOUT MODE -->
        <div class="hero-section">
            <h1 style="font-weight: 800; color:#1e293b;">Final Step: Payment</h1>
            <p style="color:#64748b;">Choose your preferred gateway to complete the transaction.</p>
        </div>

        <div style="max-width: 800px; margin: 0 auto;">
            <div class="glass-card">
                <div style="text-align:center; margin-bottom: 2.5rem;">
                    <span style="font-size: 0.9rem; text-transform:uppercase; font-weight:700; color:#64748b;">Total Payable
                        Amount</span>
                    <h2 style="font-size: 3rem; font-weight:900; color:var(--premium-green); margin:0;">
                        ₹<?php echo number_format($total_amount, 2); ?></h2>
                </div>

                <form action="process_order.php" method="POST">
                    <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
                    <input type="hidden" name="fullname" value="<?php echo htmlspecialchars($_POST['fullname']); ?>">
                    <input type="hidden" name="address" value="<?php echo htmlspecialchars($_POST['address']); ?>">
                    <input type="hidden" name="phone" value="<?php echo htmlspecialchars($_POST['phone']); ?>">

                    <?php if (isset($_POST['product_id'])): ?>
                        <input type="hidden" name="product_id" value="<?php echo $_POST['product_id']; ?>">
                        <input type="hidden" name="qty" value="<?php echo $_POST['qty']; ?>">
                    <?php endif; ?>

                    <h4 style="margin-bottom: 1.5rem; font-weight:700;">Select Payment Method</h4>
                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2.5rem;">

                        <label class="method-tile">
                            <input type="radio" name="payment_method" value="UPI" checked>
                            <div style="font-size: 1.5rem; color:#4CAF50; margin-bottom: 10px;"><i
                                    class="fas fa-mobile-alt"></i></div>
                            <span style="font-weight:600; font-size: 0.9rem;">UPI / Google Pay</span>
                        </label>

                        <label class="method-tile">
                            <input type="radio" name="payment_method" value="Card">
                            <div style="font-size: 1.5rem; color:#1976D2; margin-bottom: 10px;"><i
                                    class="fas fa-credit-card"></i></div>
                            <span style="font-weight:600; font-size: 0.9rem;">Debit / Credit Card</span>
                        </label>

                        <label class="method-tile">
                            <input type="radio" name="payment_method" value="NetBanking">
                            <div style="font-size: 1.5rem; color:#6366f1; margin-bottom: 10px;"><i
                                    class="fas fa-university"></i></div>
                            <span style="font-weight:600; font-size: 0.9rem;">Net Banking</span>
                        </label>

                        <label class="method-tile">
                            <input type="radio" name="payment_method" value="COD">
                            <div style="font-size: 1.5rem; color:#f59e0b; margin-bottom: 10px;"><i
                                    class="fas fa-hand-holding-usd"></i></div>
                            <span style="font-weight:600; font-size: 0.9rem;">Cash on Delivery</span>
                        </label>
                    </div>

                    <!-- Hidden UPI Scanner -->
                    <div id="upi-scanner-section" style="display: none; background: white; padding: 2rem; border-radius: 16px; border: 1px solid var(--glass-border); margin-bottom: 2rem; text-align: center;">
                        <h5 style="margin-bottom: 1rem; font-weight: 700; color: #1e293b;">Scan QR to Pay</h5>
                        <img src="images/scanner.jpeg" alt="UPI QR Code" style="width: 200px; height: 200px; object-fit: contain; border: 2px dashed var(--premium-green); padding: 10px; border-radius: 10px;">
                        <p style="margin-top: 1rem; color: #64748b; font-size: 0.9rem;">Scan with any UPI App (GPay, PhonePe, Paytm)</p>
                        <button type="submit" class="btn btn-primary" onclick="window.open('https://pay.google.com', '_blank');" style="margin-top: 1.5rem; padding: 10px 30px; border-radius: 50px; font-weight: 700;">Continue to Pay</button>
                    </div>

                    <!-- Hidden Credit Card Form -->
                    <div id="card-details-form" style="display: none; background: rgba(255,255,255,0.8); padding: 1.5rem; border-radius: 16px; border: 1px solid var(--glass-border); margin-bottom: 2rem;">
                        <h5 style="margin-bottom: 1rem; font-weight: 700; color: #1e293b;">Enter Card Details</h5>
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #64748b; margin-bottom: 5px;">Card Holder Name</label>
                            <input type="text" placeholder="John Doe" class="form-control" style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #64748b; margin-bottom: 5px;">Card Number</label>
                            <input type="text" placeholder="xxxx xxxx xxxx xxxx" maxlength="19" class="form-control" style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid #cbd5e1; outline: none;">
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <div style="flex: 1;">
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #64748b; margin-bottom: 5px;">Expiry Date</label>
                                <input type="text" placeholder="MM/YY" maxlength="5" class="form-control" style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid #cbd5e1; outline: none;">
                            </div>
                            <div style="flex: 1;">
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #64748b; margin-bottom: 5px;">CVV</label>
                                <input type="password" placeholder="123" maxlength="3" class="form-control" style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid #cbd5e1; outline: none;">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary"
                        style="width:100%; padding: 1.2rem; border-radius:15px; font-weight:800; font-size: 1.1rem; box-shadow: 0 10px 25px rgba(22, 163, 74, 0.25);">
                        <i class="fas fa-lock" style="margin-right:10px;"></i> CONFIRM & PAY SECURELY
                    </button>

                    <p style="text-align:center; font-size: 0.8rem; color:#64748b; margin-top: 1.5rem;">
                        Your payment is encrypted and locally processed. No card details are stored.
                    </p>
                </form>
            </div>
        </div>

    <?php else: ?>
        <!-- INFORMATIONAL MODE -->
        <div class="hero-section">
            <h1 style="font-weight: 800; color:#1e293b; font-size: 3rem;">How Payments Work</h1>
            <p style="color:#64748b; font-size: 1.1rem; max-width: 600px; margin: 1rem auto;">Fast, transparent, and built
                for modern agriculture. Learn how we handle your business securely.</p>
        </div>

        <div class="info-grid">
            <div class="glass-card step-card">
                <div class="step-num">1</div>
                <i class="fas fa-shopping-cart"
                    style="font-size: 2.5rem; color:var(--premium-green); margin-bottom: 1.5rem;"></i>
                <h3 style="margin-bottom: 1rem;">Buyer Places Order</h3>
                <p style="color:#64748b; font-size: 0.9rem;">Buyers choose crops or chemicals and pay through our secure
                    gateway.</p>
            </div>

            <div class="glass-card step-card">
                <div class="step-num">2</div>
                <i class="fas fa-shield-alt"
                    style="font-size: 2.5rem; color:var(--premium-blue); margin-bottom: 1.5rem;"></i>
                <h3 style="margin-bottom: 1rem;">Payment Escrow</h3>
                <p style="color:#64748b; font-size: 0.9rem;">Funds are safely held until the farmer confirms shipment and
                    delivery is tracked.</p>
            </div>

            <div class="glass-card step-card">
                <div class="step-num">3</div>
                <i class="fas fa-wallet" style="font-size: 2.5rem; color:#f59e0b; margin-bottom: 1.5rem;"></i>
                <h3 style="margin-bottom: 1rem;">Farmer Earnings</h3>
                <p style="color:#64748b; font-size: 0.9rem;">Once delivery is verified, earnings are instantly accessible in
                    the farmer's wallet.</p>
            </div>
        </div>

        <!-- Farmer Assurance Section -->
        <div class="glass-card" style="margin-top: 3rem; display:flex; gap:3rem; align-items:center; flex-wrap:wrap;">
            <div style="flex:1; min-width:300px;">
                <h2 style="font-weight:800; margin-bottom: 1.5rem;">Farmer Payment Assurance</h2>
                <div style="display:grid; gap:1.2rem;">
                    <div style="display:flex; gap:15px; align-items:center;">
                        <i class="fas fa-check-circle" style="color:var(--premium-green);"></i>
                        <span><strong>Zero Hidden Fees:</strong> No setup or listing costs. Only pay a small flat commission
                            on sales.</span>
                    </div>
                    <div style="display:flex; gap:15px; align-items:center;">
                        <i class="fas fa-check-circle" style="color:var(--premium-green);"></i>
                        <span><strong>Instant Settlement:</strong> Daily payouts triggered directly into your primary bank
                            account.</span>
                    </div>
                    <div style="display:flex; gap:15px; align-items:center;">
                        <i class="fas fa-check-circle" style="color:var(--premium-green);"></i>
                        <span><strong>Full Transparency:</strong> Complete sales ledger and tax reports available in your
                            dashboard.</span>
                    </div>
                </div>
            </div>
            <div
                style="flex:1; min-width:300px; padding: 2rem; background: rgba(0,0,0,0.03); border-radius: 20px; text-align:center;">
                <h1 style="font-size: 4rem; color:var(--premium-green); font-weight:900; margin-bottom:0;">0%</h1>
                <p style="font-weight:700; color:#1e293b;">Registration fees for all farmers</p>
                <a href="register.php" class="btn btn-outline" style="margin-top: 1rem;">BECOME A SELLER</a>
            </div>
        </div>

        <!-- FAQ Section -->
        <div style="margin-top: 5rem;">
            <h2 style="text-align:center; margin-bottom: 3rem; font-weight:800;">Frequently Asked Questions</h2>
            <div style="max-width: 800px; margin: 0 auto;">
                <?php
                $faqs = [
                    ["Is online payment safe for farmers?", "Yes, we use industry-standard encryption and our escrow system ensures that you are only shipping after funds are secured from the buyer."],
                    ["What happens if an order is cancelled?", "If an order is cancelled before shipping, the buyer is refunded automatically. If it's cancelled during transit, our dispute resolution team handles the payout logic based on logistics."],
                    ["How long does it take to get paid?", "Payment is settled to your bank account within 24-48 hours after the buyer confirms successful receipt of the high-quality produce."],
                    ["Which payment methods are supported?", "We support all major UPI apps, Debit/Credit Cards, and Net Banking from all localized banks."]
                ];
                foreach ($faqs as $i => $faq):
                    ?>
                    <div class="faq-item">
                        <button class="faq-trigger" onclick="toggleFaq(<?php echo $i; ?>)">
                            <?php echo $faq[0]; ?>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-content" id="faq-<?php echo $i; ?>">
                            <p><?php echo $faq[1]; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Security Banner -->
        <div class="security-banner">
            <i class="fas fa-shield-alt" style="font-size: 4rem; color:var(--premium-green); margin-bottom: 2rem;"></i>
            <h2 style="font-weight:800; margin-bottom: 1.5rem;">Enterprise-Grade Security</h2>
            <p style="max-width: 600px; margin: 0 auto 2.5rem; opacity:0.8;">We prioritize your financial safety. Our
                platform utilizes advanced encryption and real-time fraud monitoring to protect every transaction.</p>
            <div style="display:flex; justify-content:center; flex-wrap:wrap;">
                <div class="trust-badge"><i class="fas fa-lock"></i> AES-256 Encryption</div>
                <div class="trust-badge"><i class="fas fa-user-shield"></i> Fraud Protection</div>
                <div class="trust-badge"><i class="fas fa-file-invoice-dollar"></i> GST Compliant</div>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
    function toggleFaq(index) {
        // Toggle Active Class
        const item = event.currentTarget.parentElement;
        item.classList.toggle('active');

        // Toggle Icon
        const icon = event.currentTarget.querySelector('i');
        icon.classList.toggle('fa-chevron-up');
        icon.classList.toggle('fa-chevron-down');
    }

    // Auto-select animation for method tiles
    document.querySelectorAll('.method-tile').forEach(tile => {
        tile.addEventListener('click', function () {
            // Updated: Also handle radio selection logic manually if click is on label
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            document.querySelectorAll('.method-tile').forEach(t => t.style.borderColor = 'transparent');
            this.style.borderColor = 'var(--premium-green)';

            // Toggle Card Form & UPI Scanner
            const cardForm = document.getElementById('card-details-form');
            const upiScanner = document.getElementById('upi-scanner-section');

            // Reset both
            cardForm.style.display = 'none';
            upiScanner.style.display = 'none';

            if (radio.value === 'Card') {
                cardForm.style.display = 'block';
            } else if (radio.value === 'UPI') {
                upiScanner.style.display = 'block';
            }
        });
    });
</script>

<?php include 'includes/main_footer.php'; ?>

</html>