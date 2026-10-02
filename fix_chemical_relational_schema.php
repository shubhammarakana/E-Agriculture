<?php
// fix_chemical_relational_schema.php
include 'db_connect.php';

echo "Starting Relational Tables Migration...<br>";

// Tables that use 'chemical_id' instead of 'product_id'
$tables_to_fix = [
    'chemical_cart',
    'chemical_wishlist',
    'chemical_reviews'
];

foreach ($tables_to_fix as $table) {
    echo "Processing table: $table...<br>";
    $res = $conn->query("SHOW COLUMNS FROM `$table` LIKE 'chemical_id'");
    if ($res && $res->num_rows > 0) {
        // Check if product_id already exists (might have been added but not renamed)
        $check_product = $conn->query("SHOW COLUMNS FROM `$table` LIKE 'product_id'");
        if ($check_product && $check_product->num_rows == 0) {
            $sql = "ALTER TABLE `$table` CHANGE `chemical_id` `product_id` INT(6) UNSIGNED NOT NULL";
            if ($conn->query($sql)) {
                echo "Successfully renamed `chemical_id` to `product_id` in `$table`.<br>";
            } else {
                echo "Error renaming column in `$table`: " . $conn->error . "<br>";
            }
        } else {
            echo "Column `product_id` already exists in `$table`.<br>";
        }
    } else {
        echo "Column `chemical_id` not found in `$table`.<br>";
    }
}

echo "Migration Complete.";
?>