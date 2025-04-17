<?php
session_start();
require_once 'assets/database.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();

    $order_id = $_GET['order_id'] ?? null;

    if (!$order_id) {
        throw new Exception('Order ID is required');
    }

    // Get order details with supplier and salesperson information
    $query = "SELECT 
                po.order_id,
                po.order_time,
                c.company_name,
                c.company_phone_number,
                c.company_address,
                (
                    SELECT s.name 
                    FROM Salesperson s 
                    WHERE s.company_id = c.company_id 
                    AND (s.archived_on IS NULL OR s.archived_on > po.order_time)
                    ORDER BY 
                        CASE 
                            WHEN s.archived_on IS NULL THEN 1 
                            ELSE 0 
                        END,
                        s.archived_on ASC
                    LIMIT 1
                ) AS salesperson_name,
                (
                    SELECT s.phone 
                    FROM Salesperson s 
                    WHERE s.company_id = c.company_id 
                    AND (s.archived_on IS NULL OR s.archived_on > po.order_time)
                    ORDER BY 
                        CASE 
                            WHEN s.archived_on IS NULL THEN 1 
                            ELSE 0 
                        END,
                        s.archived_on ASC
                    LIMIT 1
                ) AS salesperson_phone,
                t.amount_paid as total_amount
            FROM 
                ProductOrder po
            JOIN 
                Company c ON po.company_id = c.company_id
            JOIN 
                Transaction t ON po.order_id = t.reference_id
            WHERE 
                po.order_id = :order_id
                AND t.transaction_type = 'Order Payment'";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('Order not found');
    }

    // Get ordered products
    $productsQuery = "SELECT 
                        p.name as product_name,
                        od.quantity,
                        od.cost,
                        od.cost as total_price
                    FROM 
                        OrderDetail od
                    JOIN 
                        Product p ON od.product_id = p.product_id
                    WHERE 
                        od.order_id = :order_id";

    $stmt = $db->prepare($productsQuery);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'order' => $order,
        'products' => $products
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 