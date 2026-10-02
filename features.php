<?php
session_start();
include 'db_connect.php';

$page_title = 'Platform Features - AgriAI';

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
    .feature-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        border-top: 4px solid transparent;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .feature-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
    }

    /* Gradient borders for different sections */
    .core-feature {
        border-top-color: #16a34a;
    }

    .ai-feature {
        border-top-color: #2563eb;
    }

    .tech-badge {
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        color: #4b5563;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<!-- Page Header -->
<header class="page-header"
    style="background: linear-gradient(rgba(17, 24, 39, 0.8), rgba(17, 24, 39, 0.9)), url('https://images.unsplash.com/photo-1627920769822-1011e4f4fb85?q=80&w=1920&auto=format&fit=crop') center/cover; padding: 6rem 0; text-align: center; color: white;">
    <div class="container">
        <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 1rem;">Platform Features</h1>
        <p style="font-size: 1.25rem; opacity: 0.9; max-width: 700px; margin: 0 auto;">
            A comprehensive digital solution simplifying agricultural trading through innovation and AI.
        </p>
    </div>
</header>

<!-- Core Capabilities -->
<section class="section" style="padding: 5rem 0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h2 style="font-size: 2.25rem; margin-bottom: 1rem;">Core <span class="highlight">Capabilities</span>
            </h2>
            <p style="color: #6b7280; font-size: 1.1rem;">Building the foundation of trust and efficiency.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
            <!-- Feature 1 -->
            <div class="feature-card core-feature">
                <div class="feature-icon-wrapper" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3 style="margin-bottom: 1rem;">Direct Trading</h3>
                <p style="color: #6b7280; line-height: 1.6;">Eliminate intermediaries by connecting farmers directly
                    with buyers. Negotiate prices transparently and keep more profit.</p>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card core-feature">
                <div class="feature-icon-wrapper" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 style="margin-bottom: 1rem;">Real-Time Markets</h3>
                <p style="color: #6b7280; line-height: 1.6;">Access live price updates from mandis across the
                    region. Make informed decisions based on current market rates.</p>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card core-feature">
                <div class="feature-icon-wrapper" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3 style="margin-bottom: 1rem;">Secure Payments</h3>
                <p style="color: #6b7280; line-height: 1.6;">Digital escrow system ensures farmers get paid upon
                    delivery. No more delayed payments or bad debts.</p>
            </div>

            <!-- Feature 4 -->
            <div class="feature-card core-feature">
                <div class="feature-icon-wrapper" style="background: #dcfce7; color: #16a34a;">
                    <i class="fas fa-id-badge"></i>
                </div>
                <h3 style="margin-bottom: 1rem;">Verified Profiles</h3>
                <p style="color: #6b7280; line-height: 1.6;">Every user undergoes KYC verification to maintain a
                    safe and trustworthy community for all stakeholders.</p>
            </div>
        </div>
    </div>
</section>

<!-- AI-Powered Section -->
<section class="section" style="background-color: #f8fafc; padding: 5rem 0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h2 style="font-size: 2.25rem; margin-bottom: 1rem;">AI-Powered <span
                    style="color: #2563eb;">Intelligence</span></h2>
            <p style="color: #6b7280; font-size: 1.1rem;">Smart tools to reduce risk and maximize yield.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
            <!-- AI Feature 1 -->
            <div class="feature-card ai-feature">
                <div class="feature-icon-wrapper" style="background: #dbeafe; color: #2563eb;">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3 style="margin-bottom: 1rem;">Crop Recommendations</h3>
                <p style="color: #6b7280; line-height: 1.6;">AI analyzes soil data, season, and historical yields to
                    suggest the most profitable crops for your specific farm.</p>
            </div>

            <!-- AI Feature 2 -->
            <div class="feature-card ai-feature">
                <div class="feature-icon-wrapper" style="background: #dbeafe; color: #2563eb;">
                    <i class="fas fa-virus-slash"></i>
                </div>
                <h3 style="margin-bottom: 1rem;">Disease Detection</h3>
                <p style="color: #6b7280; line-height: 1.6;">Upload a photo of your plant leaf. Our computer vision
                    model identifies diseases instantly and suggests treatments.</p>
            </div>

            <!-- AI Feature 3 -->
            <div class="feature-card ai-feature">
                <div class="feature-icon-wrapper" style="background: #dbeafe; color: #2563eb;">
                    <i class="fas fa-search-dollar"></i>
                </div>
                <h3 style="margin-bottom: 1rem;">Demand Analysis</h3>
                <p style="color: #6b7280; line-height: 1.6;">Predictive analytics forecast which crops will be in
                    high demand, helping you plan your harvest better.</p>
            </div>

            <!-- AI Feature 4 -->
            <div class="feature-card ai-feature">
                <div class="feature-icon-wrapper" style="background: #dbeafe; color: #2563eb;">
                    <i class="fas fa-bell"></i>
                </div>
                <h3 style="margin-bottom: 1rem;">Smart Alerts</h3>
                <p style="color: #6b7280; line-height: 1.6;">Receive personalized notifications on the best time to
                    sell based on real-time price trends and market volatility.</p>
            </div>
        </div>
    </div>
</section>

<!-- Features by Role -->
<section class="section" style="padding: 5rem 0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h2 style="font-size: 2.25rem; margin-bottom: 1rem;">Tailored for <span class="highlight">Everyone</span>
            </h2>
            <p style="color: #6b7280; font-size: 1.1rem;">Dedicated tools for every stakeholder in the agriculture
                ecosystem.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <!-- For Farmers -->
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 1rem; overflow: hidden;">
                <div style="background: #f0fdf4; padding: 2rem; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="color: #16a34a; margin-bottom: 0.5rem;"><i class="fas fa-tractor"></i> For Farmers</h3>
                    <p style="color: #6b7280; font-size: 0.95rem;">Manage your harvest and income.</p>
                </div>
                <div style="padding: 2rem;">
                    <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 1rem;">
                        <li style="display: flex; gap: 10px; align-items: center;"><i class="fas fa-check-circle"
                                style="color: #16a34a;"></i> Easy Crop Listing</li>
                        <li style="display: flex; gap: 10px; align-items: center;"><i class="fas fa-check-circle"
                                style="color: #16a34a;"></i> Live Order Tracking</li>
                        <li style="display: flex; gap: 10px; align-items: center;"><i class="fas fa-check-circle"
                                style="color: #16a34a;"></i> Earning Analytics</li>
                        <li style="display: flex; gap: 10px; align-items: center;"><i class="fas fa-check-circle"
                                style="color: #16a34a;"></i> Weather Updates</li>
                    </ul>
                </div>
            </div>

            <!-- For Buyers -->
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 1rem; overflow: hidden;">
                <div style="background: #eff6ff; padding: 2rem; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="color: #2563eb; margin-bottom: 0.5rem;"><i class="fas fa-shopping-basket"></i> For Buyers
                    </h3>
                    <p style="color: #6b7280; font-size: 0.95rem;">Source fresh produce efficiently.</p>
                </div>
                <div style="padding: 2rem;">
                    <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 1rem;">
                        <li style="display: flex; gap: 10px; align-items: center;"><i class="fas fa-check-circle"
                                style="color: #2563eb;"></i> Advanced Search Filters</li>
                        <li style="display: flex; gap: 10px; align-items: center;"><i class="fas fa-check-circle"
                                style="color: #2563eb;"></i> Bulk Ordering</li>
                        <li style="display: flex; gap: 10px; align-items: center;"><i class="fas fa-check-circle"
                                style="color: #2563eb;"></i> Secure Escrow Wallet</li>
                        <li style="display: flex; gap: 10px; align-items: center;"><i class="fas fa-check-circle"
                                style="color: #2563eb;"></i> Verified Farmer Ratings</li>
                    </ul>
                </div>
            </div>

            <!-- For Admin -->
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 1rem; overflow: hidden;">
                <div style="background: #fdf2f8; padding: 2rem; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="color: #db2777; margin-bottom: 0.5rem;"><i class="fas fa-user-shield"></i> For Admins
                    </h3>
                    <p style="color: #6b7280; font-size: 0.95rem;">Oversee the platform integrity.</p>
                </div>
                <div style="padding: 2rem;">
                    <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 1rem;">
                        <li style="display: flex; gap: 10px; align-items: center;"><i class="fas fa-check-circle"
                                style="color: #db2777;"></i> User Verification (KYC)</li>
                        <li style="display: flex; gap: 10px; align-items: center;"><i class="fas fa-check-circle"
                                style="color: #db2777;"></i> Dispute Resolution</li>
                        <li style="display: flex; gap: 10px; align-items: center;"><i class="fas fa-check-circle"
                                style="color: #db2777;"></i> Platform Analytics</li>
                        <li style="display: flex; gap: 10px; align-items: center;"><i class="fas fa-check-circle"
                                style="color: #db2777;"></i> Content Management</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Data Security -->
<section class="section" style="background-color: #111827; color: white; padding: 5rem 0;">
    <div class="container">
        <div style="display: flex; align-items: center; gap: 4rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px;">
                <h2 style="margin-bottom: 1.5rem; color: white;">Enterprise-Grade <span
                        class="highlight">Security</span></h2>
                <p style="opacity: 0.8; margin-bottom: 2rem; line-height: 1.8;">
                    We understand that trust is the currency of agriculture. Our platform is built with a security-first
                    approach to protect your sensitive data and financial transactions.
                </p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div>
                        <h4 style="margin-bottom: 0.5rem; color: #16a34a;">End-to-End Encryption</h4>
                        <p style="font-size: 0.9rem; opacity: 0.7;">All data in transit and at rest is encrypted using
                            industry standards.</p>
                    </div>
                    <div>
                        <h4 style="margin-bottom: 0.5rem; color: #16a34a;">GDPR Compliant</h4>
                        <p style="font-size: 0.9rem; opacity: 0.7;">We respect your privacy and give you full control
                            over your data.</p>
                    </div>
                    <div>
                        <h4 style="margin-bottom: 0.5rem; color: #16a34a;">Regular Audits</h4>
                        <p style="font-size: 0.9rem; opacity: 0.7;">Security experts routinely test our systems for
                            vulnerabilities.</p>
                    </div>
                    <div>
                        <h4 style="margin-bottom: 0.5rem; color: #16a34a;">Secure Cloud</h4>
                        <p style="font-size: 0.9rem; opacity: 0.7;">Hosted on resilient cloud infrastructure with 99.9%
                            uptime.</p>
                    </div>
                </div>
            </div>
            <div style="flex: 1; min-width: 300px; display: flex; justify-content: center;">
                <i class="fas fa-shield-alt"
                    style="font-size: 15rem; color: #1f2937; text-shadow: 0 0 50px rgba(22, 163, 74, 0.3);"></i>
            </div>
        </div>
    </div>
</section>
<section class="section"
    style="padding: 5rem 0; background: linear-gradient(rgba(22, 163, 74, 0.95), rgba(22, 163, 74, 0.9)); color: white; text-align: center;">
    <div class="container">
        <h2 style="margin-bottom: 2rem;">Built for Performance</h2>
        <p style="opacity: 0.9; margin-bottom: 3rem; max-width: 600px; margin: 0 auto;">Engineered with robust
            technologies to ensure scalability, security, and speed.</p>

        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem;">
            <div class="tech-badge"><i class="fab fa-html5" style="color: #e34c26;"></i> HTML5 Structure</div>
            <div class="tech-badge"><i class="fab fa-css3-alt" style="color: #264de4;"></i> Modern CSS3</div>
            <div class="tech-badge"><i class="fab fa-js" style="color: #f7df1e;"></i> Interactive JS</div>
            <div class="tech-badge"><i class="fab fa-php" style="color: #777bb4;"></i> Dynamic PHP</div>
            <div class="tech-badge"><i class="fas fa-database" style="color: #00758f;"></i> MySQL Backend</div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section" style="padding: 4rem 0; text-align: center;">
    <div class="container">
        <h2 style="margin-bottom: 1rem;">Ready to Experience the Future?</h2>
        <p style="color: #6b7280; margin-bottom: 2rem;">Join thousands of smart farmers and buyers on AgriAI today.
        </p>
        <a href="register.php" class="btn">Get Started</a>
    </div>
</section>

<?php include 'includes/main_footer.php'; ?>