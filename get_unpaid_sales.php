<?php
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();

header('Content-Type: application/json');

try {
    // Get customer ID from query parameters
    $customer_id = $_GET['customer_id'] ?? '';
    
    if (empty($customer_id)) {
        throw new Exception('Customer ID is required');
    }

    // Fetch unpaid sales
    $query = "SELECT 
        s.sale_id,
        s.sale_time,
        s.total_amount,
        s.discount_amount,
        (s.total_amount - s.discount_amount) AS amount_owed,
        COALESCE((
            SELECT SUM(amount_paid)
            FROM Transaction 
            WHERE transaction_type = 'Sale Payment' 
            AND reference_id = s.sale_id
        ), 0) AS amount_paid,
        ((s.total_amount - s.discount_amount) - COALESCE((
            SELECT SUM(amount_paid)
            FROM Transaction 
            WHERE transaction_type = 'Sale Payment' 
            AND reference_id = s.sale_id
        ), 0)) AS remaining_due
    FROM 
        Sale s
    WHERE 
        s.customer_id = :customer_id
        AND (s.total_amount - s.discount_amount) > COALESCE((
            SELECT SUM(amount_paid)
            FROM Transaction 
            WHERE transaction_type = 'Sale Payment' 
            AND reference_id = s.sale_id
        ), 0)
    ORDER BY 
        s.sale_time DESC";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->execute();

    $sales = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sales[] = [
            'sale_id' => $row['sale_id'],
            'sale_time' => $row['sale_time'],
            'total_amount' => number_format($row['total_amount'], 2),
            'discount_amount' => number_format($row['discount_amount'], 2),
            'amount_owed' => number_format($row['amount_owed'], 2),
            'amount_paid' => number_format($row['amount_paid'], 2),
            'remaining_due' => number_format($row['remaining_due'], 2)
        ];
    }

    echo json_encode([
        'success' => true,
        'sales' => $sales
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 