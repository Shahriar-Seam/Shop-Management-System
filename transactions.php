<?php
session_start();
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <link rel="icon" type="image/x-icon" href="assets/icon.jpg">
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- navbar -->
     <header class="header">
        <?php include 'assets/navbar.php'; ?>
    </header>

    <!-- main -->
    <main class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-money-bill-wave me-2"></i>
                Transactions
            </h2>
            <div class="d-flex gap-2">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <input type="date" id="dateFrom" class="form-control" placeholder="From">
                </div>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <input type="date" id="dateTo" class="form-control" placeholder="To">
                </div>
                <select class="form-select" id="typeFilter">
                    <option value="">All Types</option>
                    <option value="Order Payment">Order Payment</option>
                    <option value="Sale Payment">Sale Payment</option>
                    <option value="Refund">Refund</option>
                    <option value="Debt Payment">Debt Payment</option>
                </select>
                <button class="btn btn-primary" onclick="printTransactions()">
                    <i class="fas fa-print me-2"></i>Print
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="transactionsTable">
                            <?php
                            try {
                                $query = "SELECT 
                                            t.transaction_id,
                                            t.timestamp,
                                            t.transaction_type,
                                            t.amount_paid,
                                            CASE 
                                                WHEN t.transaction_type = 'Order Payment' THEN (
                                                    SELECT CONCAT('Order from ', c.company_name)
                                                    FROM ProductOrder po
                                                    JOIN Company c ON po.company_id = c.company_id
                                                    WHERE po.order_id = t.reference_id
                                                )
                                                WHEN t.transaction_type = 'Sale Payment' THEN (
                                                    SELECT CONCAT('Sale to ', c.name)
                                                    FROM Sale s
                                                    JOIN Customer c ON s.customer_id = c.customer_id
                                                    WHERE s.sale_id = t.reference_id
                                                )
                                                WHEN t.transaction_type = 'Debt Payment' THEN (
                                                    SELECT CONCAT('Payment from ', c.name)
                                                    FROM Customer c
                                                    WHERE c.customer_id = t.reference_id
                                                )
                                                WHEN t.transaction_type = 'Refund' THEN (
                                                    SELECT CONCAT('Refund for ', p.name)
                                                    FROM DamagedProduct d
                                                    JOIN Product p ON d.product_id = p.product_id
                                                    WHERE d.damage_id = t.reference_id
                                                )
                                            END as details
                                        FROM 
                                            Transaction t
                                        ORDER BY 
                                            t.timestamp DESC";

                                $stmt = $db->prepare($query);
                                $stmt->execute();

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $badge_class = match($row['transaction_type']) {
                                        'Order Payment' => 'bg-danger',
                                        'Sale Payment' => 'bg-success',
                                        'Refund' => 'bg-warning',
                                        'Debt Payment' => 'bg-info',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <tr>
                                        <td><?= date('Y-m-d H:i', strtotime($row['timestamp'])) ?></td>
                                        <td>
                                            <span class="badge <?= $badge_class ?>">
                                                <?= $row['transaction_type'] ?>
                                            </span>
                                        </td>
                                        <td>৳<?= number_format($row['amount_paid'], 2) ?></td>
                                        <td><?= htmlspecialchars($row['details'] ?? '-') ?></td>
                                    </tr>
                                    <?php
                                }
                            } catch(PDOException $e) {
                                echo "<tr><td colspan='4' class='text-center text-danger'>Error loading transactions</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to filter transactions
        function filterTransactions() {
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            const typeFilter = document.getElementById('typeFilter').value;
            
            const rows = document.querySelectorAll('#transactionsTable tr');
            rows.forEach(row => {
                const date = row.cells[0].textContent.split(' ')[0];
                const type = row.cells[1].textContent.trim();
                
                const dateMatch = (!dateFrom || date >= dateFrom) && (!dateTo || date <= dateTo);
                const typeMatch = !typeFilter || type === typeFilter;
                
                row.style.display = dateMatch && typeMatch ? '' : 'none';
            });
        }

        // Function to print transactions
        function printTransactions() {
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            const typeFilter = document.getElementById('typeFilter').value;
            
            // Create a new window for printing
            const printWindow = window.open('', '_blank');
            
            // Get the filtered transactions
            const rows = Array.from(document.querySelectorAll('#transactionsTable tr'))
                .filter(row => row.style.display !== 'none');
            
            // Create the print content
            let printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Transaction History</title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .header { text-align: center; margin-bottom: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
                        th { background-color: #f5f5f5; }
                        .badge { padding: 4px 8px; border-radius: 4px; color: white; }
                        .bg-danger { background-color: #dc3545; }
                        .bg-success { background-color: #198754; }
                        .bg-warning { background-color: #ffc107; color: black; }
                        .bg-info { background-color: #0dcaf0; }
                        .bg-secondary { background-color: #6c757d; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h2>Transaction History</h2>
                        <p>Date Range: ${dateFrom || 'Start'} to ${dateTo || 'End'}</p>
                        ${typeFilter ? `<p>Type: ${typeFilter}</p>` : ''}
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            // Add the transaction rows
            rows.forEach(row => {
                const cells = row.cells;
                printContent += `
                    <tr>
                        <td>${cells[0].textContent}</td>
                        <td>${cells[1].innerHTML}</td>
                        <td>${cells[2].textContent}</td>
                        <td>${cells[3].textContent}</td>
                    </tr>
                `;
            });
            
            printContent += `
                        </tbody>
                    </table>
                </body>
                </html>
            `;
            
            // Write the content and print
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
        }

        // Add event listeners for filters
        document.getElementById('dateFrom').addEventListener('change', filterTransactions);
        document.getElementById('dateTo').addEventListener('change', filterTransactions);
        document.getElementById('typeFilter').addEventListener('change', filterTransactions);
    </script>
</body>
</html>