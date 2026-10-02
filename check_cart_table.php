<?php
include 'db_connect.php';
$sql = "DESCRIBE cart";
$result = $conn->query($sql);
$file = fopen("cart_schema_output.txt", "w");
if ($result) {
    while($row = $result->fetch_assoc()) {
        fwrite($file, $row['Field'] . " | " . $row['Type'] . "\n");
    }
} else {
    fwrite($file, "Error: " . $conn->error);
}
fclose($file);
echo "Done";
?>
