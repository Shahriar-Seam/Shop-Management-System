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

    // Calculate total debt
    $query = "SELECT 
        c.customer_id,
        c.name AS customer_name,
        COALESCE(SUM(s.total_amount - s.discount_amount), 0) AS total_owed,
        COALESCE((
            SELECT SUM(amount_paid)
            FROM Transaction 
            WHERE transaction_type = 'Sale Payment' 
            AND reference_id IN (SELECT sale_id FROM Sale WHERE customer_id = c.customer_id)
        ), 0) 
        + COALESCE((
            SELECT SUM(amount_paid)
            FROM Transaction 
            WHERE transaction_type = 'Debt Payment' 
            AND reference_id = c.customer_id
        ), 0) AS total_paid,
        (COALESCE(SUM(s.total_amount - s.discount_amount), 0) 
         - 
         (
            COALESCE((
                SELECT SUM(amount_paid)
                FROM Transaction 
                WHERE transaction_type = 'Sale Payment' 
                AND reference_id IN (SELECT sale_id FROM Sale WHERE customer_id = c.customer_id)
            ), 0) 
            + COALESCE((
                SELECT SUM(amount_paid)
                FROM Transaction 
                WHERE transaction_type = 'Debt Payment' 
                AND reference_id = c.customer_id
            ), 0)
         )
        ) AS total_debt_remaining
    FROM 
        Customer c
    LEFT JOIN 
        Sale s ON s.customer_id = c.customer_id
    WHERE 
        c.customer_id = :customer_id
    GROUP BY 
        c.customer_id, c.name";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->execute();

    $debt = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($debt) {
        echo json_encode([
            'success' => true,
            'debt' => [
                'customer_id' => $debt['customer_id'],
                'customer_name' => $debt['customer_name'],
                'total_owed' => number_format($debt['total_owed'], 2),
                'total_paid' => number_format($debt['total_paid'], 2),
                'total_debt_remaining' => number_format($debt['total_debt_remaining'], 2)
            ]
        ]);
    } else {
        // If no debt found, return zero values
        echo json_encode([
            'success' => true,
            'debt' => [
                'customer_id' => $customer_id,
                'customer_name' => '',
                'total_owed' => '0.00',
                'total_paid' => '0.00',
                'total_debt_remaining' => '0.00'
            ]
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 