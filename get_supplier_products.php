<?php
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();

$supplierId = $_GET['supplier_id'] ?? 0;

try {
    $query = "SELECT p.product_id, p.name, p.stock_quantity, p.restock_threshold
              FROM Product p
              WHERE p.company_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$supplierId]);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <tr class="order-row" data-product-id="<?= $row['product_id'] ?>">
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td class="stock-quantity"><?= $row['stock_quantity'] ?></td>
            <td class="restock-threshold d-none"><?= $row['restock_threshold'] ?></td>
            <td>
                <input type="number" name="quantity[<?= $row['product_id'] ?>]" 
                       class="form-control quantity" min="0" value="0" 
                       onchange="calculateTotal()">
            </td>
            <td>
                <input type="number" name="price[<?= $row['product_id'] ?>]" 
                       class="form-control price" min="0.01" step="0.01" 
                       value="" onchange="calculateTotal()">
            </td>
            <td class="row-total">৳0.00</td>
        </tr>
        <?php
    }
} catch(PDOException $e) {
    echo "<tr><td colspan='5' class='text-center text-danger'>Error loading products</td></tr>";
}
?>