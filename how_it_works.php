<?php
session_start();
include 'db_connect.php';

$page_title = 'How It Works - AgriAI';

// Fix Services Menu Visibility
$extra_css = "
<style>
    /* Force Solid Header on this page */
    .glass-header-nav, #main-header {
        background: #ffffff !important;
        backdrop-filter: none !important;
        border-bottom: 1px solid #e5e7eb !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        z-index: 10000 !important;
        position: sticky !important;
    }
</style>
";

include 'includes/main_header.php';
?>

<style>
    /* Custom page styles */
    .step-card {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        height: 100%;
        position: relative;
        z-index: 1;
    }

    .step-card:hover {
        transform: translateY(-5px);
    }

    .step-number {
        width: 50px;
        height: 50px;
        background: var(--primary-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
    }

    /* Simple Tab System */
    .role-tabs {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 3rem;
    }

    .role-tab {
        padding: 1rem 2rem;
        border-radius: 2rem;
        cursor: pointer;
        font-weight: 600;
        background: #f3f4f6;
        color: #4b5563;
        border: 2px solid transparent;
        transition: all 0.3s;
    }

    .role-tab.active {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
    }

    .role-content {
        display: none;
        animation: fadeIn 0.5s;
    }

    .role-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .connector-line {
        position: absolute;
        top: 2rem;
        left: 50%;
        width: 2px;
        height: 100%;
        background: #e5e7eb;
        z-index: 0;
        display: none;
        /* Show only on desktop if needed, currently hidden for cleaner grid */
    }
</style>

    <!-- Page Header -->
    <header class="page-header"
        style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1595113316349-9fa4eb24f884?q=80&w=1772&auto=format&fit=crop') center/cover; padding: 5rem 0; text-align: center; color: white;">
        <div class="container">
            <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 1rem;">How It Works</h1>
            <p style="font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto;">Simplifying agriculture with a
                seamless digital marketplace.</p>
        </div>
    </header>

    <!-- Role Selection & Steps -->
    <section class="section" style="padding: 5rem 0;">
        <div class="container">
            <div class="role-tabs">
                <div class="role-tab active" onclick="switchRole('farmer')">I am a Farmer</div>
                <div class="role-tab" onclick="switchRole('buyer')">I am a Buyer</div>
            </div>

            <!-- Farmer Flow -->
            <div id="farmer-content" class="role-content active">
                <div style="text-align: center; margin-bottom: 3rem;">
                    <h2 style="margin-bottom: 1rem;">For <span class="highlight">Farmers</span></h2>
                    <p style="color: #6b7280;">Get fair prices and reach a wider market in 4 simple steps.</p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h3>Create Account</h3>
                        <p style="color: #6b7280; margin-top: 1rem;">Register quickly using your mobile number. Verify
                            via OTP to ensure security.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h3>List Crops</h3>
                        <p style="color: #6b7280; margin-top: 1rem;">Upload photos and details of your produce. Our AI
                            will suggest the optimal grading and price.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h3>Receive Orders</h3>
                        <p style="color: #6b7280; margin-top: 1rem;">Get direct orders from buyers. Accept or negotiate
                            terms transparently.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h3>Deliver & Earn</h3>
                        <p style="color: #6b7280; margin-top: 1rem;">Deliver the produce and receive secure payment
                            directly into your bank account.</p>
                    </div>
                </div>
            </div>

            <!-- Buyer Flow -->
            <div id="buyer-content" class="role-content">
                <div style="text-align: center; margin-bottom: 3rem;">
                    <h2 style="margin-bottom: 1rem;">For <span class="highlight">Buyers</span></h2>
                    <p style="color: #6b7280;">Source fresh, high-quality produce directly from the farm.</p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h3>Register</h3>
                        <p style="color: #6b7280; margin-top: 1rem;">Sign up as a buyer (Trade/Retailer) to access
                            thousands of fresh listings.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h3>Browse Market</h3>
                        <p style="color: #6b7280; margin-top: 1rem;">Filter crops by location, quality, and price. Use
                            AI insights to predict market trends.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h3>Place Order</h3>
                        <p style="color: #6b7280; margin-top: 1rem;">Book your requirements instantly. Secure the deal
                            with our escrow payment system.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h3>Track Delivery</h3>
                        <p style="color: #6b7280; margin-top: 1rem;">Monitor your shipment in real-time until it reaches
                            your doorstep.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Tutorial -->
    <section class="section" style="background-color: #111827; color: white; padding: 5rem 0; text-align: center;">
        <div class="container">
            <h2 style="margin-bottom: 1rem; color: white;">See It in <span class="highlight">Action</span></h2>
            <p style="opacity: 0.8; margin-bottom: 3rem; max-width: 600px; margin-left: auto; margin-right: auto;">Watch
                how easy it is to list crops and secure deals in minutes.</p>

            <div
                style="max-width: 800px; margin: 0 auto; border-radius: 1rem; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5); position: relative; aspect-ratio: 16/9; background: #374151;">
                <!-- Placeholder for Video -->
                <div
                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column; cursor: pointer;">
                    <i class="fas fa-play-circle"
                        style="font-size: 5rem; color: white; opacity: 0.8; transition: opacity 0.3s; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.5));"></i>
                    <p style="margin-top: 1rem; font-weight: 600;">Play Demo Video</p>
                </div>
                <img src="https://images.unsplash.com/photo-1595113316349-9fa4eb24f884?q=80&w=1772&auto=format&fit=crop"
                    style="width: 100%; height: 100%; object-fit: cover; opacity: 0.4;">
            </div>
        </div>
    </section>

    <!-- Comparison Table -->
    <section class="section" style="padding: 5rem 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="margin-bottom: 1rem;">Why Choose <span class="highlight">AgriAI</span>?</h2>
                <p style="color: #6b7280;">See how we compare to traditional markets.</p>
            </div>

            <div style="overflow-x: auto;">
                <table
                    style="width: 100%; max-width: 800px; margin: 0 auto; border-collapse: collapse; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <thead>
                        <tr style="background: #f9fafb;">
                            <th style="padding: 1.5rem; text-align: left; color: #6b7280; font-weight: 600;">Feature
                            </th>
                            <th style="padding: 1.5rem; text-align: center; color: #dc2626; font-weight: 600;">
                                Traditional Mandi</th>
                            <th
                                style="padding: 1.5rem; text-align: center; color: var(--primary-color); font-weight: 700; background: #f0fdf4;">
                                AgriAI Platform</th>
                        </tr>
                    </thead>
                    <tbody style="background: white;">
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 1.5rem; font-weight: 600; color: #374151;">Profit Share</td>
                            <td style="padding: 1.5rem; text-align: center; color: #6b7280;">Farmer gets < 60%</td>
                            <td
                                style="padding: 1.5rem; text-align: center; color: #16a34a; font-weight: 700; background: #f0fdf4;">
                                Farmer gets > 90%</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 1.5rem; font-weight: 600; color: #374151;">Payment Time</td>
                            <td style="padding: 1.5rem; text-align: center; color: #6b7280;">Weeks or Months</td>
                            <td
                                style="padding: 1.5rem; text-align: center; color: #16a34a; font-weight: 700; background: #f0fdf4;">
                                Within 24 Hours</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 1.5rem; font-weight: 600; color: #374151;">Pricing Transparency</td>
                            <td style="padding: 1.5rem; text-align: center; color: #6b7280;">Opaque / Hidden</td>
                            <td
                                style="padding: 1.5rem; text-align: center; color: #16a34a; font-weight: 700; background: #f0fdf4;">
                                100% Transparent</td>
                        </tr>
                        <tr>
                            <td style="padding: 1.5rem; font-weight: 600; color: #374151;">Market Access</td>
                            <td style="padding: 1.5rem; text-align: center; color: #6b7280;">Local Only</td>
                            <td
                                style="padding: 1.5rem; text-align: center; color: #16a34a; font-weight: 700; background: #f0fdf4;">
                                National Coverage</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Technology Section -->
    <section style="background-color: #f0fdf4; padding: 5rem 0;">
        <div class="container">
            <div style="display: flex; gap: 4rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 300px;">
                    <img src="https://images.unsplash.com/photo-1555664424-778a69032054?q=80&w=1665&auto=format&fit=crop"
                        style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1);"
                        alt="AI Technology">
                </div>
                <div style="flex: 1; min-width: 300px;">
                    <h2 style="margin-bottom: 1.5rem;">The <span class="highlight">Technology</span> Behind It</h2>

                    <div style="margin-bottom: 2rem;">
                        <h4 style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;"><i
                                class="fas fa-robot" style="color: var(--primary-color);"></i> AI Grading</h4>
                        <p style="color: #6b7280;">Our computer vision algorithms analyze crop photos to determine
                            quality grades (A, B, C) automatically, ensuring standardisation.</p>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <h4 style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;"><i
                                class="fas fa-chart-line" style="color: var(--primary-color);"></i> Dynamic Pricing</h4>
                        <p style="color: #6b7280;">Machine learning models scan regional market data to suggest fair
                            prices that benefit both farmers and buyers.</p>
                    </div>

                    <div>
                        <h4 style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;"><i
                                class="fas fa-lock" style="color: var(--primary-color);"></i> Secure Ledger</h4>
                        <p style="color: #6b7280;">Every transaction is recorded securely, preventing fraud and ensuring
                            payment guarantees.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Benefits -->
    <section class="section" style="padding: 5rem 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="margin-bottom: 1rem;">Unlock Your <span class="highlight">Potential</span></h2>
                <p style="color: #6b7280;">Real value for every stakeholder in the ecosystem.</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                <!-- Benefit 1 -->
                <div style="display: flex; gap: 1.5rem; align-items: flex-start;">
                    <div style="width: 50px; height: 50px; background: #e0f2fe; color: #0284c7; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h4 style="margin-bottom: 0.5rem; font-size: 1.1rem;">Higher Margins</h4>
                        <p style="color: #6b7280; font-size: 0.95rem; line-height: 1.6;">By cutting out multiple layers of middlemen, farmers retain a significantly higher portion of the final sale price.</p>
                    </div>
                </div>
                
                <!-- Benefit 2 -->
                <div style="display: flex; gap: 1.5rem; align-items: flex-start;">
                    <div style="width: 50px; height: 50px; background: #fef3c7; color: #d97706; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div>
                        <h4 style="margin-bottom: 0.5rem; font-size: 1.1rem;">Wider Reach</h4>
                        <p style="color: #6b7280; font-size: 0.95rem; line-height: 1.6;">Access buyers from across the country, not just those in your local mandi vicinity.</p>
                    </div>
                </div>

                <!-- Benefit 3 -->
                <div style="display: flex; gap: 1.5rem; align-items: flex-start;">
                    <div style="width: 50px; height: 50px; background: #fce7f3; color: #db2777; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h4 style="margin-bottom: 0.5rem; font-size: 1.1rem;">Zero Risk</h4>
                        <p style="color: #6b7280; font-size: 0.95rem; line-height: 1.6;">Escrow payments mean money is locked before produce moves. No more bad debts.</p>
                    </div>
                </div>

                <!-- Benefit 4 -->
                <div style="display: flex; gap: 1.5rem; align-items: flex-start;">
                    <div style="width: 50px; height: 50px; background: #ede9fe; color: #7c3aed; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div>
                        <h4 style="margin-bottom: 0.5rem; font-size: 1.1rem;">Ease of Use</h4>
                        <p style="color: #6b7280; font-size: 0.95rem; line-height: 1.6;">Designed for mobile-first. Manage your entire business from a basic smartphone.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust & Safety -->
    <section class="section" style="background-color: #fafafa; padding: 5rem 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="margin-bottom: 1rem;">Safety <span class="highlight">First</span></h2>
                <p style="color: #6b7280;">We prioritize the security of your money and produce.</p>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; text-align: center;">
                <div style="background: white; padding: 2rem; border-radius: 1rem; border: 1px solid #e5e7eb;">
                    <i class="fas fa-lock" style="font-size: 2.5rem; color: #16a34a; margin-bottom: 1.5rem;"></i>
                    <h3 style="margin-bottom: 1rem;">Escrow Protection</h3>
                    <p style="color: #6b7280; font-size: 0.95rem;">Funds are held safely in a neutral account until you confirm receipt of the goods. Only then is the seller paid.</p>
                </div>
                <div style="background: white; padding: 2rem; border-radius: 1rem; border: 1px solid #e5e7eb;">
                    <i class="fas fa-certificate" style="font-size: 2.5rem; color: #16a34a; margin-bottom: 1.5rem;"></i>
                    <h3 style="margin-bottom: 1rem;">Quality Verified</h3>
                    <p style="color: #6b7280; font-size: 0.95rem;">Our AI grading provides an objective quality score, so you know exactly what you are buying or selling.</p>
                </div>
                <div style="background: white; padding: 2rem; border-radius: 1rem; border: 1px solid #e5e7eb;">
                    <i class="fas fa-user-shield" style="font-size: 2.5rem; color: #16a34a; margin-bottom: 1.5rem;"></i>
                    <h3 style="margin-bottom: 1rem;">Verified Users</h3>
                    <p style="color: #6b7280; font-size: 0.95rem;">Every farmer and buyer undergoes KYC verification (Aadhar/PAN) to ensure a trustworthy community.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Getting Started Checklist -->
    <section class="section" style="padding: 5rem 0;">
        <div class="container" style="max-width: 800px;">
            <div style="background: #16a34a; border-radius: 1rem; padding: 3rem; color: white;">
                <h2 style="margin-bottom: 2rem; text-align: center; color: white;">Ready to Start?</h2>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <input type="checkbox" checked onclick="return false;" style="width: 20px; height: 20px;">
                        <span style="font-size: 1.1rem;">Register with your mobile number</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <input type="checkbox" checked onclick="return false;" style="width: 20px; height: 20px;">
                        <span style="font-size: 1.1rem;">Complete your profile (KYC for traders)</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <input type="checkbox" checked onclick="return false;" style="width: 20px; height: 20px;">
                        <span style="font-size: 1.1rem;">Upload crop photos (Farmers) or Add filters (Buyers)</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <input type="checkbox" checked onclick="return false;" style="width: 20px; height: 20px;">
                        <span style="font-size: 1.1rem;">Start trading instantly!</span>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="register.php" class="btn" style="background: white; color: #16a34a; padding: 0.8rem 2rem; border-radius: 0.5rem; font-weight: 600; text-decoration: none; display: inline-block;">Create Free Account</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Lite -->
    <section class="section" style="padding: 5rem 0;">
        <div class="container" style="text-align: center;">
            <p style="margin-bottom: 1rem; color: #6b7280;">Still have questions?</p>
            <h2 style="margin-bottom: 2rem;">Common Queries</h2>
            <div style="max-width: 700px; margin: 0 auto; text-align: left;">
                <details
                    style="background: white; border: 1px solid #e5e7eb; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <summary style="font-weight: 600; cursor: pointer;">How do I get paid?</summary>
                    <p style="margin-top: 1rem; color: #6b7280;">Pixels are transferred directly to your registered bank
                        account within 24 hours of successful delivery.</p>
                </details>
                <details
                    style="background: white; border: 1px solid #e5e7eb; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <summary style="font-weight: 600; cursor: pointer;">Who handles transportation?</summary>
                    <p style="margin-top: 1rem; color: #6b7280;">Buying parties usually arrange logistics, but we offer
                        a network of verified transport partners you can book directly.</p>
                </details>
            </div>
            <a href="contact.php" class="btn btn-outline" style="margin-top: 2rem; display: inline-block;">Contact
                Support</a>
        </div>
    </section>

    <?php include 'includes/main_footer.php'; ?>

    <script>
        function switchRole(role) {
            // Update tabs
            document.querySelectorAll('.role-tab').forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');

            // Update content
            document.querySelectorAll('.role-content').forEach(content => content.classList.remove('active'));
            document.getElementById(role + '-content').classList.add('active');
        }
    </script>