<?php
session_start();
require_once 'assets/database.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get product ID from query parameters
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit;
}

try {
    // Prepare and execute query to get product details
    $query = "SELECT 
                p.product_id,
                p.name,
                p.unit_price,
                p.restock_threshold,
                p.company_id,
                c.company_name
            FROM 
                Product p
            JOIN 
                Company c ON p.company_id = c.company_id
            WHERE 
                p.product_id = :product_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        echo json_encode([
            'success' => true,
            'product' => $product
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching product details: ' . $e->getMessage()
    ]);
}
?> 