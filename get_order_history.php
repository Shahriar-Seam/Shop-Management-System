<?php
session_start();
require_once 'assets/database.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();

    $supplier_id = $_GET['supplier_id'] ?? null;

    if (!$supplier_id) {
        throw new Exception('Supplier ID is required');
    }

    $query = "SELECT 
                po.order_id,
                po.order_time as order_date,
                t.amount_paid
            FROM 
                ProductOrder po
            JOIN 
                Transaction t ON po.order_id = t.reference_id
            WHERE 
                po.company_id = :supplier_id
                AND t.transaction_type = 'Order Payment'
            ORDER BY 
                po.order_time DESC";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':supplier_id', $supplier_id);
    $stmt->execute();

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'orders' => $orders
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 