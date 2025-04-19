<?php
require_once 'assets/database.php';

// Set error reporting to prevent HTML output
error_reporting(0);
ini_set('display_errors', 0);

// Ensure we're sending JSON
header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }

    $search = isset($_GET['search']) ? $_GET['search'] : '';

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
            WHERE c.name LIKE :search OR c.contact_info LIKE :search
            GROUP BY 
                c.customer_id, c.name, c.contact_info
            ORDER BY 
                total_debt_remaining DESC, c.name ASC";

    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception("Query preparation failed");
    }

    $searchParam = "%$search%";
    $stmt->bindParam(':search', $searchParam);
    $stmt->execute();
    
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'customers' => $customers
    ]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} 