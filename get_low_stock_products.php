<?php
session_start();
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();

header('Content-Type: text/html; charset=utf-8');

try {
    $query = "SELECT p.*, c.company_name 
             FROM Product p 
             JOIN Company c ON p.company_id = c.company_id
             WHERE p.stock_quantity < p.restock_threshold
             ORDER BY p.stock_quantity ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $hasData = false;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $hasData = true;
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . $row['stock_quantity'] . "</td>";
        echo "<td>" . $row['restock_threshold'] . "</td>";
        echo "<td>" . htmlspecialchars($row['company_name']) . "</td>";
        echo "<td>
                <button class='btn btn-sm btn-warning' 
                        onclick='orderProduct({$row['product_id']}, {$row['company_id']})'>
                    <i class='fas fa-plus me-1'></i>Order
                </button>
              </td>";
        echo "</tr>";
    }
    
    if (!$hasData) {
        echo "<tr><td colspan='5' class='text-center'>No low stock products found</td></tr>";
    }
} catch(PDOException $e) {
    error_log("Error fetching low stock products: " . $e->getMessage());
    echo "<tr><td colspan='5' class='text-center text-danger'>Error loading products</td></tr>";
}
?> 