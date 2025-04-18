<?php
session_start();
require_once 'assets/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();

        // Get form data
        $customer_id = !empty($_POST['customer_id']) ? $_POST['customer_id'] : null;
        $discount = floatval($_POST['discount']);
        $amount_paid = floatval($_POST['amount_paid']);
        $products = $_POST['product_id'];
        $quantities = $_POST['quantity'];

        // Calculate total amount
        $total_amount = 0;
        $sale_details = [];

        // Validate and prepare sale details
        for ($i = 0; $i < count($products); $i++) {
            if (empty($products[$i]) || empty($quantities[$i])) continue;

            $product_id = $products[$i];
            $quantity = intval($quantities[$i]);

            // Get product price and check stock
            $stmt = $conn->prepare("SELECT unit_price, stock_quantity FROM Product WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception("Product not found");
            }

            if ($product['stock_quantity'] < $quantity) {
                throw new Exception("Insufficient stock for product ID: $product_id");
            }

            $price_per_unit = $product['unit_price'];
            $subtotal = $price_per_unit * $quantity;
            $total_amount += $subtotal;

            $sale_details[] = [
                'product_id' => $product_id,
                'quantity' => $quantity,
                'price_per_unit' => $price_per_unit
            ];
        }

        if (empty($sale_details)) {
            throw new Exception("No products selected");
        }

        // Apply discount
        $total_amount -= $discount;

        // Validate customer selection for partial payment
        if ($amount_paid < $total_amount && empty($customer_id)) {
            throw new Exception("Customer must be selected for partial payment");
        }

        // Insert sale record
        $stmt = $conn->prepare("
            INSERT INTO Sale (customer_id, total_amount, discount_amount) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$customer_id, $total_amount, $discount]);
        $sale_id = $conn->lastInsertId();

        // Insert sale details and update stock
        foreach ($sale_details as $detail) {
            // Insert sale detail
            $stmt = $conn->prepare("
                INSERT INTO SaleDetail (sale_id, product_id, quantity, price_per_unit)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $sale_id,
                $detail['product_id'],
                $detail['quantity'],
                $detail['price_per_unit']
            ]);

            // Update product stock
            $stmt = $conn->prepare("
                UPDATE Product 
                SET stock_quantity = stock_quantity - ? 
                WHERE product_id = ?
            ");
            $stmt->execute([$detail['quantity'], $detail['product_id']]);
        }

        // Record transaction
        $stmt = $conn->prepare("
            INSERT INTO Transaction (reference_id, transaction_type, amount_paid)
            VALUES (?, 'Sale Payment', ?)
        ");
        $stmt->execute([$sale_id, $amount_paid]);

        $conn->commit();
        $_SESSION['success'] = "Sale completed successfully!";
        header("Location: sales.php");
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: sales.php");
        exit();
    }
} else {
    header("Location: sales.php");
    exit();
} 