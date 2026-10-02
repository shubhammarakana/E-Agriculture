<?php
// migrate_chemicals_schema.php
include 'db_connect.php';

echo "Starting Chemicals Module Migration...<br>";

// 1. Chemicals Table Cleanup
$renames_chem = [
    'image_url' => ['new_name' => 'image_path', 'type' => 'VARCHAR(255)'],
    'stock_quantity' => ['new_name' => 'stock', 'type' => 'INT(11)']
];

foreach ($renames_chem as $old => $info) {
    $new = $info['new_name'];
    $type = $info['type'];
    $res = $conn->query("SHOW COLUMNS FROM chemicals LIKE '$old'");
    if ($res && $res->num_rows > 0) {
        $sql = "ALTER TABLE chemicals CHANGE `$old` `$new` $type";
        if ($conn->query($sql))
            echo "Renamed chemicals.`$old` to `$new`.<br>";
    }
}

$adds_chem = [
    'category' => "VARCHAR(100) AFTER brand",
    'active_ingredient' => "VARCHAR(255) AFTER category",
    'crop_suitability' => "TEXT AFTER active_ingredient",
    'safety_label' => "VARCHAR(50) AFTER crop_suitability",
    'pack_size' => "VARCHAR(50) AFTER price",
    'status' => "ENUM('Active', 'Inactive') DEFAULT 'Active' AFTER image_path"
];

foreach ($adds_chem as $col => $def) {
    $res = $conn->query("SHOW COLUMNS FROM chemicals LIKE '$col'");
    if ($res && $res->num_rows == 0) {
        $sql = "ALTER TABLE chemicals ADD COLUMN `$col` $def";
        if ($conn->query($sql))
            echo "Added chemicals.`$col`.<br>";
    }
}

// 2. Orders Table Migration
// Rename tables first
$table_renames = [
    'chemical_orders_v2' => 'chemical_orders',
    'chemical_order_items_v2' => 'chemical_order_items'
];

foreach ($table_renames as $old => $new) {
    $res = $conn->query("SHOW TABLES LIKE '$old'");
    if ($res && $res->num_rows > 0) {
        $check_new = $conn->query("SHOW TABLES LIKE '$new'");
        if ($check_new && $check_new->num_rows == 0) {
            if ($conn->query("RENAME TABLE `$old` TO `$new`"))
                echo "Renamed table `$old` to `$new`.<br>";
        } else {
            echo "Cannot rename `$old` to `$new`: `$new` already exists or error.<br>";
        }
    }
}

// Ensure columns in chemical_orders
$renames_orders = [
    'farmer_id' => ['new_name' => 'seller_id', 'type' => 'INT(6) UNSIGNED'],
    'order_status' => ['new_name' => 'delivery_status', 'type' => "ENUM('Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Processing'"]
];

foreach ($renames_orders as $old => $info) {
    $new = $info['new_name'];
    $type = $info['type'];
    $res = $conn->query("SHOW COLUMNS FROM chemical_orders LIKE '$old'");
    if ($res && $res->num_rows > 0) {
        $sql = "ALTER TABLE chemical_orders CHANGE `$old` `$new` $type";
        if ($conn->query($sql))
            echo "Renamed chemical_orders.`$old` to `$new`.<br>";
    }
}

// Ensure columns in chemical_order_items
$renames_items = [
    'chemical_id' => ['new_name' => 'product_id', 'type' => 'INT(6) UNSIGNED'],
    'price_at_time' => ['new_name' => 'price', 'type' => 'DECIMAL(10,2)']
];

foreach ($renames_items as $old => $info) {
    $new = $info['new_name'];
    $type = $info['type'];
    $res = $conn->query("SHOW COLUMNS FROM chemical_order_items LIKE '$old'");
    if ($res && $res->num_rows > 0) {
        $sql = "ALTER TABLE chemical_order_items CHANGE `$old` `$new` $type";
        if ($conn->query($sql))
            echo "Renamed chemical_order_items.`$old` to `$new`.<br>";
    }
}

echo "Migration Complete.";
?>