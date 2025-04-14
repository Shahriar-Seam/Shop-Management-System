<?php
session_start();
require_once 'assets/database.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$unit_price = isset($_POST['unit_price']) ? floatval($_POST['unit_price']) : 0;
$restock_threshold = isset($_POST['restock_threshold']) ? intval($_POST['restock_threshold']) : 0;
$company_id = isset($_POST['company_id']) ? intval($_POST['company_id']) : null;
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;

// Validate required fields
if (empty($name) || empty($company_id)) {
    echo json_encode(['success' => false, 'message' => 'Product name and supplier are required']);
    exit;
}

// Validate numeric fields
if ($unit_price < 0 || $restock_threshold < 0) {
    echo json_encode(['success' => false, 'message' => 'Unit price and restock threshold must be non-negative']);
    exit;
}

try {
    if ($product_id) {
        // Update existing product
        $query = "UPDATE Product 
                 SET name = :name,
                     unit_price = :unit_price,
                     restock_threshold = :restock_threshold,
                     company_id = :company_id
                 WHERE product_id = :product_id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
    } else {
        // Insert new product
        $query = "INSERT INTO Product (name, unit_price, restock_threshold, company_id, stock_quantity) 
                 VALUES (:name, :unit_price, :restock_threshold, :company_id, 0)";
        
        $stmt = $db->prepare($query);
    }
    
    // Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':unit_price', $unit_price);
    $stmt->bindParam(':restock_threshold', $restock_threshold);
    $stmt->bindParam(':company_id', $company_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving product']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?> 