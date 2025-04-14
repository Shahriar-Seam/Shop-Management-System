<?php
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();

header('Content-Type: application/json');

try {
    // Get form data
    $customer_id = $_POST['customer_id'] ?? null;
    $amount = $_POST['amount'] ?? null;
    
    // Validate required fields
    if (!$customer_id || !$amount) {
        throw new Exception('Missing required fields');
    }
    
    // Validate amount
    if (!is_numeric($amount) || $amount <= 0) {
        throw new Exception('Invalid amount');
    }
    
    // Start transaction
    $db->beginTransaction();
    
    // Insert payment record
    $query = "INSERT INTO Transaction (
                transaction_type,
                amount_paid,
                reference_id
            ) VALUES (
                'Debt Payment',
                :amount,
                :customer_id
            )";
            
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':amount' => $amount,
        ':customer_id' => $customer_id
    ]);
    
    // Commit transaction
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Payment added successfully'
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 