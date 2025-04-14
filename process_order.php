<?php
session_start();
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();

header('Content-Type: application/json');

try {
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Validate input
    if(empty($data['supplier_id']) || empty($data['products'])) {
        throw new Exception('Invalid order data');
    }

    // Begin transaction
    $db->beginTransaction();

    try {
        // Insert order record into ProductOrder table
        $orderQuery = "INSERT INTO ProductOrder (company_id, order_time) 
                       VALUES (:company_id, NOW())";
        $orderStmt = $db->prepare($orderQuery);
        $orderStmt->execute([
            ':company_id' => $data['supplier_id']
        ]);
        $orderId = $db->lastInsertId();

        // Process each product
        foreach($data['products'] as $product) {
            // Validate product quantity
            if ($product['quantity'] <= 0) {
                throw new Exception('Invalid quantity for product');
            }

            // Insert order item into OrderDetail table
            $itemQuery = "INSERT INTO OrderDetail (order_id, product_id, quantity, cost)
                          VALUES (:order_id, :product_id, :quantity, :cost)";
            $itemStmt = $db->prepare($itemQuery);
            $itemStmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $product['product_id'],
                ':quantity' => $product['quantity'],
                ':cost' => $product['price']
            ]);

            // Update product stock
            $updateQuery = "UPDATE Product 
                           SET stock_quantity = stock_quantity + :quantity
                           WHERE product_id = :product_id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([
                ':product_id' => $product['product_id'],
                ':quantity' => $product['quantity']
            ]);
        }

        // Calculate total amount
        $totalAmount = array_reduce($data['products'], function($sum, $product) {
            return $sum + $product['price'];
        }, 0);

        // Record transaction for the order
        $transactionQuery = "INSERT INTO Transaction 
                           (reference_id, transaction_type, amount_paid)
                           VALUES (:order_id, 'Order Payment', :amount)";
        $transactionStmt = $db->prepare($transactionQuery);
        $transactionStmt->execute([
            ':order_id' => $orderId,
            ':amount' => $totalAmount
        ]);

        // Commit transaction
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully',
            'toastType' => 'success'
        ]);

    } catch(Exception $e) {
        // Rollback on error
        if($db->inTransaction()) {
            $db->rollBack();
        }
        throw $e;
    }
    
} catch(Exception $e) {
    error_log("Order processing error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'toastType' => 'danger'
    ]);
}