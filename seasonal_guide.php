<?php
session_start();
include 'db_connect.php';

$page_title = 'Seasonal Crop Guide - AgriAI';

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

    /* Fix scrolling issue */
    html, body {
        overflow-y: auto !important;
        height: auto !important;
        display: block !important;
    }
    
    .season-tab {
        padding: 1rem 2rem;
        cursor: pointer;
        opacity: 0.7;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .season-tab.active {
        opacity: 1;
        border-bottom-color: #16a34a;
        color: #16a34a;
    }
    
    .season-content {
        display: none;
        animation: fadeIn 0.5s;
    }
    
    .season-content.active {
        display: block;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .crop-pill {
        background: #f0fdf4;
        color: #166534;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid #bbf7d0;
    }
</style>
";

include 'includes/main_header.php';
?>

<!-- Hero Section -->
<header class="page-header" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=1932&auto=format&fit=crop') center/cover; padding: 6rem 0; text-align: center; color: white;">
    <div class="container">
        <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 1rem;">Seasonal Crop Guide</h1>
        <p style="font-size: 1.25rem; opacity: 0.9; max-width: 700px; margin: 0 auto;">
            Maximize your yield by planting the right crop at the right time. Powered by AI insights.
        </p>
    </div>
</header>

<!-- Intro -->
<section class="section" style="padding: 4rem 0;">
    <div class="container text-center">
        <h2 style="margin-bottom: 1rem;">Understand the <span class="highlight">Seasons</span></h2>
        <p style="color: #6b7280; max-width: 800px; margin: 0 auto;">
            India has three distinct cropping seasons: Kharif, Rabi, and Zaid. Choosing crops aligned with these cycles ensures better growth, fewer pest attacks, and higher market demand.
        </p>
    </div>
</section>

<!-- Seasonal Tabs -->
<section class="section" style="background-color: #f9fafb; padding-top: 0;">
    <div class="container">
        <!-- Tabs Header -->
        <div style="display: flex; justify-content: center; gap: 2rem; border-bottom: 1px solid #e5e7eb; margin-bottom: 3rem;">
            <div class="season-tab active" onclick="switchSeason('kharif')">
                <i class="fas fa-cloud-showers-heavy" style="margin-right: 8px;"></i> Kharif (Monsoon)
            </div>
            <div class="season-tab" onclick="switchSeason('rabi')">
                <i class="fas fa-snowflake" style="margin-right: 8px;"></i> Rabi (Winter)
            </div>
            <div class="season-tab" onclick="switchSeason('zaid')">
                <i class="fas fa-sun" style="margin-right: 8px;"></i> Zaid (Summer)
            </div>
        </div>

        <!-- Kharif Content -->
        <div id="kharif" class="season-content active">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
                <div>
                    <h3 style="color: #16a34a; margin-bottom: 1rem;">Kharif Season (July - October)</h3>
                    <p style="margin-bottom: 1.5rem; line-height: 1.7; color: #4b5563;">
                        Crops sown at the beginning of the monsoon season and harvested around September-October. These crops require significant water and hot, humid conditions.
                    </p>
                    <ul style="list-style: none; padding: 0; margin-bottom: 2rem; display: grid; gap: 0.5rem;">
                        <li style="display: flex; gap: 10px;"><i class="fas fa-check" style="color: #16a34a;"></i> <strong>Sowing:</strong> June - July</li>
                        <li style="display: flex; gap: 10px;"><i class="fas fa-check" style="color: #16a34a;"></i> <strong>Harvesting:</strong> September - October</li>
                        <li style="display: flex; gap: 10px;"><i class="fas fa-check" style="color: #16a34a;"></i> <strong>Water Need:</strong> High</li>
                    </ul>
                    <h4 style="margin-bottom: 1rem;">Recommended Crops:</h4>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span class="crop-pill"><i class="fas fa-seedling"></i> Rice (Paddy)</span>
                        <span class="crop-pill"><i class="fas fa-seedling"></i> Maize</span>
                        <span class="crop-pill"><i class="fas fa-seedling"></i> Sorghum</span>
                        <span class="crop-pill"><i class="fas fa-seedling"></i> Pearl Millet</span>
                        <span class="crop-pill"><i class="fas fa-seedling"></i> Cotton</span>
                        <span class="crop-pill"><i class="fas fa-seedling"></i> Soybean</span>
                        <span class="crop-pill"><i class="fas fa-seedling"></i> Groundnut</span>
                    </div>
                </div>
                <div>
                    <img src="https://images.unsplash.com/photo-1586771107445-d3ca888129ff?q=80&w=1772&auto=format&fit=crop" style="width: 100%; border-radius: 1rem; box-shadow: 0 10px 20px rgba(0,0,0,0.1);" alt="Kharif Crops">
                </div>
            </div>
        </div>

        <!-- Rabi Content -->
        <div id="rabi" class="season-content">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
                <div>
                    <h3 style="color: #2563eb; margin-bottom: 1rem;">Rabi Season (October - March)</h3>
                    <p style="margin-bottom: 1.5rem; line-height: 1.7; color: #4b5563;">
                        Crops sown in winter and harvested in spring. These crops generally require irrigation but less water than Kharif crops, thriving in cool, dry climates.
                    </p>
                    <ul style="list-style: none; padding: 0; margin-bottom: 2rem; display: grid; gap: 0.5rem;">
                        <li style="display: flex; gap: 10px;"><i class="fas fa-check" style="color: #2563eb;"></i> <strong>Sowing:</strong> October - December</li>
                        <li style="display: flex; gap: 10px;"><i class="fas fa-check" style="color: #2563eb;"></i> <strong>Harvesting:</strong> April - May</li>
                        <li style="display: flex; gap: 10px;"><i class="fas fa-check" style="color: #2563eb;"></i> <strong>Water Need:</strong> Moderate</li>
                    </ul>
                    <h4 style="margin-bottom: 1rem;">Recommended Crops:</h4>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span class="crop-pill" style="color: #1e40af; background: #dbeafe; border-color: #93c5fd;"><i class="fas fa-seedling"></i> Wheat</span>
                        <span class="crop-pill" style="color: #1e40af; background: #dbeafe; border-color: #93c5fd;"><i class="fas fa-seedling"></i> Barley</span>
                        <span class="crop-pill" style="color: #1e40af; background: #dbeafe; border-color: #93c5fd;"><i class="fas fa-seedling"></i> Oats</span>
                        <span class="crop-pill" style="color: #1e40af; background: #dbeafe; border-color: #93c5fd;"><i class="fas fa-seedling"></i> Chickpea</span>
                        <span class="crop-pill" style="color: #1e40af; background: #dbeafe; border-color: #93c5fd;"><i class="fas fa-seedling"></i> Mustard</span>
                        <span class="crop-pill" style="color: #1e40af; background: #dbeafe; border-color: #93c5fd;"><i class="fas fa-seedling"></i> Peas</span>
                    </div>
                </div>
                <div>
                    <img src="https://images.unsplash.com/photo-1574943320219-553eb213f72d?q=80&w=1661&auto=format&fit=crop" style="width: 100%; border-radius: 1rem; box-shadow: 0 10px 20px rgba(0,0,0,0.1);" alt="Rabi Crops">
                </div>
            </div>
        </div>

        <!-- Zaid Content -->
        <div id="zaid" class="season-content">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
                <div>
                    <h3 style="color: #d97706; margin-bottom: 1rem;">Zaid Season (March - June)</h3>
                    <p style="margin-bottom: 1.5rem; line-height: 1.7; color: #4b5563;">
                        A short season during the summer months between Kharif and Rabi. Crops grown are typically quick-maturing fruits, vegetables, and fodder.
                    </p>
                    <ul style="list-style: none; padding: 0; margin-bottom: 2rem; display: grid; gap: 0.5rem;">
                        <li style="display: flex; gap: 10px;"><i class="fas fa-check" style="color: #d97706;"></i> <strong>Sowing:</strong> March - May</li>
                        <li style="display: flex; gap: 10px;"><i class="fas fa-check" style="color: #d97706;"></i> <strong>Harvesting:</strong> June - July</li>
                        <li style="display: flex; gap: 10px;"><i class="fas fa-check" style="color: #d97706;"></i> <strong>Water Need:</strong> Irrigation Dependent</li>
                    </ul>
                    <h4 style="margin-bottom: 1rem;">Recommended Crops:</h4>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span class="crop-pill" style="color: #92400e; background: #fef3c7; border-color: #fcd34d;"><i class="fas fa-seedling"></i> Watermelon</span>
                        <span class="crop-pill" style="color: #92400e; background: #fef3c7; border-color: #fcd34d;"><i class="fas fa-seedling"></i> Muskmelon</span>
                        <span class="crop-pill" style="color: #92400e; background: #fef3c7; border-color: #fcd34d;"><i class="fas fa-seedling"></i> Cucumber</span>
                        <span class="crop-pill" style="color: #92400e; background: #fef3c7; border-color: #fcd34d;"><i class="fas fa-seedling"></i> Vegetables</span>
                        <span class="crop-pill" style="color: #92400e; background: #fef3c7; border-color: #fcd34d;"><i class="fas fa-seedling"></i> Fodder Crops</span>
                    </div>
                </div>
                <div>
                    <img src="https://images.unsplash.com/photo-1598155523122-38423bb4d693?q=80&w=1676&auto=format&fit=crop" style="width: 100%; border-radius: 1rem; box-shadow: 0 10px 20px rgba(0,0,0,0.1);" alt="Zaid Crops">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- AI Insights Banner -->
<section class="section">
    <div class="container">
        <div style="background: #111827; border-radius: 1rem; padding: 3rem; color: white; display: flex; align-items: center; flex-wrap: wrap; gap: 2rem;">
            <div style="flex: 2; min-width: 300px;">
                <h2 style="color: white; margin-bottom: 1rem;">How <span class="highlight">AI</span> Helps You Plan</h2>
                <p style="opacity: 0.9; margin-bottom: 1.5rem;">
                    Don't guess—know. Our AI models process 10+ years of weather and yield data to predict the outcome of your harvest before you sow.
                </p>
                <div style="display: flex; gap: 2rem;">
                    <div>
                        <h3 style="color: #16a34a; font-size: 2rem; margin-bottom: 0;">20%</h3>
                        <span style="font-size: 0.9rem; opacity: 0.8;">Higher Yield</span>
                    </div>
                    <div>
                        <h3 style="color: #16a34a; font-size: 2rem; margin-bottom: 0;">15%</h3>
                        <span style="font-size: 0.9rem; opacity: 0.8;">Cost Saving</span>
                    </div>
                </div>
            </div>
            <div style="flex: 1; text-align: center;">
                <i class="fas fa-brain" style="font-size: 8rem; color: #374151;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Live Available Crops (Dynamic) -->
<section class="section" style="padding-top: 0;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Currently Available in Market</h2>
            <a href="shop_crops.php" class="btn btn-outline">View All Listings</a>
        </div>
        
        <div class="crop-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 2rem;">
            <?php
            // Fetch random 4 crops to show variety
            $sql = "SELECT * FROM crops ORDER BY RAND() LIMIT 4";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $img = !empty($row["image_path"]) ? "images/" . $row["image_path"] : 'images/default-crop.png';
                    // Quick fix for image path if it already contains images/ or http
                    if (strpos($row["image_path"], 'images/') !== false || strpos($row["image_path"], 'http') !== false) {
                        $img = $row["image_path"];
                    }
                    
                    echo '
                    <div class="crop-card" style="background: white; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                        <div style="height: 200px; overflow: hidden;">
                            <img src="'.$img.'" alt="'.$row["name"].'" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="padding: 1.5rem;">
                            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem;">'.$row["name"].'</h3>
                            <div style="color: var(--primary); font-weight: 700; font-size: 1.2rem; margin-bottom: 1rem;">$'.$row["price"].' <span style="font-size: 0.9rem; font-weight: 400; color: #6b7280;">/ '.$row["price_unit"].'</span></div>
                            <a href="login.php" class="btn btn-sm btn-outline" style="width: 100%; text-align: center; display: block;">View Details</a>
                        </div>
                    </div>';
               }
            } else {
                echo '<p>No crops listed at the moment.</p>';
            }
            ?>
        </div>
    </div>
</section>

<?php include 'includes/main_footer.php'; ?>

<script>
    function switchSeason(seasonId) {
        // Tabs
        document.querySelectorAll('.season-tab').forEach(t => t.classList.remove('active'));
        event.target.classList.add('active');
        
        // Content
        document.querySelectorAll('.season-content').forEach(c => c.classList.remove('active'));
        document.getElementById(seasonId).classList.add('active');
    }
</script>
