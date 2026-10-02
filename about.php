<?php
session_start();
include 'db_connect.php';

$page_title = 'About Us - AgriAI';

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

<!-- Page Header -->
<header class="page-header"
    style="background: linear-gradient(rgba(22, 163, 74, 0.9), rgba(22, 163, 74, 0.8)); padding: 4rem 0; text-align: center; color: white;">
    <div class="container">
        <h1 style="font-size: 2.5rem; font-weight: 700;">About AgriAI</h1>
        <p style="font-size: 1.1rem; opacity: 0.9;">Transforming agriculture with technology.</p>
    </div>
</header>

<!-- About Section Content -->
<section class="section about-page-content" style="padding: 4rem 0;">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <div class="section-header" style="text-align: left; margin-bottom: 1.5rem;">
                    <h2>Our <span class="highlight">Mission</span></h2>
                </div>

                <p class="about-lead">
                    The <strong>AI-Powered Agriculture E-Marketplace System</strong> is a modern digital solution
                    built to transform traditional agricultural trading into a transparent, efficient, and
                    farmer-centric ecosystem.
                </p>

                <p>
                    We directly connect farmers, buyers, traders, and agri-businesses on a single online
                    marketplace,
                    eliminating middlemen to ensure fair pricing and expand market access beyond local boundaries.
                    By
                    providing real-time market prices, secure digital transactions, and direct buyer–farmer
                    communication, we build trust and transparency across the supply chain.
                </p>

                <p>
                    <strong>Powered by Artificial Intelligence</strong>, our platform enables crop recommendations,
                    disease detection, market demand insights, and smart selling alerts. These features empower
                    users to
                    make data-driven decisions, reduce risks, and improve productivity and profitability.
                </p>

                <p class="tech-stack-note">
                    <small>Built with HTML, CSS, JavaScript, PHP, and MySQL for security and scalability.</small>
                </p>
            </div>

            <div class="about-image">
                <div class="about-img-placeholder">
                    <i class="fa-solid fa-seedling" style="font-size: 5rem; color: white;"></i>
                    <div class="glass-float">
                        <i class="fa-solid fa-robot"></i> AI-Driven
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Content for Page Version -->
        <div style="margin-top: 4rem;">
            <h3 style="margin-bottom: 2rem; text-align: center;">Core Values</h3>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-users"></i></div>
                    <h3>Farmer Centric</h3>
                    <p>Prioritizing the needs and profitability of local farmers.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <h3>Transparency</h3>
                    <p>Open pricing and direct trading with no hidden fees.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-leaf"></i></div>
                    <h3>Sustainability</h3>
                    <p>Promoting eco-friendly farming practices through AI.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Our Vision & Impact -->
    <div class="container">
        <div style="display: flex; gap: 3rem; align-items: center;">
            <div style="flex: 1;">
                <img src="https://images.unsplash.com/photo-1625246333195-78d9c38ad449?q=80&w=1770&auto=format&fit=crop"
                    alt="Future Agriculture"
                    style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            </div>
            <div style="flex: 1;">
                <h2 style="margin-bottom: 1rem;">Our <span class="highlight">Vision</span></h2>
                <p style="font-size: 1.1rem; line-height: 1.6; color: #4b5563; margin-bottom: 1.5rem;">
                    We envision a world where technology bridges the gap between hard work and fair reward. A future
                    where every farmer has access to global markets, real-time data, and AI-driven insights to maximize
                    their potential.
                </p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div style="background: #f0fdf4; padding: 1.5rem; border-radius: 0.5rem; text-align: center;">
                        <h3 style="font-size: 2rem; color: #16a34a; margin-bottom: 0.5rem;">5000+</h3>
                        <p style="font-size: 0.9rem; color: #6b7280;">Farmers Empowered</p>
                    </div>
                    <div style="background: #f0fdf4; padding: 1.5rem; border-radius: 0.5rem; text-align: center;">
                        <h3 style="font-size: 2rem; color: #16a34a; margin-bottom: 0.5rem;">$2M+</h3>
                        <p style="font-size: 0.9rem; color: #6b7280;">Trade Volume</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- How We Work (Process) -->
    <div style="padding: 5rem 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 4rem;">
                <h2 style="margin-bottom: 1rem;">How It <span class="highlight">Works</span></h2>
                <p style="color: #6b7280;">Simplifying the agricultural supply chain in 3 easy steps.</p>
            </div>
            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3rem; text-align: center;">
                <div style="position: relative;">
                    <div
                        style="width: 80px; height: 80px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
                        <i class="fas fa-list"></i>
                    </div>
                    <h3 style="margin-bottom: 1rem;">1. List Produce</h3>
                    <p style="color: #6b7280;">Farmers list their crops with photos and details. AI suggests the best
                        price.</p>
                </div>
                <div>
                    <div
                        style="width: 80px; height: 80px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 style="margin-bottom: 1rem;">2. Connect</h3>
                    <p style="color: #6b7280;">Buyers browse listings and connect directly. Negotiations are
                        transparent.</p>
                </div>
                <div>
                    <div
                        style="width: 80px; height: 80px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3 style="margin-bottom: 1rem;">3. Deliver</h3>
                    <p style="color: #6b7280;">Secure payment is made, and produce is delivered efficiently to the
                        buyer.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sustainability -->
    <div
        style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1500651230702-0e2d8a49d4ad?q=80&w=1770&auto=format&fit=crop') center/cover fixed; padding: 6rem 0; color: white; text-align: center;">
        <div class="container">
            <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">Commited to Sustainability</h2>
            <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto 2rem; opacity: 0.9;">We are not just building
                a market; we are building a greener future. Our AI helps reduce waste, optimize resource use, and
                promote organic farming.</p>
            <a href="#" class="btn btn-primary"
                style="background: white; color: var(--primary-color); border: none;">Learn About Our Green
                Initiatives</a>
        </div>
    </div>


    <!-- Our Story -->
    <div style="background-color: #f9fafb; padding: 5rem 0; margin-top: 5rem;">
        <div class="container">
            <div style="text-align: center; max-width: 800px; margin: 0 auto;">
                <h2 style="margin-bottom: 2rem;">Our <span class="highlight">Story</span></h2>
                <div style="position: relative; padding-left: 2rem; border-left: 4px solid #e5e7eb; text-align: left;">
                    <div style="margin-bottom: 2rem; position: relative;">
                        <span
                            style="position: absolute; left: -2.6rem; top: 0; background: var(--primary-color); color: white; padding: 0.2rem 0.8rem; border-radius: 20px; font-weight: bold; font-size: 0.9rem;">2024</span>
                        <h4 style="margin-bottom: 0.5rem; font-size: 1.2rem;">The Seed is Planted</h4>
                        <p style="color: #6b7280;">A group of tech enthusiasts and farmers met in a small rural
                            cafe, discussing the disconnect between modern technology and traditional farming.</p>
                    </div>
                    <div style="margin-bottom: 2rem; position: relative;">
                        <span
                            style="position: absolute; left: -2.6rem; top: 0; background: var(--primary-color); color: white; padding: 0.2rem 0.8rem; border-radius: 20px; font-weight: bold; font-size: 0.9rem;">2025</span>
                        <h4 style="margin-bottom: 0.5rem; font-size: 1.2rem;">First Harvest</h4>
                        <p style="color: #6b7280;">Launched the beta version of AgriAI in 3 pilot districts. Helped
                            500 farmers increase profits by 30% through direct sales.</p>
                    </div>
                    <div style="position: relative;">
                        <span
                            style="position: absolute; left: -2.6rem; top: 0; background: var(--primary-color); color: white; padding: 0.2rem 0.8rem; border-radius: 20px; font-weight: bold; font-size: 0.9rem;">2026</span>
                        <h4 style="margin-bottom: 0.5rem; font-size: 1.2rem;">Going Global</h4>
                        <p style="color: #6b7280;">Expanded nationwide with advanced AI disease detection and
                            real-time market linkages.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Partners (Logos) -->
    <div style="padding: 4rem 0; text-align: center;">
        <div class="container">
            <p style="color: #9ca3af; font-weight: 600; letter-spacing: 1px; margin-bottom: 2rem;">TRUSTED BY
                INDUSTRY LEADERS</p>
            <div
                style="display: flex; justify-content: center; gap: 4rem; flex-wrap: wrap; opacity: 0.6; align-items: center;">
                <i class="fab fa-aws fa-3x"></i>
                <i class="fab fa-google fa-3x"></i>
                <i class="fab fa-stripe fa-3x"></i>
                <i class="fas fa-tractor fa-3x"></i>
                <i class="fas fa-leaf fa-3x"></i>
            </div>
        </div>
    </div>

    <!-- Customer Stories -->
    <div style="background-color: #f0fdf4; padding: 5rem 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="margin-bottom: 1rem;">Impact <span class="highlight">Stories</span></h2>
                <p style="color: #6b7280; max-width: 600px; margin: 0 auto;">Hear from the real people whose lives
                    have changed through AgriAI.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                <div
                    style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <div style="color: #fbbf24; margin-bottom: 1rem;"><i class="fas fa-star"></i><i
                            class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                            class="fas fa-star"></i></div>
                    <p style="font-style: italic; color: #4b5563; margin-bottom: 1.5rem;">"I used to sell my wheat
                        for whatever the local broker offered. With AgriAI, I found buyers offering 40% more. This
                        platform changed my family's life."</p>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <img src="https://ui-avatars.com/api/?name=Rajesh+K&background=random"
                            style="width: 50px; height: 50px; border-radius: 50%;" alt="User">
                        <div>
                            <h4 style="font-size: 1rem;">Rajesh Kumar</h4>
                            <p style="font-size: 0.85rem; color: #6b7280;">Farmer, Punjab</p>
                        </div>
                    </div>
                </div>

                <div
                    style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <div style="color: #fbbf24; margin-bottom: 1rem;"><i class="fas fa-star"></i><i
                            class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                            class="fas fa-star"></i></div>
                    <p style="font-style: italic; color: #4b5563; margin-bottom: 1.5rem;">"Sourcing high-quality
                        organic produce was a nightmare. Now I can see exactly where the crops come from and chat
                        directly with farmers. It's brilliant."</p>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <img src="https://ui-avatars.com/api/?name=Anita+S&background=random"
                            style="width: 50px; height: 50px; border-radius: 50%;" alt="User">
                        <div>
                            <h4 style="font-size: 1rem;">Anita Sharma</h4>
                            <p style="font-size: 0.85rem; color: #6b7280;">Organic Retailer, Mumbai</p>
                        </div>
                    </div>
                </div>

                <div
                    style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <div style="color: #fbbf24; margin-bottom: 1rem;"><i class="fas fa-star"></i><i
                            class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                            class="fas fa-star"></i></div>
                    <p style="font-style: italic; color: #4b5563; margin-bottom: 1.5rem;">"The disease detection
                        feature saved my tomato crop this year. I uploaded a photo, got a diagnosis, and treated it
                        immediately. Thank you AgriAI!"</p>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <img src="https://ui-avatars.com/api/?name=Vikram+Singh&background=random"
                            style="width: 50px; height: 50px; border-radius: 50%;" alt="User">
                        <div>
                            <h4 style="font-size: 1rem;">Vikram Singh</h4>
                            <p style="font-size: 0.85rem; color: #6b7280;">Farmer, Karnataka</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div style="padding: 5rem 0;">
        <div class="container" style="max-width: 800px;">
            <h2 style="text-align: center; margin-bottom: 3rem;">Frequently Asked <span
                    class="highlight">Questions</span></h2>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
                    <button
                        onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'block' ? 'none' : 'block'"
                        style="width: 100%; text-align: left; padding: 1.5rem; background: none; border: none; font-size: 1.1rem; font-weight: 600; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                        How does AgriAI ensure fair pricing? <i class="fas fa-chevron-down"></i>
                    </button>
                    <div style="padding: 0 1.5rem 1.5rem; display: none; color: #4b5563; line-height: 1.6;">
                        We use advanced AI algorithms that aggregate real-time data from local mandis, government
                        databases, and global markets to suggest a fair price range for every crop, empowering
                        farmers to negotiate better.
                    </div>
                </div>

                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
                    <button
                        onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'block' ? 'none' : 'block'"
                        style="width: 100%; text-align: left; padding: 1.5rem; background: none; border: none; font-size: 1.1rem; font-weight: 600; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                        Is there a fee to join? <i class="fas fa-chevron-down"></i>
                    </button>
                    <div style="padding: 0 1.5rem 1.5rem; display: none; color: #4b5563; line-height: 1.6;">
                        Registration is completely free for farmers. We believe in lowering barriers to entry. We
                        charge a small commission only on successful transactions made through the platform.
                    </div>
                </div>

                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
                    <button
                        onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'block' ? 'none' : 'block'"
                        style="width: 100%; text-align: left; padding: 1.5rem; background: none; border: none; font-size: 1.1rem; font-weight: 600; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                        How accurate is the disease detection? <i class="fas fa-chevron-down"></i>
                    </button>
                    <div style="padding: 0 1.5rem 1.5rem; display: none; color: #4b5563; line-height: 1.6;">
                        Our AI model is trained on over 100,000 plant images and currently boasts a 95% accuracy
                        rate for common crop diseases. However, we always recommend consulting with a local expert
                        for critical interventions.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Meet the Team -->
        <div style="margin-top: 5rem; text-align: center;">
            <h2 style="margin-bottom: 3rem;">Meet the <span class="highlight">Innovators</span></h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
                <!-- Team Member 1 -->
                <div
                    style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div
                        style="width: 100px; height: 100px; background: #ddd; border-radius: 50%; margin: 0 auto 1rem; overflow: hidden;">
                        <img src="images/team1.jpg"
                            onerror="this.src='https://ui-avatars.com/api/?name=Alex+R&background=random'"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <h4 style="margin-bottom: 0.5rem;">Alex Reynolds</h4>
                    <p style="color: #6b7280; font-size: 0.9rem;">Lead Developer</p>
                </div>
                <!-- Team Member 2 -->
                <div
                    style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div
                        style="width: 100px; height: 100px; background: #ddd; border-radius: 50%; margin: 0 auto 1rem; overflow: hidden;">
                        <img src="images/team2.jpg"
                            onerror="this.src='https://ui-avatars.com/api/?name=Sarah+K&background=random'"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <h4 style="margin-bottom: 0.5rem;">Sarah Khan</h4>
                    <p style="color: #6b7280; font-size: 0.9rem;">AI Specialist</p>
                </div>
                <!-- Team Member 3 -->
                <div
                    style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div
                        style="width: 100px; height: 100px; background: #ddd; border-radius: 50%; margin: 0 auto 1rem; overflow: hidden;">
                        <img src="images/team3.jpg"
                            onerror="this.src='https://ui-avatars.com/api/?name=David+M&background=random'"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <h4 style="margin-bottom: 0.5rem;">David Miller</h4>
                    <p style="color: #6b7280; font-size: 0.9rem;">Agri-economist</p>
                </div>
                <!-- Team Member 4 -->
                <div
                    style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div
                        style="width: 100px; height: 100px; background: #ddd; border-radius: 50%; margin: 0 auto 1rem; overflow: hidden;">
                        <img src="images/team4.jpg"
                            onerror="this.src='https://ui-avatars.com/api/?name=Emily+W&background=random'"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <h4 style="margin-bottom: 0.5rem;">Emily Wong</h4>
                    <p style="color: #6b7280; font-size: 0.9rem;">UX Designer</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div
            style="margin-top: 5rem; background: var(--primary-color); padding: 3rem; border-radius: 1rem; text-align: center; color: white;">
            <h2 style="color: white; margin-bottom: 1rem;">Ready to Transform Your Farming?</h2>
            <p style="font-size: 1.1rem; margin-bottom: 2rem; opacity: 0.9;">Join thousands of farmers and buyers
                already trading smarter.</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <a href="register.php" class="btn" style="background: white; color: var(--primary-color);">Get
                    Started</a>
                <a href="contact.php" class="btn btn-outline" style="border-color: white; color: white;">Contact
                    Us</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/main_footer.php'; ?>