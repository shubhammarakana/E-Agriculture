<?php
include 'db_connect.php';

// Create chemicals table
$sql = "CREATE TABLE IF NOT EXISTS chemicals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'chemicals' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Insert Dummy Data
// Check if table is empty
$result = $conn->query("SELECT count(*) as count FROM chemicals");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // Get a seller ID (farmer)
    $res_farmer = $conn->query("SELECT id FROM users WHERE role='farmer' LIMIT 1");
    if ($res_farmer->num_rows > 0) {
        $farmer_id = $res_farmer->fetch_assoc()['id'];

        $dummy_data = [
            "('1', '$farmer_id', 'SuperGro Fertilizer', 'High nitrogen fertilizer for rapid growth.', 'Fertilizer', 500.00, 100, 'images/chem_fert.jpg')",
            "('2', '$farmer_id', 'PestControl X', 'Effective against aphids and mites.', 'Pesticide', 250.00, 50, 'images/chem_pest.jpg')",
            "('3', '$farmer_id', 'WeedKiller Pro', 'Selective herbicide for wheat.', 'Herbicide', 350.00, 75, 'images/chem_herb.jpg')",
            "('4', '$farmer_id', 'RootBooster', 'Promotes strong root development.', 'Nutrient', 400.00, 120, 'images/chem_root.jpg')"
        ];

        // Note: I removed the ID from insert to let auto-increment work, 
        // but for dummy data consistency I'll just use NULL or let it auto-inc.
        // Actually, let's just insert without ID.
        
        $sql_insert = "INSERT INTO chemicals (seller_id, name, description, category, price, stock, image_path) VALUES 
        ('$farmer_id', 'SuperGro Fertilizer', 'High nitrogen fertilizer for rapid growth.', 'Fertilizer', 500.00, 100, 'images/chem_fert.jpg'),
        ('$farmer_id', 'PestControl X', 'Effective against aphids and mites.', 'Pesticide', 250.00, 50, 'images/chem_pest.jpg'),
        ('$farmer_id', 'WeedKiller Pro', 'Selective herbicide for wheat.', 'Herbicide', 350.00, 75, 'images/chem_herb.jpg'),
        ('$farmer_id', 'RootBooster', 'Promotes strong root development.', 'Nutrient', 400.00, 120, 'images/chem_root.jpg')";

        if ($conn->query($sql_insert) === TRUE) {
            echo "Dummy data inserted successfully.<br>";
        } else {
            echo "Error inserting dummy data: " . $conn->error . "<br>";
        }
    } else {
        echo "No farmer found to assign dummy chemicals to.<br>";
    }
} else {
    echo "Table already has data.<br>";
}
?>
