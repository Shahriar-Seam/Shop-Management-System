<?php
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();

$show_all = isset($_GET['show_all']) && $_GET['show_all'] == 1;

try {
    $query = "SELECT 
                d.damage_id,
                d.damage_date,
                p.name AS product_name,
                d.quantity_damaged,
                d.damage_cost,
                d.refund_amount,
                d.action_taken,
                d.reason
            FROM DamagedProduct d
            JOIN Product p ON d.product_id = p.product_id
            " . (!$show_all ? "WHERE d.action_taken = 'No Action'" : "") . "
            ORDER BY d.damage_date DESC";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $badge_class = match ($row['action_taken']) {
            'Refunded' => 'bg-warning',
            'Replaced' => 'bg-info',
            'No Action' => 'bg-secondary',
            default => 'bg-danger'
        };
?>
        <tr>
            <td><?= date('Y-m-d', strtotime($row['damage_date'])) ?></td>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= $row['quantity_damaged'] ?></td>
            <td>৳<?= number_format($row['damage_cost'], 2) ?></td>
            <td>৳<?= number_format($row['refund_amount'], 2) ?></td>
            <td><span class="badge <?= $badge_class ?>"><?= $row['action_taken'] ?></span></td>
            <td><?= htmlspecialchars($row['reason'] ?: '-') ?></td>
            <td>
                <?php if ($row['action_taken'] === 'No Action'): ?>
                    <button class="btn btn-sm btn-warning ms-2" onclick="showActionModal(<?= $row['damage_id'] ?>)">
                        <i class="fas fa-edit"></i> Take Action
                    </button>
                <?php endif; ?>
            </td>
        </tr>
<?php
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='7' class='text-center text-danger'>Error loading damage records</td></tr>";
    error_log("Error fetching damage records: " . $e->getMessage());
}
?>