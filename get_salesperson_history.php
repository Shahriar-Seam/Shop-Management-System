<?php
session_start();
require_once 'assets/database.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get supplier ID from query parameter
    $supplier_id = $_GET['supplier_id'] ?? '';

    if (empty($supplier_id)) {
        throw new Exception('Supplier ID is required');
    }

    // Get all salespersons for the supplier, ordered by created_at
    $query = "SELECT 
                name, 
                phone, 
                status, 
                DATE_FORMAT(archived_on, '%Y-%m-%d %H:%i:%s') as archived_on
              FROM salesperson 
              WHERE company_id = :company_id 
              ORDER BY archived_on DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([':company_id' => $supplier_id]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'history' => $history
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 