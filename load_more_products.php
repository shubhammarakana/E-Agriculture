<?php
include 'db_connect.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 10;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$html = '';

if ($type === 'crops') {
    $sql_crops = "SELECT c.*, u.fullname as seller_name, u.role FROM crops c JOIN users u ON c.farmer_id = u.id WHERE u.status = 'Active' ORDER BY c.id DESC LIMIT $limit OFFSET $offset";
    $res = $conn->query($sql_crops);
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $img = !empty($row["image_path"]) ? $row["image_path"] : 'images/default-crop.png';
            if(strpos($img, 'images/') === 0) $img = $img;
            $seller = ($row['role'] === 'admin') ? 'AgriAI Verified' : $row['seller_name'];
            $price_fmt = number_format($row['price']);
            $name_safe = htmlspecialchars($row['name']);
            $cat_safe = htmlspecialchars($row['category']);
            $seller_safe = htmlspecialchars($seller);
            $unit = $row['price_unit'];
            
            $html .= "
            <a href='crop_details.php?id={$row['id']}' class='product-card'>
                <div class='card-img-wrapper'>
                    <img src='{$img}' alt='{$name_safe}' class='card-img' style='border-radius: 32px; object-fit: cover;'>
                </div>
                <div class='card-body'>
                    <div class='product-cat'>{$cat_safe}</div>
                    <h3 class='product-title'>{$name_safe}</h3>
                    <div class='seller-info'><i class='fas fa-store-alt'></i> {$seller_safe}</div>
                    <div class='card-footer'>
                        <span class='price-tag'>₹{$price_fmt} <span style='font-size: 0.8rem; color: #6b7280; font-weight: 500;'>/{$unit}</span></span>
                    </div>
                </div>
            </a>";
        }
    }
} elseif ($type === 'chemicals') {
    $sql_chems = "SELECT c.*, u.fullname as seller_name, u.role FROM chemicals c JOIN users u ON c.seller_id = u.id WHERE c.status = 'Active' AND u.status = 'Active' ORDER BY c.id DESC LIMIT $limit OFFSET $offset";
    $res = $conn->query($sql_chems);
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
             $img = !empty($row["image_path"]) ? $row["image_path"] : 'images/default_chem.png';
             if(strpos($img, 'images/') === 0) $img = $img;
             $seller = ($row['role'] === 'admin') ? 'AgriAI Verified' : $row['seller_name'];
             $price_fmt = number_format($row['price']);
             $name_safe = htmlspecialchars($row['name']);
             $cat_safe = htmlspecialchars($row['category']);
             $seller_safe = htmlspecialchars($seller);
             
             $html .= "
             <a href='chemical_details.php?id={$row['id']}' class='product-card'>
                 <div class='card-img-wrapper'>
                     <img src='{$img}' alt='{$name_safe}' class='card-img'>
                 </div>
                 <div class='card-body'>
                     <div class='product-cat'>{$cat_safe}</div>
                     <h3 class='product-title'>{$name_safe}</h3>
                     <div class='seller-info'><i class='fas fa-flask'></i> {$seller_safe}</div>
                     <div class='card-footer'>
                         <span class='price-tag' style='color:#2563eb;'>₹{$price_fmt}</span>
                     </div>
                 </div>
             </a>";
        }
    }
} elseif ($type === 'seeds') {
    $sql_seeds = "SELECT s.*, u.fullname as seller_name, u.role FROM seeds s JOIN users u ON s.seller_id = u.id WHERE s.status = 'Active' AND u.status = 'Active' ORDER BY s.id DESC LIMIT $limit OFFSET $offset";
    $res = $conn->query($sql_seeds);
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
             $img = !empty($row["image_path"]) ? $row["image_path"] : 'images/default_seed.png';
             if(strpos($img, 'images/') === 0) $img = $img;
             $seller = ($row['role'] === 'admin') ? 'AgriAI Verified' : $row['seller_name'];
             $price_fmt = number_format($row['price']);
             $name_safe = htmlspecialchars($row['name']);
             $cat_safe = htmlspecialchars($row['category']);
             $seller_safe = htmlspecialchars($seller);
             
             $html .= "
             <a href='seed_details.php?id={$row['id']}' class='product-card'>
                 <div class='card-img-wrapper'>
                     <img src='{$img}' alt='{$name_safe}' class='card-img'>
                 </div>
                 <div class='card-body'>
                     <div class='product-cat'>{$cat_safe}</div>
                     <h3 class='product-title'>{$name_safe}</h3>
                     <div class='seller-info'><i class='fas fa-seedling'></i> {$seller_safe}</div>
                     <div class='card-footer'>
                         <span class='price-tag' style='color:#059669;'>₹{$price_fmt}</span>
                     </div>
                 </div>
             </a>";
        }
    }
}

echo $html;
?>
