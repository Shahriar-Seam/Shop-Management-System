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

    // Fetch customer's sales and payments history
    $query = "(
        SELECT 
            s.sale_time as transaction_date,
            'Sale' as transaction_type,
            s.total_amount,
            s.discount_amount,
            (s.total_amount - s.discount_amount) as amount_owed,
            COALESCE((
                SELECT SUM(amount_paid)
                FROM Transaction 
                WHERE transaction_type = 'Sale Payment' 
                AND reference_id = s.sale_id
            ), 0) as sale_payment,
            0 as debt_payment,
            COALESCE((
                SELECT SUM(amount_paid)
                FROM Transaction 
                WHERE transaction_type = 'Sale Payment' 
                AND reference_id = s.sale_id
            ), 0) as total_paid,
            ((s.total_amount - s.discount_amount) - COALESCE((
                SELECT SUM(amount_paid)
                FROM Transaction 
                WHERE transaction_type = 'Sale Payment' 
                AND reference_id = s.sale_id
            ), 0)) as remaining_due,
            s.sale_id,
            GROUP_CONCAT(
                CONCAT(p.name, ' (', sd.quantity, ' x ৳', sd.price_per_unit, ')')
                SEPARATOR '||'
            ) as items
        FROM 
            Sale s
        LEFT JOIN SaleDetail sd ON s.sale_id = sd.sale_id
        LEFT JOIN Product p ON sd.product_id = p.product_id
        WHERE 
            s.customer_id = :customer_id
        GROUP BY 
            s.sale_id, s.sale_time, s.total_amount, s.discount_amount
    )
    UNION ALL
    (
        SELECT 
            t.timestamp as transaction_date,
            'Debt Payment' as transaction_type,
            NULL as total_amount,
            NULL as discount_amount,
            NULL as amount_owed,
            0 as sale_payment,
            t.amount_paid as debt_payment,
            t.amount_paid as total_paid,
            NULL as remaining_due,
            NULL as sale_id,
            NULL as items
        FROM 
            Transaction t
        WHERE 
            t.transaction_type = 'Debt Payment' 
            AND t.reference_id = :customer_id
    )
    ORDER BY 
        transaction_date DESC";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->execute();

    $history = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $history[] = [
            'transaction_date' => $row['transaction_date'],
            'transaction_type' => $row['transaction_type'],
            'total_amount' => $row['total_amount'] ? number_format($row['total_amount'], 2) : '-',
            'discount_amount' => $row['discount_amount'] ? number_format($row['discount_amount'], 2) : '-',
            'amount_owed' => $row['amount_owed'] ? number_format($row['amount_owed'], 2) : '-',
            'sale_payment' => number_format($row['sale_payment'], 2),
            'debt_payment' => number_format($row['debt_payment'], 2),
            'total_paid' => number_format($row['total_paid'], 2),
            'remaining_due' => $row['remaining_due'] ? number_format($row['remaining_due'], 2) : '-',
            'sale_id' => $row['sale_id'],
            'items' => $row['items'] ?: '-'
        ];
    }

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