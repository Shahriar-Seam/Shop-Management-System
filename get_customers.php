<?php
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();

header('Content-Type: application/json');

try {
    $query = "SELECT 
                c.customer_id,
                c.name,
                c.contact_info,
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
            GROUP BY 
                c.customer_id, c.name, c.contact_info
            ORDER BY 
                total_debt_remaining DESC, c.name";

    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'customers' => $customers
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching customers'
    ]);
} 