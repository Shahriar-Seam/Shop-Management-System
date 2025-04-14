<?php
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();

header('Content-Type: application/json');

try {
    // Validate input
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity_damaged', FILTER_VALIDATE_INT);
    $damage_cost = filter_input(INPUT_POST, 'damage_cost', FILTER_VALIDATE_FLOAT);
    $refund_amount = filter_input(INPUT_POST, 'refund_amount', FILTER_VALIDATE_FLOAT) ?: 0;
    $action_taken = filter_input(INPUT_POST, 'action_taken');
    $reason = filter_input(INPUT_POST, 'reason');

    if (!$product_id || !$quantity || !$damage_cost) {
        throw new Exception('Invalid input data');
    }

    // Check stock quantity
    $stock_query = "SELECT stock_quantity FROM Product WHERE product_id = ?";
    $stmt = $db->prepare($stock_query);
    $stmt->execute([$product_id]);
    $stock = $stmt->fetchColumn();

    if ($quantity > $stock) {
        throw new Exception('Damaged quantity cannot exceed stock quantity');
    }

    // Begin transaction
    $db->beginTransaction();

    // Insert damage record
    $query = "INSERT INTO DamagedProduct (product_id, quantity_damaged, damage_cost, refund_amount, action_taken, reason) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$product_id, $quantity, $damage_cost, $refund_amount, $action_taken, $reason]);

    // Update product stock
    $update_query = "UPDATE Product SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
    $stmt = $db->prepare($update_query);
    $stmt->execute([$quantity, $product_id]);

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Damage record created successfully'
    ]);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>