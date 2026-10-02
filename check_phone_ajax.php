<?php
include 'db_connect.php';
header('Content-Type: application/json');

if (isset($_POST['phone'])) {
    $phone = $_POST['phone'];
    $sql = "SELECT id, fullname, email FROM users WHERE phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'status' => 'success', 
            'exists' => true, 
            'message' => 'User exists',
            'fullname' => $row['fullname'],
            'email' => $row['email']
        ]);
    } else {
        echo json_encode(['status' => 'success', 'exists' => false, 'message' => 'New user']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No phone provided']);
}
?>
