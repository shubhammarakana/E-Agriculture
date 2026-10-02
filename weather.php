<?php
session_start();
include 'db_connect.php';

$page_title = 'Weather Advisory - AgriAI';

// Fix Services Menu Visibility & Scroll
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
    
    /* Force scroll fix */
    html, body {
        overflow-y: auto !important;
        height: auto !important;
        display: block !important;
    }

    .weather-card-main {
        background: linear-gradient(135deg, #3b82f6, #06b6d4);
        color: white;
        border-radius: 1.5rem;
        padding: 3rem;
        box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.15), 0 10px 10px -5px rgba(59, 130, 246, 0.1);
        position: relative;
        overflow: hidden;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .weather-card-main::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
    }

    .forecast-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
        border: 1px solid #f3f4f6;
        transition: transform 0.2s;
    }

    .forecast-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .alert-box {
        background: #fff1f2;
        border-left: 4px solid #f43f5e;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
        display: none; /* Hidden by default until JS checks logic */
    }

    .loading-spinner {
        display: inline-block;
        width: 50px;
        height: 50px;
        border: 3px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
";

include 'includes/main_header.php';
?>

<!-- Weather Header -->
<div class="weather-page-header" style="padding: 4rem 0; text-align: center;">
    <div class="container">
        <span id="location-badge" style="background: #eff6ff; color: #3b82f6; padding: 0.5rem 1rem; border-radius: 2rem; font-weight: 600; font-size: 0.9rem;">
            <i class="fas fa-location-arrow"></i> Locating...
        </span>
        <h1 style="font-size: 2.5rem; margin-top: 1rem; margin-bottom: 0.5rem;">Weather Advisory</h1>
        <p style="color: #6b7280;">Real-time weather insights to protect your crops and optimize your yield.</p>
    </div>
</div>

<!-- Main Weather Display -->
<section class="section" style="padding-top: 0;">
    <div class="container">
        
        <!-- Alert Box (Dynamic) -->
        <div id="weather-alert" class="alert-box">
            <div style="display: flex; align-items: start; gap: 1rem;">
                <i class="fas fa-exclamation-triangle" style="color: #f43f5e; font-size: 1.5rem; margin-top: 0.2rem;"></i>
                <div>
                    <h3 style="color: #9f1239; margin-bottom: 0.5rem;" id="alert-title">Weather Alert</h3>
                    <p style="color: #be123c; margin: 0;" id="alert-msg">High temperatures expected.</p>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 3rem;">
            <!-- Current Conditions -->
            <div class="weather-card-main" id="current-weather-card">
                <!-- Loading State -->
                <div id="loading-weather" style="width: 100%; text-align: center;">
                    <div class="loading-spinner"></div>
                    <p style="margin-top: 1rem;">Fetching live weather data...</p>
                </div>

                <!-- Live Data (Hidden initially) -->
                <div id="weather-content" style="display: none; width: 100%; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 2rem;">
                    <div>
                        <div style="font-size: 1.2rem; opacity: 0.9; margin-bottom: 0.5rem;">Current Conditions</div>
                        <div style="font-size: 5rem; font-weight: 700; line-height: 1;" id="current-temp">--°C</div>
                        <div style="font-size: 1.5rem; font-weight: 500; margin-bottom: 1rem;" id="current-desc">--</div>
                        <div style="display: flex; gap: 2rem; font-size: 1rem;">
                            <span><i class="fas fa-wind"></i> <span id="wind-speed">--</span> km/h</span>
                            <span><i class="fas fa-tint"></i> <span id="humidity">--</span>% Humidity</span>
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <i id="weather-icon-main" class="fas fa-sun" style="font-size: 8rem; color: rgba(255,255,255,0.9); filter: drop-shadow(0 0 20px rgba(255,255,0,0.5));"></i>
                    </div>
                </div>
            </div>

            <!-- Farming Insight -->
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 1.5rem; padding: 2rem; display: flex; flex-direction: column; justify-content: center;">
                <h3 style="color: #166534; margin-bottom: 1rem;"><i class="fas fa-seedling"></i> AI Farming Tip</h3>
                <p id="farming-tip" style="color: #15803d; margin-bottom: 1.5rem; line-height: 1.6;">
                    Analyzing weather conditions...
                </p>
                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid #dcfce7; color: #166534; font-size: 0.9rem;">
                    <strong>Next Action:</strong> <span id="next-action">Wait for analysis</span>
                </div>
            </div>
        </div>

        <!-- 7 Day Forecast -->
        <h2 style="margin-bottom: 1.5rem;">7-Day Forecast</h2>
        <div id="forecast-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 1rem;">
            <!-- Forecast items injected by JS -->
             <div class="forecast-card"><div class="loading-spinner" style="width: 30px; height: 30px; border-width: 2px; border-color: #3b82f6; border-top-color: transparent;"></div></div>
        </div>
    </div>
</section>

<?php include 'includes/main_footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        initWeather();
    });

    function initWeather() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(fetchWeatherData, handleGeoError);
        } else {
            handleGeoError();
        }
    }

    function handleGeoError() {
        // Default to 'Central India' (approx lat/long for Nagpur) if denied or unavailable
        console.warn("Geolocation denied or unavailable. Using default location.");
        document.getElementById('location-badge').innerHTML = '<i class="fas fa-map-marker-alt"></i> Default: Central India';
        fetchWeatherData({ coords: { latitude: 21.1458, longitude: 79.0882 } });
    }

    async function fetchWeatherData(position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        
        // Update badge if real location
        if(position.coords.accuracy) {
             document.getElementById('location-badge').innerHTML = `<i class="fas fa-map-marker-alt"></i> Lat: ${lat.toFixed(2)}, Long: ${lon.toFixed(2)}`;
        }

        const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max&timezone=auto`;

        try {
            const response = await fetch(url);
            const data = await response.json();
            
            updateCurrentWeather(data.current);
            updateForecast(data.daily);
            generateFarmingTip(data.current);
            
        } catch (error) {
            console.error("Error fetching weather:", error);
            document.getElementById('loading-weather').innerHTML = '<p style="color:white;">Failed to load weather data. Please try again later.</p>';
        }
    }

    // WMO Weather Code Mapping to FontAwesome Icons & Desc
    function getWeatherInfo(code) {
        const map = {
            0: { icon: 'fa-sun', desc: 'Clear Sky', color: '#f59e0b' },
            1: { icon: 'fa-cloud-sun', desc: 'Mainly Clear', color: '#f59e0b' },
            2: { icon: 'fa-cloud-sun', desc: 'Partly Cloudy', color: '#6b7280' },
            3: { icon: 'fa-cloud', desc: 'Overcast', color: '#6b7280' },
            45: { icon: 'fa-smog', desc: 'Fog', color: '#9ca3af' },
            48: { icon: 'fa-smog', desc: 'Rime Fog', color: '#9ca3af' },
            51: { icon: 'fa-cloud-rain', desc: 'Light Drizzle', color: '#3b82f6' },
            53: { icon: 'fa-cloud-rain', desc: 'Mod. Drizzle', color: '#3b82f6' },
            55: { icon: 'fa-cloud-showers-heavy', desc: 'Dense Drizzle', color: '#2563eb' },
            61: { icon: 'fa-cloud-rain', desc: 'Slight Rain', color: '#3b82f6' },
            63: { icon: 'fa-cloud-rain', desc: 'Moderate Rain', color: '#3b82f6' },
            65: { icon: 'fa-cloud-showers-heavy', desc: 'Heavy Rain', color: '#1d4ed8' },
            71: { icon: 'fa-snowflake', desc: 'Slight Snow', color: '#93c5fd' },
            73: { icon: 'fa-snowflake', desc: 'Mod. Snow', color: '#93c5fd' },
            75: { icon: 'fa-snowflake', desc: 'Heavy Snow', color: '#60a5fa' },
            80: { icon: 'fa-cloud-showers-heavy', desc: 'Rain Showers', color: '#3b82f6' },
            81: { icon: 'fa-cloud-showers-heavy', desc: 'Mod. Showers', color: '#2563eb' },
            82: { icon: 'fa-cloud-showers-heavy', desc: 'Violent Showers', color: '#1e40af' },
            95: { icon: 'fa-bolt', desc: 'Thunderstorm', color: '#7c3aed' },
            96: { icon: 'fa-bolt', desc: 'Thunderstorm', color: '#7c3aed' },
            99: { icon: 'fa-bolt', desc: 'Heavy T-Storm', color: '#7c3aed' },
        };
        return map[code] || { icon: 'fa-question-circle', desc: 'Unknown', color: '#9ca3af' };
    }

    function updateCurrentWeather(current) {
        const info = getWeatherInfo(current.weather_code);
        
        document.getElementById('current-temp').innerText = `${Math.round(current.temperature_2m)}°C`;
        document.getElementById('current-desc').innerText = info.desc;
        document.getElementById('wind-speed').innerText = current.wind_speed_10m;
        document.getElementById('humidity').innerText = current.relative_humidity_2m;
        
        const iconEl = document.getElementById('weather-icon-main');
        iconEl.className = `fas ${info.icon}`;
        
        document.getElementById('loading-weather').style.display = 'none';
        document.getElementById('weather-content').style.display = 'flex';
    }

    function updateForecast(daily) {
        const container = document.getElementById('forecast-grid');
        container.innerHTML = '';
        
        // Display 7 days
        for(let i=0; i < 7; i++) {
            if(!daily.time[i]) break;
            
            // Fix Date Parsing: Manually parse YYYY-MM-DD to get the correct day regardless of timezone
            const parts = daily.time[i].split('-');
            const date = new Date(parts[0], parts[1] - 1, parts[2]); // Month is 0-indexed
            const dayName = date.toLocaleDateString('en-US', { weekday: 'short' });
            
            const info = getWeatherInfo(daily.weather_code[i]);
            const maxTemp = Math.round(daily.temperature_2m_max[i]);
            const minTemp = Math.round(daily.temperature_2m_min[i]);
            const rainProb = daily.precipitation_probability_max ? daily.precipitation_probability_max[i] : 0;
            
            let rainBadge = '';
            if(rainProb > 30) {
                rainBadge = `<div style="font-size: 0.75rem; color: #2563eb; font-weight: 600; margin-top:2px;"><i class="fas fa-umbrella"></i> ${rainProb}%</div>`;
            } else {
                 rainBadge = `<div style="font-size: 0.75rem; color: #9ca3af; margin-top:2px;">DRY</div>`;
            }

            const card = document.createElement('div');
            card.className = 'forecast-card';
            card.innerHTML = `
                <div style="font-weight: 600; color: #6b7280; margin-bottom: 0.5rem; font-size: 0.9rem;">${dayName}</div>
                <div style="font-size: 0.8rem; color: #9ca3af; margin-bottom: 0.5rem;">${parts[1]}/${parts[2]}</div>
                <i class="fas ${info.icon}" style="font-size: 1.8rem; color: ${info.color}; margin-bottom: 0.5rem;"></i>
                <div style="font-size: 1.1rem; font-weight: 700; color: #111827;">${maxTemp}° <span style="font-size:0.8rem; color:#9ca3af; font-weight:400;">/ ${minTemp}°</span></div>
                <div style="font-size: 0.8rem; color: #4b5563; margin-top: 0.3rem; height: 35px; overflow:hidden; line-height:1.2;">${info.desc}</div>
                ${rainBadge}
            `;
            container.appendChild(card);
        }
    }

    function generateFarmingTip(current) {
        let tip = "Conditions are stable. Good for general field work.";
        let action = "Monitor crops";
        
        // Simple logic for farming advice
        if (current.relative_humidity_2m > 80 || current.rain > 0) {
            tip = "High humidity detected. Risk of fungal diseases is elevated. Avoid spraying now.";
            action = "Check drainage";
            // Show alert
            const alertBox = document.getElementById('weather-alert');
            alertBox.style.display = 'block';
            document.getElementById('alert-title').innerText = "High Moisture Alert";
            document.getElementById('alert-msg').innerText = "High humidity levels may lead to fungal infection. Inspect fungicide requirements.";
        } else if (current.temperature_2m > 30) {
            tip = "High temperatures detected. Evaporation rates are high. Irrigate during cooler hours.";
            action = "Irrigate (Evening)";
        } else if (current.wind_speed_10m > 15) {
             tip = "High wind speeds detected. Do not apply pesticide sprays as drift may occur.";
             action = "Delay Spraying";
        }

        document.getElementById('farming-tip').innerText = tip;
        document.getElementById('next-action').innerText = action;
    }
</script>
