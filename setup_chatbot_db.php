<?php
// setup_chatbot_db.php
require_once 'db_connect.php';

// 1. Create table `chatbot_faqs`
$sql_table = "CREATE TABLE IF NOT EXISTS `chatbot_faqs` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `category` VARCHAR(100) NOT NULL DEFAULT 'General',
    `question` TEXT NOT NULL,
    `answer` TEXT NOT NULL,
    `keywords` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if (!$conn->query($sql_table)) {
    die("Error creating table: " . $conn->error . "\n");
}
echo "Table `chatbot_faqs` created/verified successfully.\n";

// 2. New 15 Core Questions & Answers requested
$new_faqs = [
    [
        'category' => 'General Information',
        'question' => 'What is E-Agriculture?',
        'answer' => 'E-Agriculture is an online platform that provides farmers with agricultural information, products, and services digitally.',
        'keywords' => ['what is e-agriculture', 'what is e agriculture', 'e-agriculture', 'e agriculture', 'about e-agriculture', 'about the platform', 'what is this website', 'what is this platform']
    ],
    [
        'category' => 'Account & Registration',
        'question' => 'How can I register on the website?',
        'answer' => 'You can register by entering your basic details such as name, mobile number/email, address, and password.',
        'keywords' => ['how can i register on the website', 'how can i register', 'how to register', 'register on the website', 'create an account', 'create account', 'sign up', 'signup', 'registration']
    ],
    [
        'category' => 'Account & Login',
        'question' => 'Do I need to log in to use the system?',
        'answer' => 'Some basic information may be available without login, but login is required for features such as ordering products, managing your profile, and checking orders.',
        'keywords' => ['do i need to log in to use the system', 'do i need to log in', 'do i need to login', 'is login required', 'need to log in', 'need login', 'log in required', 'login to use']
    ],
    [
        'category' => 'Product Search & Browsing',
        'question' => 'How can I find a particular agricultural product?',
        'answer' => 'You can use the search option or select a suitable product category to find the required product.',
        'keywords' => ['how can i find a particular agricultural product', 'how can i find a product', 'find a particular product', 'find product', 'search product', 'how to find product', 'find crops', 'find seeds', 'find chemicals']
    ],
    [
        'category' => 'Pricing & Details',
        'question' => 'Can I see the product price before buying?',
        'answer' => 'Yes, the product price and other available details are displayed before you add the product to the cart.',
        'keywords' => ['can i see the product price before buying', 'can i see product price', 'see product price before buying', 'product price before buying', 'see price before buy', 'check price before buying', 'view price']
    ],
    [
        'category' => 'Ordering & Purchase',
        'question' => 'How can I buy a product?',
        'answer' => 'Select the product, choose the required quantity, add it to the cart, enter delivery details, and confirm the order.',
        'keywords' => ['how can i buy a product', 'how to buy a product', 'how can i buy', 'how to buy', 'how to purchase', 'purchase product', 'buy product', 'how to order', 'place an order']
    ],
    [
        'category' => 'Order Management',
        'question' => 'Can I check my order status?',
        'answer' => 'Yes. After logging in, you can check your orders and their current status from the My Orders section.',
        'keywords' => ['can i check my order status', 'check my order status', 'check order status', 'track my order', 'track order', 'order status', 'my orders status', 'where is my order']
    ],
    [
        'category' => 'Order Management',
        'question' => 'Can I cancel my order?',
        'answer' => 'Yes, if the cancellation option is available and the order has not reached the processing or delivery stage.',
        'keywords' => ['can i cancel my order', 'cancel my order', 'how to cancel order', 'cancel order', 'order cancellation', 'cancellation']
    ],
    [
        'category' => 'Customer Support & Help',
        'question' => 'How can I contact the admin or get help?',
        'answer' => 'You can use the contact/help section provided on the website to communicate with the administrator.',
        'keywords' => ['how can i contact the admin or get help', 'how can i contact the admin', 'contact the admin', 'get help', 'contact admin', 'contact administrator', 'help section', 'contact support', 'customer support']
    ],
    [
        'category' => 'Security & Privacy',
        'question' => 'Is my personal information safe?',
        'answer' => 'Yes. The system uses authentication and access controls to protect customer information.',
        'keywords' => ['is my personal information safe', 'is personal information safe', 'my personal information safe', 'is data safe', 'is my data safe', 'privacy', 'security of data', 'is it secure']
    ],
    [
        'category' => 'Profile & Account',
        'question' => 'Can I update my address?',
        'answer' => 'Yes, you can update your address and other editable profile information from your account.',
        'keywords' => ['can i update my address', 'update my address', 'update address', 'change address', 'edit address', 'modify address', 'change my delivery address']
    ],
    [
        'category' => 'Account & Login',
        'question' => 'What if I forget my password?',
        'answer' => 'You can use the Forgot Password option to reset your password, if that feature is implemented in the system.',
        'keywords' => ['what if i forget my password', 'forget my password', 'forgot password', 'reset password', 'reset my password', 'lost password', 'change password']
    ],
    [
        'category' => 'Feedback & Reviews',
        'question' => 'Can I give feedback about a product or service?',
        'answer' => 'Yes, if the feedback feature is available, you can submit your feedback or review.',
        'keywords' => ['can i give feedback about a product or service', 'can i give feedback', 'give feedback about a product', 'give feedback', 'submit feedback', 'write review', 'submit review', 'leave feedback']
    ],
    [
        'category' => 'General Information',
        'question' => 'What are the benefits of using E-Agriculture?',
        'answer' => 'It saves time, provides agricultural information in one place, makes product purchasing easier, and helps farmers use digital services.',
        'keywords' => ['what are the benefits of using e-agriculture', 'benefits of using e-agriculture', 'benefits of e-agriculture', 'advantages of e-agriculture', 'why use e-agriculture', 'benefits of platform', 'advantages of platform']
    ],
    [
        'category' => 'Order Support',
        'question' => 'What should I do if I have a problem with my order?',
        'answer' => 'You can check the order details and contact the admin/support team through the available contact option.',
        'keywords' => ['what should i do if i have a problem with my order', 'problem with my order', 'issue with my order', 'order problem', 'order issue', 'wrong item received', 'damaged product']
    ]
];

// 3. Insert or update the FAQs into chatbot_faqs
$stmt = $conn->prepare("INSERT INTO `chatbot_faqs` (`category`, `question`, `answer`, `keywords`) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `answer` = VALUES(`answer`), `keywords` = VALUES(`keywords`)");

foreach ($new_faqs as $faq) {
    $keywords_json = json_encode($faq['keywords'], JSON_UNESCAPED_UNICODE);
    
    // Check if question exists
    $chk = $conn->prepare("SELECT id FROM chatbot_faqs WHERE question = ? LIMIT 1");
    $chk->bind_param("s", $faq['question']);
    $chk->execute();
    $res = $chk->get_result();
    
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $upd = $conn->prepare("UPDATE chatbot_faqs SET category = ?, answer = ?, keywords = ? WHERE id = ?");
        $upd->bind_param("sssi", $faq['category'], $faq['answer'], $keywords_json, $row['id']);
        $upd->execute();
        echo "Updated FAQ: " . $faq['question'] . "\n";
    } else {
        $ins = $conn->prepare("INSERT INTO chatbot_faqs (category, question, answer, keywords) VALUES (?, ?, ?, ?)");
        $ins->bind_param("ssss", $faq['category'], $faq['question'], $faq['answer'], $keywords_json);
        $ins->execute();
        echo "Inserted FAQ: " . $faq['question'] . "\n";
    }
}

// 4. Also import any existing FAQs from kb_data.json into the DB if not already present
$kb_path = __DIR__ . '/kb_data.json';
if (file_exists($kb_path)) {
    $existing_kb = json_decode(file_get_contents($kb_path), true);
    if (is_array($existing_kb)) {
        foreach ($existing_kb as $item) {
            $q = $item['question'] ?? '';
            $a = $item['answer'] ?? '';
            $cat = $item['category'] ?? 'General';
            $kw = json_encode($item['keywords'] ?? [], JSON_UNESCAPED_UNICODE);
            if ($q && $a) {
                $chk = $conn->prepare("SELECT id FROM chatbot_faqs WHERE question = ? LIMIT 1");
                $chk->bind_param("s", $q);
                $chk->execute();
                $res = $chk->get_result();
                if ($res->num_rows == 0) {
                    $ins = $conn->prepare("INSERT INTO chatbot_faqs (category, question, answer, keywords) VALUES (?, ?, ?, ?)");
                    $ins->bind_param("ssss", $cat, $q, $a, $kw);
                    $ins->execute();
                }
            }
        }
    }
}

// 5. Export combined database FAQs back to kb_data.json to keep both in perfect sync
$all_faqs = [];
$res = $conn->query("SELECT id, category, question, answer, keywords FROM chatbot_faqs ORDER BY id ASC");
while ($row = $res->fetch_assoc()) {
    $kw_arr = json_decode($row['keywords'], true);
    if (!is_array($kw_arr)) {
        $kw_arr = array_map('trim', explode(',', $row['keywords']));
    }
    $all_faqs[] = [
        'id' => (int)$row['id'],
        'category' => $row['category'],
        'question' => $row['question'],
        'answer' => $row['answer'],
        'keywords' => $kw_arr
    ];
}

file_put_contents($kb_path, json_encode($all_faqs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "Successfully synced " . count($all_faqs) . " FAQs to MySQL database and kb_data.json!\n";
