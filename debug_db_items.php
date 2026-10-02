<?php
ob_start();
include 'db_connect.php';

echo "<h2>Order Items Data</h2>";
// Try to select the requested columns. identifying table name as 'order_items' (common convention)
$sql = "SELECT order_id, crop_id, quantity, subtotal, price_per_unit FROM order_items LIMIT 20";

// If table is different, checking tables
$tables = $conn->query("SHOW TABLES");
$table_exists = false;
while($row = $tables->fetch_array()) {
    if ($row[0] == 'order_items') {
        $table_exists = true;
        break;
    }
}

if ($table_exists) {
    echo "<table border='1'><tr><th>Order ID</th><th>Crop ID</th><th>Quantity</th><th>Subtotal (Total Price)</th><th>Unit Price</th></tr>";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['order_id'] . "</td>";
            echo "<td>" . $row['crop_id'] . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            echo "<td>" . $row['subtotal'] . "</td>";
             echo "<td>" . $row['price_per_unit'] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "Error: " . $conn->error;
    }
    echo "</table>";
} else {
    echo "Table 'order_items' not found. Listing tables:<br>";
    $tables = $conn->query("SHOW TABLES");
    while($row = $tables->fetch_array()) {
        echo $row[0] . "<br>";
    }
}
$content = ob_get_clean();
file_put_contents('db_result.txt', $content);
echo "Output written to db_result.txt";
?>
