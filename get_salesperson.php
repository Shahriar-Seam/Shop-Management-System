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

    // Get current active salesperson
    $query = "SELECT name, phone 
              FROM salesperson 
              WHERE company_id = :company_id AND status = 'Active'";
    $stmt = $db->prepare($query);
    $stmt->execute([':company_id' => $supplier_id]);
    $salesperson = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$salesperson) {
        throw new Exception('No active salesperson found');
    }

    echo json_encode([
        'success' => true,
        'salesperson' => $salesperson
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 