<?php
// Add error reporting at the top
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();

// Make sure no output is sent before headers
if (headers_sent()) {
    die('Headers already sent');
}

header('Content-Type: application/json');

try {
    // Validate input
    $damage_id = filter_input(INPUT_POST, 'damage_id', FILTER_VALIDATE_INT);
    $action_taken = filter_input(INPUT_POST, 'action_taken', FILTER_SANITIZE_STRING);
    $refund_amount = filter_input(INPUT_POST, 'refund_amount', FILTER_VALIDATE_FLOAT) ?: 0;
    $replacement_quantity = filter_input(INPUT_POST, 'replacement_quantity', FILTER_VALIDATE_INT) ?: 0;

    if (!$damage_id || !$action_taken) {
        throw new Exception('Invalid input data');
    }

    // Begin transaction
    $db->beginTransaction();

    // Get damage details
    $damage_query = "SELECT product_id, quantity_damaged FROM DamagedProduct WHERE damage_id = ?";
    $stmt = $db->prepare($damage_query);
    $stmt->execute([$damage_id]);
    $damage = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$damage) {
        throw new Exception('Damage record not found');
    }

    // Update damage record
    $update_query = "UPDATE DamagedProduct 
                    SET action_taken = ?, 
                        refund_amount = ?
                    WHERE damage_id = ?";
    $stmt = $db->prepare($update_query);
    $success = $stmt->execute([
        $action_taken,
        ($action_taken === 'Refunded') ? $refund_amount : 0,
        $damage_id
    ]);

    if (!$success) {
        throw new Exception('Failed to update damage record');
    }

    // Handle replacement action
    if ($action_taken === 'Replaced') {
        $update_stock = "UPDATE Product 
                        SET stock_quantity = stock_quantity + ? 
                        WHERE product_id = ?";
        $stmt = $db->prepare($update_stock);
        $success = $stmt->execute([$replacement_quantity, $damage['product_id']]);
        
        if (!$success) {
            throw new Exception('Failed to update product stock');
        }
        $message = "Successfully replaced {$replacement_quantity} items";
        $toastType = 'success';
    }
    
    // Handle refund action
    elseif ($action_taken === 'Refunded' && $refund_amount > 0) {
        $insert_transaction = "INSERT INTO Transaction 
                             (amount_paid, transaction_type, reference_id)
                             VALUES (?, 'refund', ?)";
        $stmt = $db->prepare($insert_transaction);
        $success = $stmt->execute([
            $refund_amount,
            $damage_id
        ]);
        
        if (!$success) {
            throw new Exception('Failed to record refund transaction');
        }
        $message = "Successfully refunded ৳" . number_format($refund_amount, 2);
        $toastType = 'success';
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => $message ?? 'Action processed successfully',
        'toastType' => $toastType ?? 'success'
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'toastType' => 'danger'
    ]);
}
?>