<?php
// chatbot_api.php
header('Content-Type: application/json');

// Read JSON input from the frontend fetch request
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

if (!isset($input['message']) || trim($input['message']) === '') {
    echo json_encode(['response' => 'Hello! How can I help you with E-Agriculture today? 🌾']);
    exit;
}

$raw_message = trim($input['message']);
$clean_message = strtolower(trim(preg_replace('/[^\w\s-]/u', '', $raw_message)));

// Default response
$response = "I'm here to help with all your agriculture and platform questions! You can ask about our crops, seeds, chemicals, ordering process, account details, or weather forecasts. 🌾";

// 1. Fetch FAQs from Database (with fallback to kb_data.json)
$faqs = [];
include_once 'db_connect.php';

if (isset($conn) && !$conn->connect_error) {
    $res = $conn->query("SELECT id, category, question, answer, keywords FROM chatbot_faqs ORDER BY id ASC");
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $kw = json_decode($row['keywords'], true);
            if (!is_array($kw)) {
                $kw = array_map('trim', explode(',', $row['keywords']));
            }
            $faqs[] = [
                'id' => $row['id'],
                'category' => $row['category'],
                'question' => $row['question'],
                'answer' => $row['answer'],
                'keywords' => $kw
            ];
        }
    }
}

// Fallback to kb_data.json if database was empty or not accessible
if (empty($faqs)) {
    $kb_file = __DIR__ . '/kb_data.json';
    if (file_exists($kb_file)) {
        $kb_data = json_decode(file_get_contents($kb_file), true);
        if (is_array($kb_data)) {
            $faqs = $kb_data;
        }
    }
}

$best_match = null;
$highest_score = 0;

$user_words = array_filter(explode(' ', $clean_message), function($w) {
    return strlen($w) > 2;
});

// 2. Matching Algorithm
foreach ($faqs as $faq) {
    $q_clean = strtolower(trim(preg_replace('/[^\w\s-]/u', '', $faq['question'])));
    
    // Exact question match -> Top Priority (Score 1000)
    if ($q_clean === $clean_message || strpos($clean_message, $q_clean) !== false || strpos($q_clean, $clean_message) !== false) {
        $score = 1000 + strlen($q_clean);
        if ($score > $highest_score) {
            $highest_score = $score;
            $best_match = $faq['answer'];
        }
    }
    
    // Keyword phrase matching
    if (!empty($faq['keywords']) && is_array($faq['keywords'])) {
        foreach ($faq['keywords'] as $keyword) {
            $kw_clean = strtolower(trim(preg_replace('/[^\w\s-]/u', '', $keyword)));
            if (empty($kw_clean)) continue;
            
            // Exact phrase match in message
            if (strpos($clean_message, $kw_clean) !== false) {
                // Higher score for longer, more specific keywords
                $score = 200 + (strlen($kw_clean) * 5) + (count(explode(' ', $kw_clean)) * 20);
                if ($score > $highest_score) {
                    $highest_score = $score;
                    $best_match = $faq['answer'];
                }
            }
        }
    }

    // Word Overlap Scoring
    $faq_words = array_filter(explode(' ', $q_clean), function($w) {
        return strlen($w) > 2;
    });
    
    if (!empty($user_words) && !empty($faq_words)) {
        $common = array_intersect($user_words, $faq_words);
        $overlap_count = count($common);
        if ($overlap_count >= 2) {
            $overlap_score = ($overlap_count * 30) / max(count($user_words), count($faq_words)) * 100;
            if ($overlap_score > 60 && $overlap_score > $highest_score) {
                $highest_score = $overlap_score;
                $best_match = $faq['answer'];
            }
        }
    }
}

if ($best_match !== null && $highest_score >= 100) {
    $response = $best_match;
} else {
    // 3. Fallback intent matching for common greetings / dynamic queries
    if (preg_match('/\b(hi|hello|hey|greetings|namaste)\b/', $clean_message)) {
        $response = "Hello! I am the AgriAI Agriculture Expert. How can I assist you with your crops, soil health, products, or orders today? 🌾";
    } elseif (strpos($clean_message, 'weather') !== false || strpos($clean_message, 'rain') !== false || strpos($clean_message, 'temperature') !== false) {
        $response = "You can check live weather forecasts and smart irrigation advisories for your region directly on our [Weather Advisory](weather.php) page! It provides real-time conditions and customized farming advice.";
    } elseif (strpos($clean_message, 'price') !== false || strpos($clean_message, 'market rate') !== false || strpos($clean_message, 'mandi') !== false) {
        if (isset($conn) && !$conn->connect_error) {
            $price_info = "";
            $sql = "SELECT name, AVG(price) as avg_price, price_unit FROM crops GROUP BY name LIMIT 5";
            $p_res = $conn->query($sql);
            if ($p_res && $p_res->num_rows > 0) {
                $price_info = "Here are some current average market prices from our listings:\n";
                while ($row = $p_res->fetch_assoc()) {
                    $price_info .= "- " . $row['name'] . ": $" . number_format($row['avg_price'], 2) . " per " . ($row['price_unit'] ?? 'kg') . "\n";
                }
                $price_info .= "\nFor detailed AI forecasts, please visit the [Market Trends](market_trends.php) page!";
                $response = $price_info;
            } else {
                $response = "Our AI price prediction tool uses market data to forecast crop prices. Check out our [Market Trends](market_trends.php) page for live rates!";
            }
        } else {
            $response = "Our AI price prediction tool uses market data to forecast crop prices. Check out our [Market Trends](market_trends.php) page for live rates!";
        }
    } elseif (strpos($clean_message, 'contact') !== false || strpos($clean_message, 'support') !== false || strpos($clean_message, 'phone') !== false || strpos($clean_message, 'email') !== false) {
        $response = "You can use the contact/help section provided on the website to communicate with the administrator, email us at support@eagriculture.com, or call our helpline.";
    } elseif ($best_match !== null) {
        $response = $best_match;
    }
}

// Quick simulated AI delay
usleep(300000); // 0.3s for fast and responsive UI

// Return JSON output
echo json_encode([
    'response' => $response,
    'session_id' => isset($input['session_id']) ? $input['session_id'] : 'unknown'
]);
?>