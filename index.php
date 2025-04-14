<?php
session_start();

require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();

// Verify database connection
if (!$db) {
    die("Database connection failed");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Title</title>
    <link rel="icon" type="image/x-icon" href="assets/icon.jpg">
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
    <link 
        rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>
    <!-- navbar -->
     <header class="header">
        <?php include 'assets/navbar.php'; ?>
    </header>

    <!-- main -->
    <main class="container mt-4">
        <div class="row">
            <!-- Income and Expenses Card -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row">
                            <!-- Income Section -->
                            <div class="col-6 border-end">
                                <h5 class="card-title text-success">
                                    <i class="fas fa-money-bill-wave me-2"></i>Income
                                </h5>
                                <?php
                                try {
                                    $today = date('Y-m-d');
                                    $income_query = "SELECT SUM(amount_paid) as total 
                                                   FROM Transaction 
                                                   WHERE DATE(timestamp) = :today 
                                                   AND transaction_type IN ('Sale Payment', 'Debt Payment', 'Refund')";
                                    
                                    $stmt = $db->prepare($income_query);
                                    $stmt->bindParam(':today', $today);
                                    $stmt->execute();
                                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $income = $result['total'] ?? 0;
                                } catch(PDOException $e) {
                                    error_log("Error fetching income: " . $e->getMessage());
                                    $income = 0;
                                }
                                ?>
                                <h2 class="card-text">৳<?php echo number_format($income, 2); ?></h2>
                            </div>

                            <!-- Expenses Section -->
                            <div class="col-6">
                                <h5 class="card-title text-danger">
                                    <i class="fas fa-receipt me-2"></i>Expenses
                                </h5>
                                <?php
                                try {
                                    $cost_query = "SELECT SUM(amount_paid) as total 
                                                 FROM Transaction 
                                                 WHERE DATE(timestamp) = :today 
                                                 AND transaction_type IN ('Order Payment')";
                                    
                                    $stmt = $db->prepare($cost_query);
                                    $stmt->bindParam(':today', $today);
                                    $stmt->execute();
                                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $cost = $result['total'] ?? 0;
                                } catch(PDOException $e) {
                                    error_log("Error fetching expenses: " . $e->getMessage());
                                    $cost = 0;
                                }
                                ?>
                                <h2 class="card-text">৳<?php echo number_format($cost, 2); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert Card -->
            <div class="col-6 col-md-3 mb-4">
                <a href="low_stock.php" class="text-decoration-none">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-warning mb-3">
                                <i class="fas fa-box-open me-2"></i>Low Stock
                            </h5>
                            <?php
                            try {
                                $low_stock_query = "SELECT COUNT(*) as count 
                                                       FROM Product 
                                                       WHERE stock_quantity <= restock_threshold";
                                    
                                    $stmt = $db->prepare($low_stock_query);
                                    $stmt->execute();
                                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $low_stock_count = $result['count'] ?? 0;
                                } catch(PDOException $e) {
                                    error_log("Error fetching low stock count: " . $e->getMessage());
                                    $low_stock_count = 0;
                                }
                                ?>
                            <div class="text-center">
                                <h2 class="card-text text-warning mb-0">
                                    <?php echo $low_stock_count; ?>
                                </h2>
                                <span class="text-muted">need restocking</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Damaged Products Card -->
            <div class="col-6 col-md-3 mb-4">
                <a href="damaged_products.php" class="text-decoration-none">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-danger mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>Damaged
                            </h5>
                            <?php
                            try {
                                $damaged_query = "SELECT COUNT(*) as count 
                                               FROM DamagedProduct 
                                               WHERE action_taken = 'No Action'";
                                
                                $stmt = $db->prepare($damaged_query);
                                $stmt->execute();
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                $damaged_count = $result['count'] ?? 0;
                            } catch(PDOException $e) {
                                error_log("Error fetching damaged count: " . $e->getMessage());
                                $damaged_count = 0;
                            }
                            ?>
                            <div class="text-center">
                                <h2 class="card-text text-danger mb-0">
                                    <?php echo $damaged_count; ?>
                                </h2>
                                <span class="text-muted">damaged items</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </main>
</body>
</html>