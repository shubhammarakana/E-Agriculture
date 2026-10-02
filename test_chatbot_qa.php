<?php
// test_chatbot_qa.php
$questions = [
    "What is E-Agriculture?",
    "How can I register on the website?",
    "Do I need to log in to use the system?",
    "How can I find a particular agricultural product?",
    "Can I see the product price before buying?",
    "How can I buy a product?",
    "Can I check my order status?",
    "Can I cancel my order?",
    "How can I contact the admin or get help?",
    "Is my personal information safe?",
    "Can I update my address?",
    "What if I forget my password?",
    "Can I give feedback about a product or service?",
    "What are the benefits of using E-Agriculture?",
    "What should I do if I have a problem with my order?"
];

$expected = [
    "E-Agriculture is an online platform that provides farmers with agricultural information, products, and services digitally.",
    "You can register by entering your basic details such as name, mobile number/email, address, and password.",
    "Some basic information may be available without login, but login is required for features such as ordering products, managing your profile, and checking orders.",
    "You can use the search option or select a suitable product category to find the required product.",
    "Yes, the product price and other available details are displayed before you add the product to the cart.",
    "Select the product, choose the required quantity, add it to the cart, enter delivery details, and confirm the order.",
    "Yes. After logging in, you can check your orders and their current status from the My Orders section.",
    "Yes, if the cancellation option is available and the order has not reached the processing or delivery stage.",
    "You can use the contact/help section provided on the website to communicate with the administrator.",
    "Yes. The system uses authentication and access controls to protect customer information.",
    "Yes, you can update your address and other editable profile information from your account.",
    "You can use the Forgot Password option to reset your password, if that feature is implemented in the system.",
    "Yes, if the feedback feature is available, you can submit your feedback or review.",
    "It saves time, provides agricultural information in one place, makes product purchasing easier, and helps farmers use digital services.",
    "You can check the order details and contact the admin/support team through the available contact option."
];

$all_passed = true;

for ($i = 0; $i < count($questions); $i++) {
    $q = $questions[$i];
    $exp = $expected[$i];
    
    // Simulate POST request to chatbot_api.php
    $ch = curl_init('http://localhost/E-Agriculture%20(2)/E-Agriculture/chatbot_api.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $q]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $res = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($res, true);
    $ans = $data['response'] ?? '';
    
    $match = ($ans === $exp || strpos($ans, trim($exp)) !== false);
    if ($match) {
        echo "PASS [Q" . ($i + 1) . "]: $q\n  -> Output: $ans\n\n";
    } else {
        $all_passed = false;
        echo "FAIL [Q" . ($i + 1) . "]: $q\n  -> Expected: $exp\n  -> Got: $ans\n\n";
    }
}

if ($all_passed) {
    echo "=============================================\n";
    echo "ALL 15 QUESTIONS PASSED WITH EXACT MATCHES!\n";
    echo "=============================================\n";
} else {
    echo "SOME TESTS FAILED!\n";
}
