<?php
include 'db_connect.php';

$sql = "DESCRIBE cart";
$result = $conn->query($sql);

if ($result) {
    echo "Table 'cart' columns:\n";
    echo str_pad("Field", 20) . str_pad("Type", 20) . str_pad("Null", 10) . str_pad("Key", 10) . str_pad("Default", 10) . "\n";
    echo str_repeat("-", 70) . "\n";
    while ($row = $result->fetch_assoc()) {
        echo str_pad($row['Field'], 20) . 
             str_pad($row['Type'], 20) . 
             str_pad($row['Null'], 10) . 
             str_pad($row['Key'], 10) . 
             str_pad($row['Default'] ?? 'USER_DEFINED', 20) . 
             "\n";
    }
} else {
    echo "Error describing table: " . $conn->error;
}
?>
