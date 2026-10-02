<?php
session_start();
include 'db_connect.php';

$page_title = 'Market Price Trends - AgriAI';

// Fix Services Menu Visibility & Scroll
$extra_css = "
<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css\" integrity=\"sha512-aa0xk8zn4mr0KK31VfI1hM4G1pAgn1f1W4/X9+f9/p/8/r/5/9/6/5/9/6/5\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\" />
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
    
    /* Force scroll fix */
    html, body {
        overflow-y: auto !important;
        height: auto !important;
        display: block !important;
    }

    .trend-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: transform 0.2s;
    }

    .trend-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .trend-up {
        color: #16a34a;
        font-weight: 600;
        background: #f0fdf4;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 0.85rem;
    }

    .trend-down {
        color: #dc2626;
        font-weight: 600;
        background: #fef2f2;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 0.85rem;
    }

    .chart-container {
        background: white;
        padding: 2rem;
        border-radius: 1.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        border: 1px solid #f3f4f6;
        margin-bottom: 3rem;
        height: 400px;
    }
</style>
";

include 'includes/main_header.php';
?>

<!-- Header -->
<header style="padding: 4rem 0; text-align: center; background: #f9fafb;">
    <div class="container">
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Market Price <span class="highlight">Trends</span></h1>
        <p style="color: #6b7280; max-width: 600px; margin: 0 auto;">
            Track historical price movements and future predictions powered by AI. Make informed selling decisions.
        </p>
    </div>
</header>

<!-- Main Chart Section -->
<section class="section">
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem;">
            <h2><i class="fas fa-chart-line" style="color: #2563eb;"></i> Price History & Forecast</h2>
            <select style="padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid #d1d5db; font-size: 1rem; outline: none;" id="cropSelector" onchange="updateChart()">
                <option value="wheat">Wheat</option>
                <option value="rice">Rice</option>
                <option value="maize">Maize</option>
                <option value="soybean">Soybean</option>
            </select>
        </div>

        <div class="chart-container">
            <canvas id="priceChart"></canvas>
        </div>

        <!-- Live Market Snapshot -->
        <h2 style="margin-bottom: 1.5rem;">Live Market Snapshot</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            <?php
            // Calculate real average prices from listings
            $sql = "SELECT name, AVG(price) as avg_price, price_unit, COUNT(*) as listings FROM crops GROUP BY name LIMIT 6";
            $result = $conn->query($sql);

            if($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // Simulate trend for demo purposes since no history table
                    $isUp = rand(0, 1) == 1;
                    $trendVal = rand(1, 5) . '.' . rand(0, 9);
                    
                    echo '
                    <div class="trend-card">
                        <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom: 1rem;">
                            <h3 style="margin:0; font-size: 1.25rem;">'.htmlspecialchars($row['name']).'</h3>
                            '.($isUp ? '<span class="trend-up"><i class="fas fa-arrow-up"></i> +'.$trendVal.'%</span>' : '<span class="trend-down"><i class="fas fa-arrow-down"></i> -'.$trendVal.'%</span>').'
                        </div>
                        <div style="font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem;">
                            $'.number_format($row['avg_price'], 2).'
                        </div>
                        <div style="color: #6b7280; font-size: 0.9rem;">
                            per '.$row['price_unit'].' &bull; '.$row['listings'].' active listings
                        </div>
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f3f4f6; color: #16a34a; font-size: 0.85rem; font-weight:500;">
                            <i class="fas fa-robot"></i> AI Prediction: Buy Signal
                        </div>
                    </div>';
                }
            } else {
                 // Fallback cards
                 $crops = [['Wheat', 240, 'ton'], ['Rice', 450, 'ton'], ['Corn', 180, 'ton']];
                 foreach($crops as $c) {
                     echo '
                    <div class="trend-card">
                        <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom: 1rem;">
                            <h3 style="margin:0; font-size: 1.25rem;">'.$c[0].'</h3>
                            <span class="trend-up"><i class="fas fa-arrow-up"></i> +2.4%</span>
                        </div>
                        <div style="font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem;">
                            $'.$c[1].'
                        </div>
                        <div style="color: #6b7280; font-size: 0.9rem;">
                            per '.$c[2].' &bull; Mock listings
                        </div>
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f3f4f6; color: #16a34a; font-size: 0.85rem; font-weight:500;">
                            <i class="fas fa-robot"></i> AI Prediction: Stable
                        </div>
                    </div>';
                 }
            }
            ?>
        </div>
    </div>
</section>

<!-- AI Analysis Banner -->
<section class="section" style="background: #1e293b; color: white; padding: 4rem 0;">
    <div class="container" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 3rem;">
        <div style="flex: 1; min-width: 300px;">
            <h2 style="color: white; margin-bottom: 1rem;">Unlock Premium Insights</h2>
            <p style="color: #cbd5e1; margin-bottom: 2rem; font-size: 1.1rem; line-height: 1.7;">
                Our advanced AI models analyze global supply chains, weather impact, and local demand to forecast prices up to 30 days in advance.
            </p>
            <div style="display: flex; gap: 1rem;">
                <button class="btn btn-primary">Join as Pro Farmer</button>
                <button class="btn btn-outline" style="color: white; border-color: #cbd5e1;">Learn More</button>
            </div>
        </div>
        <div style="flex: 1; min-width: 300px; text-align: center;">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/stock-market-analysis-illustration-download-in-svg-png-gif-file-formats--business-finance-graph-chart-investment-growth-trading-pack-illustrations-3790757.png?f=webp" alt="Analysis" style="width: 100%; max-width: 400px; filter: drop-shadow(0 0 20px rgba(255,255,255,0.1));">
        </div>
    </div>
</section>

<?php include 'includes/main_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('priceChart').getContext('2d');
    
    // Mock Data for Charts
    const cropData = {
        wheat: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            prices: [220, 230, 225, 240, 255, 250, 265],
            forecast: [null, null, null, null, null, null, 265, 275, 280] // Continued forecast
        },
        rice: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            prices: [420, 415, 430, 440, 450, 460, 455],
             forecast: [null, null, null, null, null, null, 455, 450, 445]
        },
        maize: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            prices: [180, 185, 190, 188, 195, 200, 210],
             forecast: [null, null, null, null, null, null, 210, 215, 220]
        },
        soybean: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            prices: [500, 510, 505, 520, 530, 525, 540],
             forecast: [null, null, null, null, null, null, 540, 550, 560]
        }
    };

    let myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug (Pro)', 'Sep (Pro)'],
            datasets: [{
                label: 'Historical Price ($/ton)',
                data: [220, 230, 225, 240, 255, 250, 265, null, null],
                borderColor: '#2563eb',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(37, 99, 235, 0.1)'
            }, {
                label: 'AI Forecast',
                data: [null, null, null, null, null, null, 265, 275, 280],
                borderColor: '#16a34a',
                borderDash: [5, 5],
                tension: 0.4,
                pointStyle: 'star',
                pointRadius: 6,
                backgroundColor: 'rgba(22, 163, 74, 0.1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });

    function updateChart() {
        const selected = document.getElementById('cropSelector').value;
        const data = cropData[selected];
        
        // Update Actual Data
        myChart.data.datasets[0].data = [...data.prices, null, null];
        
        // Update Forecast Data (linking the last known point)
        const lastPrice = data.prices[data.prices.length - 1];
        // Hacky forecast structure just for visual
        let forecastArr = Array(6).fill(null);
        forecastArr.push(lastPrice);
        // Simple linear projection for mock
        forecastArr.push(lastPrice * 1.05); 
        forecastArr.push(lastPrice * 1.08);
        
        myChart.data.datasets[1].data = forecastArr;
        
        myChart.update();
    }
</script>
