<?php
include 'db_connect.php';
$result = $conn->query("SHOW COLUMNS FROM crops");
while($row = $result->fetch_assoc()){
    echo $row['Field'] . " - " . $row['Type'] . "<br>";
}
?>
