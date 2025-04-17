<?php
session_start();
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();
?>
<table class="table table-hover align-middle">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Current Stock</th>
            <th>Restock Threshold</th>
            <th>Unit Price</th>
            <th>Supplier</th>
            <th>Total Sold</th>
            <th>Total Income</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        try {
            $query = "SELECT 
                        p.product_id,
                        p.name as product_name,
                        p.stock_quantity,
                        p.unit_price,
                        p.restock_threshold,
                        c.company_name,
                        COALESCE(SUM(sd.quantity), 0) as total_sold,
                        COALESCE(SUM(sd.quantity * sd.price_per_unit), 0) as total_income
                    FROM 
                        Product p
                    JOIN 
                        Company c ON p.company_id = c.company_id
                    LEFT JOIN
                        SaleDetail sd ON p.product_id = sd.product_id
                    GROUP BY
                        p.product_id, p.name, p.stock_quantity, p.unit_price, 
                        p.restock_threshold, c.company_name
                    ORDER BY 
                        total_income DESC, p.name";
            $stmt = $db->prepare($query);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td>
                        <span class="badge <?= $row['stock_quantity'] <= $row['restock_threshold'] ? 'bg-warning' : 'bg-success' ?>">
                            <?= $row['stock_quantity'] ?>
                        </span>
                    </td>
                    <td><?= $row['restock_threshold'] ?></td>
                    <td>৳<?= number_format($row['unit_price'], 2) ?></td>
                    <td><?= htmlspecialchars($row['company_name']) ?></td>
                    <td><?= $row['total_sold'] ?></td>
                    <td>৳<?= number_format($row['total_income'], 2) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editProduct(<?= $row['product_id'] ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </td>
                </tr>
                <?php
            }
        } catch(PDOException $e) {
            echo "<tr><td colspan='8' class='text-center text-danger'>Error loading products</td></tr>";
        }
        ?>
    </tbody>
</table> 