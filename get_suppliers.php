<?php
session_start();
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();
?>
<table class="table table-hover align-middle">
    <thead>
        <tr>
            <th>Supplier Name</th>
            <th>Phone Number</th>
            <th>Address</th>
            <th>Salesperson</th>
            <th>Salesperson Phone Number</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        try {
            $query = "SELECT
                        c.company_id,
                        c.company_name,
                        c.company_phone_number,
                        c.company_address,
                        s.name,
                        s.phone
                    FROM
                        company c
                    LEFT JOIN
                        salesperson s ON c.company_id = s.company_id AND s.status = 'Active'
                    ORDER BY c.company_name";
            $stmt = $db->prepare($query);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['company_name']) ?></td>
                    <td><?= htmlspecialchars($row['company_phone_number']) ?></td>
                    <td><?= htmlspecialchars($row['company_address']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-success me-2" onclick="orderFromSupplier(<?= $row['company_id'] ?>)">
                            <i class="fas fa-shopping-cart"></i> Order
                        </button>
                        <button class="btn btn-sm btn-warning me-2" onclick="updateSalesperson(<?= $row['company_id'] ?>)">
                            <i class="fas fa-user-edit"></i> Update Salesperson
                        </button>
                        <button class="btn btn-sm btn-info" onclick="viewSalespersonHistory(<?= $row['company_id'] ?>)">
                            <i class="fas fa-history"></i> View Salesperson History
                        </button>
                    </td>
                </tr>
                <?php
            }
        } catch(PDOException $e) {
            echo "<tr><td colspan='6' class='text-center text-danger'>Error loading suppliers</td></tr>";
        }
        ?>
    </tbody>
</table> 