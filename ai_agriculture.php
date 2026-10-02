<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<?php
$page_title = 'AI in Agriculture - AgriAI';
include 'includes/main_header.php';
?>

<style>
    /* Custom styles for AI page */
    .ai-hero {
        background: linear-gradient(rgba(17, 24, 39, 0.8), rgba(17, 24, 39, 0.9)), url('https://images.unsplash.com/photo-1574943320219-553eb213f72d?q=80&w=1920&auto=format&fit=crop');
        background-position: center;
        background-size: cover;
        padding: 6rem 0;
        color: white;
        text-align: center;
    }

    .tech-card {
        background: white;
        border-radius: 1rem;
        padding: 2.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
        border: 1px solid #f3f4f6;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .tech-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .icon-box {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1.5rem;
    }

    .data-point {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
</style>

<body>
    <!-- Hero Section -->
    <section class="ai-hero">
        <div class="container">
            <h1 style="font-size: 3.5rem; margin-bottom: 1rem;">AI in <span class="highlight">Agriculture</span></h1>
            <p style="font-size: 1.25rem; opacity: 0.9; max-width: 800px; margin: 0 auto; line-height: 1.6;">
                The core engine driving the AgriAI marketplace. We combine machine learning, computer vision, and
                predictive analytics to empower farmers and buyers.
            </p>
        </div>
    </section>

    <!-- The 3 Pillars -->
    <section class="section" style="padding: 5rem 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 4rem;">
                <h2 style="font-size: 2.25rem; margin-bottom: 1rem;">Core <span class="highlight">Intelligence</span>
                </h2>
                <p style="color: #6b7280; font-size: 1.1rem;">Three powerful technologies working in harmony.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 3rem;">

                <!-- Crop Intelligence -->
                <div class="tech-card">
                    <div class="icon-box" style="background: #ecfccb; color: #65a30d;">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Smart Crop Management</h3>
                    <p style="color: #6b7280; line-height: 1.7; margin-bottom: 2rem; flex-grow: 1;">
                        Our algorithms analyze soil health data, local climate patterns, and historical harvest records
                        to recommend the ideal crops for your specific plot.
                    </p>
                    <div class="data-point">
                        <i class="fas fa-check" style="color: #65a30d;"></i>
                        <span style="font-weight: 500; font-size: 0.95rem;">Automated Soil Analysis</span>
                    </div>
                    <div class="data-point">
                        <i class="fas fa-check" style="color: #65a30d;"></i>
                        <span style="font-weight: 500; font-size: 0.95rem;">Yield Forecasting</span>
                    </div>
                </div>

                <!-- Computer Vision -->
                <div class="tech-card">
                    <div class="icon-box" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Visual Disease Detection</h3>
                    <p style="color: #6b7280; line-height: 1.7; margin-bottom: 2rem; flex-grow: 1;">
                        Simply snap a photo of a suspicious leaf. Our deep learning models (CNNs) instantly identify
                        blight, rust, or pests with 98% accuracy.
                    </p>
                    <div class="data-point">
                        <i class="fas fa-check" style="color: #0284c7;"></i>
                        <span style="font-weight: 500; font-size: 0.95rem;">Instant Diagnosis</span>
                    </div>
                    <div class="data-point">
                        <i class="fas fa-check" style="color: #0284c7;"></i>
                        <span style="font-weight: 500; font-size: 0.95rem;">Treatment Suggestions</span>
                    </div>
                </div>

                <!-- Predictive Analytics -->
                <div class="tech-card">
                    <div class="icon-box" style="background: #ffedd5; color: #ea580c;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Market & Price Prediction</h3>
                    <p style="color: #6b7280; line-height: 1.7; margin-bottom: 2rem; flex-grow: 1;">
                        Stop guessing when to sell. We process millions of data points from regional mandis to forecast
                        price trends for the upcoming weeks.
                    </p>
                    <div class="data-point">
                        <i class="fas fa-check" style="color: #ea580c;"></i>
                        <span style="font-weight: 500; font-size: 0.95rem;">Demand Forecasting</span>
                    </div>
                    <div class="data-point">
                        <i class="fas fa-check" style="color: #ea580c;"></i>
                        <span style="font-weight: 500; font-size: 0.95rem;">Best-Time-To-Sell Alerts</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- How It Works (Process Flow) -->
    <section class="section" style="background-color: white;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 4rem;">
                <h2 style="font-size: 2.25rem; margin-bottom: 1rem;">How the <span class="highlight">Intelligence</span>
                    Flows</h2>
                <p style="color: #6b7280; font-size: 1.1rem;">From raw data to actionable insights in milliseconds.</p>
            </div>

            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem; position: relative;">
                <!-- Step 1 -->
                <div style="flex: 1; min-width: 250px; text-align: center; position: relative; z-index: 2;">
                    <div
                        style="width: 80px; height: 80px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 2px solid #e5e7eb;">
                        <i class="fas fa-database" style="color: #4b5563; font-size: 1.75rem;"></i>
                    </div>
                    <h3 style="margin-bottom: 1rem;">1. Data Collection</h3>
                    <p style="color: #6b7280; font-size: 0.95rem;">
                        We aggregate data from user inputs, IoT sensors, satellite imagery, and regional agricultural
                        databases.
                    </p>
                </div>

                <!-- Arrow -->
                <div
                    style="display: flex; align-items: center; justify-content: center; color: #d1d5db; font-size: 1.5rem;">
                    <i class="fas fa-chevron-right hidden-mobile"></i>
                </div>

                <!-- Step 2 -->
                <div style="flex: 1; min-width: 250px; text-align: center; position: relative; z-index: 2;">
                    <div
                        style="width: 80px; height: 80px; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 2px solid #bae6fd;">
                        <i class="fas fa-cogs" style="color: #0284c7; font-size: 1.75rem;"></i>
                    </div>
                    <h3 style="margin-bottom: 1rem;">2. AI Processing</h3>
                    <p style="color: #6b7280; font-size: 0.95rem;">
                        Our deep learning models (CNN & RNN) process the data, identifying patterns invisible to the
                        human eye.
                    </p>
                </div>

                <!-- Arrow -->
                <div
                    style="display: flex; align-items: center; justify-content: center; color: #d1d5db; font-size: 1.5rem;">
                    <i class="fas fa-chevron-right hidden-mobile"></i>
                </div>

                <!-- Step 3 -->
                <div style="flex: 1; min-width: 250px; text-align: center; position: relative; z-index: 2;">
                    <div
                        style="width: 80px; height: 80px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; border: 2px solid #86efac;">
                        <i class="fas fa-lightbulb" style="color: #16a34a; font-size: 1.75rem;"></i>
                    </div>
                    <h3 style="margin-bottom: 1rem;">3. Smart Insight</h3>
                    <p style="color: #6b7280; font-size: 0.95rem;">
                        You receive clear, actionable recommendations: "Spray X fungicide today" or "Sell Wheat next
                        Tuesday".
                    </p>
                </div>
            </div>

            <style>
                @media (max-width: 768px) {
                    .hidden-mobile {
                        display: none;
                    }
                }
            </style>
        </div>
    </section>

    <!-- Future Roadmap -->
    <section class="section"
        style="background: linear-gradient(to right, #1f2937, #111827); color: white; padding: 5rem 0;">
        <div class="container">
            <div style="display: flex; gap: 4rem; align-items: center; flex-wrap: wrap-reverse;">
                <div style="flex: 1; min-width: 300px;">
                    <h2 style="font-size: 2.25rem; margin-bottom: 1.5rem;">The Road <span
                            style="color: var(--secondary-color);">Ahead</span></h2>
                    <p style="opacity: 0.9; margin-bottom: 2rem; line-height: 1.8;">
                        We are just getting started. Our R&D team is working on the next generation of agricultural
                        technology to further automate and optimize farming.
                    </p>

                    <div class="roadmap-item" style="display: flex; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary-color); font-size: 1.5rem; width: 30px;"><i
                                class="fas fa-drone"></i></div>
                        <div>
                            <h4 style="margin-bottom: 0.25rem;">Drone Integration</h4>
                            <p style="font-size: 0.9rem; opacity: 0.7;">Automated aerial surveillance for large-scale
                                pest mapping.</p>
                        </div>
                    </div>

                    <div class="roadmap-item" style="display: flex; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary-color); font-size: 1.5rem; width: 30px;"><i
                                class="fas fa-wifi"></i></div>
                        <div>
                            <h4 style="margin-bottom: 0.25rem;">IoT Soil Sensors</h4>
                            <p style="font-size: 0.9rem; opacity: 0.7;">Direct hardware connection for real-time
                                moisture and pH tracking.</p>
                        </div>
                    </div>

                    <div class="roadmap-item" style="display: flex; gap: 1.5rem;">
                        <div style="color: var(--secondary-color); font-size: 1.5rem; width: 30px;"><i
                                class="fas fa-comments"></i></div>
                        <div>
                            <h4 style="margin-bottom: 0.25rem;">Voice-Activated Assistant</h4>
                            <p style="font-size: 0.9rem; opacity: 0.7;">"Hey AgriAI, what's today's market price?" -
                                Hands-free operation.</p>
                        </div>
                    </div>
                </div>

                <div style="flex: 1; min-width: 300px; display: flex; justify-content: center;">
                    <img src="https://images.unsplash.com/photo-1586771107445-d3ca888129ff?q=80&w=1772&auto=format&fit=crop"
                        style="border-radius: 1rem; box-shadow: 0 0 30px rgba(234, 179, 8, 0.1);" alt="Future Tech">
                </div>
            </div>
        </div>
    </section>

    <!-- FAQs -->
    <section class="section" style="padding: 5rem 0;">
        <div class="container" style="max-width: 800px;">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="font-size: 2.25rem;">Common <span class="highlight">Questions</span></h2>
                <p style="color: #6b7280;">Everything you need to know about our AI systems.</p>
            </div>

            <div class="faq-container" style="display: flex; flex-direction: column; gap: 1rem;">
                <details
                    style="background: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); cursor: pointer;">
                    <summary
                        style="font-weight: 600; font-size: 1.1rem; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                        How accurate are the price predictions?
                        <i class="fas fa-chevron-down" style="color: var(--primary-color);"></i>
                    </summary>
                    <p style="color: #6b7280; margin-top: 1rem; line-height: 1.6;">
                        Our models currently operate with an 85-90% accuracy rate for major crops. Accuracy improves
                        constantly as more data from local mandis is fed into the system.
                    </p>
                </details>

                <details
                    style="background: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); cursor: pointer;">
                    <summary
                        style="font-weight: 600; font-size: 1.1rem; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                        Do I need a high-end phone to use disease detection?
                        <i class="fas fa-chevron-down" style="color: var(--primary-color);"></i>
                    </summary>
                    <p style="color: #6b7280; margin-top: 1rem; line-height: 1.6;">
                        No! All the heavy processing happens on our cloud servers. You just need a basic smartphone with
                        a camera and internet connection.
                    </p>
                </details>

                <details
                    style="background: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); cursor: pointer;">
                    <summary
                        style="font-weight: 600; font-size: 1.1rem; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                        Is my farm data kept private?
                        <i class="fas fa-chevron-down" style="color: var(--primary-color);"></i>
                    </summary>
                    <p style="color: #6b7280; margin-top: 1rem; line-height: 1.6;">
                        Absolutely. We use end-to-end encryption. Your specific yield and soil data is yours alone and
                        is only used to generate your personal recommendations.
                    </p>
                </details>
            </div>
        </div>
    </section>
    <!-- Tech Deep Dive -->
    <section class="section" style="background-color: #111827; color: white; padding: 5rem 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 4rem;">
                <h2 style="font-size: 2.25rem; margin-bottom: 1rem; color: white;">Under the <span class="highlight"
                        style="color: var(--secondary-color);">Hood</span></h2>
                <p style="opacity: 0.8; font-size: 1.1rem;">For the tech-savvy: The models powering our insights.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                <div
                    style="background: rgba(255,255,255,0.05); padding: 2rem; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.1);">
                    <h3 style="margin-bottom: 1rem; color: var(--secondary-color);">Convolutional Neural Networks (CNN)
                    </h3>
                    <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.6;">
                        Used for our Disease Detection. We trained a ResNet-50 architecture on over 50,000 labeled leaf
                        images to classify 15+ distinct plant diseases with high precision.
                    </p>
                </div>
                <div
                    style="background: rgba(255,255,255,0.05); padding: 2rem; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.1);">
                    <h3 style="margin-bottom: 1rem; color: var(--secondary-color);">XGBoost Regressors</h3>
                    <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.6;">
                        The core of our Price Prediction engine. It handles tabular data (weather, historical prices,
                        transport costs) better than deep learning for this specific tabular task.
                    </p>
                </div>
                <div
                    style="background: rgba(255,255,255,0.05); padding: 2rem; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.1);">
                    <h3 style="margin-bottom: 1rem; color: var(--secondary-color);">Time-Series Forecasting</h3>
                    <p style="opacity: 0.8; font-size: 0.9rem; line-height: 1.6;">
                        We utilize ARIMA and Prophet models to detect seasonality and long-term trends in crop demand,
                        helping farmers plan seasons ahead.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sustainability Impact -->
    <section class="section" style="padding: 5rem 0;">
        <div class="container">
            <div style="display: flex; gap: 4rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 300px;">
                    <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=1932&auto=format&fit=crop"
                        style="width: 100%; border-radius: 1rem; box-shadow: 0 20px 25px -5px rgba(22, 163, 74, 0.2);"
                        alt="Sustainable Field">
                </div>
                <div style="flex: 1; min-width: 300px;">
                    <h2 style="font-size: 2.25rem; margin-bottom: 1.5rem;">AI for a <span class="highlight">Greener
                            Earth</span></h2>
                    <p style="color: #6b7280; font-size: 1.1rem; margin-bottom: 2rem; line-height: 1.8;">
                        Precision agriculture isn't just about profit; it's about planet. Our AI helps reduce the
                        environmental footprint of farming.
                    </p>

                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 1.5rem;">
                        <li style="display: flex; gap: 1rem;">
                            <div
                                style="min-width: 40px; height: 40px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                                <i class="fas fa-tint"></i>
                            </div>
                            <div>
                                <h4 style="margin-bottom: 0.25rem;">Water Conservation</h4>
                                <p style="font-size: 0.95rem; color: #6b7280;">Precise weather alerts mean farmers
                                    irrigate only when necessary, saving millions of liters.</p>
                            </div>
                        </li>
                        <li style="display: flex; gap: 1rem;">
                            <div
                                style="min-width: 40px; height: 40px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                                <i class="fas fa-flask"></i>
                            </div>
                            <div>
                                <h4 style="margin-bottom: 0.25rem;">Reduced Chemical Use</h4>
                                <p style="font-size: 0.95rem; color: #6b7280;">Targeted disease detection allows for
                                    spot-treatment rather than blanket spraying of pesticides.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Data Ecosystem -->
    <section class="section" style="background-color: white;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 4rem;">
                <h2 style="font-size: 2.25rem; margin-bottom: 1rem;">Powered by <span class="highlight">Big Data</span>
                </h2>
                <p style="color: #6b7280; font-size: 1.1rem;">Our AI doesn't yield results in a vacuum. It processes
                    terabytes of global and local data.</p>
            </div>

            <div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 2rem;">
                <div style="text-align: center; max-width: 200px;">
                    <div style="font-size: 2.5rem; color: #3b82f6; margin-bottom: 1rem;"><i
                            class="fas fa-satellite"></i></div>
                    <h4 style="margin-bottom: 0.5rem;">Satellite Imagery</h4>
                    <p style="font-size: 0.9rem; color: #6b7280;">Sentinel-2 & Landsat data for vegetation indices
                        (NDVI).</p>
                </div>
                <div style="text-align: center; max-width: 200px;">
                    <div style="font-size: 2.5rem; color: #eab308; margin-bottom: 1rem;"><i class="fas fa-sun"></i>
                    </div>
                    <h4 style="margin-bottom: 0.5rem;">Weather Stations</h4>
                    <p style="font-size: 0.9rem; color: #6b7280;">Hyper-local updates from 5,000+ networked
                        meteorological stations.</p>
                </div>
                <div style="text-align: center; max-width: 200px;">
                    <div style="font-size: 2.5rem; color: #10b981; margin-bottom: 1rem;"><i class="fas fa-users"></i>
                    </div>
                    <h4 style="margin-bottom: 0.5rem;">Community Reports</h4>
                    <p style="font-size: 0.9rem; color: #6b7280;">Real-time pest sightings reported by our 50k+ farmer
                        network.</p>
                </div>
                <div style="text-align: center; max-width: 200px;">
                    <div style="font-size: 2.5rem; color: #6366f1; margin-bottom: 1rem;"><i
                            class="fas fa-dollar-sign"></i></div>
                    <h4 style="margin-bottom: 0.5rem;">Mandi Prices</h4>
                    <p style="font-size: 0.9rem; color: #6b7280;">Daily price feeds from government-regulated wholesale
                        markets.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Comparison Table -->
    <section class="section" style="background-color: #f8fafc; padding: 5rem 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 4rem;">
                <h2 style="font-size: 2.25rem; margin-bottom: 1rem;">Traditional vs. <span
                        class="highlight">AgriAI</span></h2>
                <p style="color: #6b7280; font-size: 1.1rem;">See the difference intelligent tools make.</p>
            </div>

            <div style="overflow-x: auto;">
                <table
                    style="width: 100%; border-collapse: collapse; background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <thead>
                        <tr style="background: #1f2937; color: white; text-align: left;">
                            <th style="padding: 1.5rem; width: 40%;">Feature</th>
                            <th style="padding: 1.5rem; width: 30%; opacity: 0.8;">Traditional Farming</th>
                            <th style="padding: 1.5rem; width: 30%; background: var(--primary-color);">With AgriAI <i
                                    class="fas fa-check-circle" style="margin-left: 0.5rem;"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 1.5rem; font-weight: 600; color: #374151;">Disease Diagnosis</td>
                            <td style="padding: 1.5rem; color: #6b7280;">Manual inspection, often too late.</td>
                            <td style="padding: 1.5rem; color: #16a34a; font-weight: 600; background: #ecfccb50;">
                                Instant visual ID via Phone.</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 1.5rem; font-weight: 600; color: #374151;">Selling Decision</td>
                            <td style="padding: 1.5rem; color: #6b7280;">Based on immediate cash flow need.</td>
                            <td style="padding: 1.5rem; color: #16a34a; font-weight: 600; background: #ecfccb50;">
                                Data-driven "Best Time to Sell".</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 1.5rem; font-weight: 600; color: #374151;">Input Usage</td>
                            <td style="padding: 1.5rem; color: #6b7280;">Generic application schedules.</td>
                            <td style="padding: 1.5rem; color: #16a34a; font-weight: 600; background: #ecfccb50;">
                                Precise, need-based application.</td>
                        </tr>
                        <tr>
                            <td style="padding: 1.5rem; font-weight: 600; color: #374151;">Market Access</td>
                            <td style="padding: 1.5rem; color: #6b7280;">Local middlemen only.</td>
                            <td style="padding: 1.5rem; color: #16a34a; font-weight: 600; background: #ecfccb50;">Direct
                                national buyer network.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="section" style="padding: 5rem 0;">
        <div class="container">
            <div style="display: flex; gap: 4rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 300px;">
                    <h2 style="font-size: 2.25rem; margin-bottom: 1.5rem;">Why AI Matters?</h2>

                    <div style="margin-bottom: 2rem;">
                        <h4 style="margin-bottom: 0.5rem; color: var(--primary-color);">1. Reduces Optimism Bias</h4>
                        <p style="color: #6b7280;">Farmers often hope for better prices. AI provides objective,
                            data-backed reality checks.</p>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <h4 style="margin-bottom: 0.5rem; color: var(--primary-color);">2. Minimizes Crop Waste</h4>
                        <p style="color: #6b7280;">Early disease detection saves tons of produce that would otherwise be
                            destroyed.</p>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <h4 style="margin-bottom: 0.5rem; color: var(--primary-color);">3. Empowers Negotiation</h4>
                        <p style="color: #6b7280;">Knowing the real market value gives farmers leverage when negotiating
                            with buyers.</p>
                    </div>
                </div>
                <div style="flex: 1; min-width: 300px;">
                    <img src="https://images.unsplash.com/photo-1625246333195-78d9c38ad449?q=80&w=1770&auto=format&fit=crop"
                        style="width: 100%; border-radius: 1rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);"
                        alt="Farmer using AI app">
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="section" style="padding: 5rem 0; text-align: center; background: #111827; color: white;">
        <div class="container">
            <h2 style="margin-bottom: 1rem; color: white;">See the AI in Action</h2>
            <p style="opacity: 0.8; margin-bottom: 3rem; max-width: 600px; margin: 0 auto;">
                Register today to access the Disease Detector and Price Prediction tools for free.
            </p>
            <a href="register.php" class="btn" style="background: var(--primary-color); color: white; border: none;">Try
                AI Tools Now</a>
        </div>
    </section>

    <?php include 'includes/main_footer.php'; ?>
</body>

</html>