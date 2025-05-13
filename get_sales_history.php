<?php
session_start();
require_once 'assets/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

try {
    // Fetch sales with customer information and payment details
    $query = "SELECT 
                s.sale_id,
                s.sale_time,
                s.total_amount,
                s.discount_amount,
                c.name as customer_name,
                c.contact_info,
                GROUP_CONCAT(
                    CONCAT(p.name, ' (', sd.quantity, ' x ৳', sd.price_per_unit, ')')
                    SEPARATOR '||'
                ) as items,
                COALESCE(
                    (SELECT SUM(amount_paid) 
                     FROM Transaction 
                     WHERE reference_id = s.sale_id 
                     AND transaction_type = 'Sale Payment'
                     GROUP BY reference_id),
                    0
                ) as amount_paid
              FROM Sale s
              LEFT JOIN Customer c ON s.customer_id = c.customer_id
              LEFT JOIN SaleDetail sd ON s.sale_id = sd.sale_id
              LEFT JOIN Product p ON sd.product_id = p.product_id
              GROUP BY s.sale_id, s.sale_time, s.total_amount, s.discount_amount, c.name, c.contact_info
              ORDER BY s.sale_time DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($sales)) {
        echo json_encode(['status' => 'success', 'data' => [], 'message' => 'No sales found']);
        exit;
    }

    // Format the data for display
    $formatted_sales = array_map(function($sale) {
        // Ensure items is not null
        $items = !empty($sale['items']) ? explode('||', $sale['items']) : [];
        
        $formatted_items = array_map(function($item) {
            return htmlspecialchars($item);
        }, $items);
        
        // Ensure all numeric values are properly formatted
        $total_amount = floatval($sale['total_amount']);
        $discount_amount = floatval($sale['discount_amount']);
        $amount_paid = floatval($sale['amount_paid']);
        $debt = $total_amount - $amount_paid;
        
        return [
            'sale_id' => intval($sale['sale_id']),
            'date' => date('Y-m-d H:i:s', strtotime($sale['sale_time'])),
            'customer' => $sale['customer_name'] ? htmlspecialchars($sale['customer_name']) : 'Walk-in Customer',
            'contact' => $sale['contact_info'] ? htmlspecialchars($sale['contact_info']) : 'N/A',
            'items' => $formatted_items,
            'total' => number_format($total_amount, 2),
            'discount' => number_format($discount_amount, 2),
            'amount_paid' => number_format($amount_paid, 2),
            'debt' => number_format($debt, 2)
        ];
    }, $sales);

    echo json_encode(['status' => 'success', 'data' => $formatted_sales]);
} catch (PDOException $e) {
    error_log("Database error in get_sales_history.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 