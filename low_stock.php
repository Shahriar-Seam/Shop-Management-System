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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock</title>
    <link rel="icon" type="image/png" href="assets/image.png">
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
        <h2 class="mb-4">
            <i class="fas fa-box-open text-warning me-2"></i>
            Low Stock Products
        </h2>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Current Stock</th>
                        <th>Threshold</th>
                        <th>Company</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $query = "SELECT p.*, c.company_name 
                                 FROM Product p 
                                 JOIN Company c ON p.company_id = c.company_id
                                 WHERE p.stock_quantity < p.restock_threshold
                                 ORDER BY p.stock_quantity ASC";
                        
                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . $row['stock_quantity'] . "</td>";
                            echo "<td>" . $row['restock_threshold'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['company_name']) . "</td>";
                            echo "<td>
                                    <button class='btn btn-sm btn-warning' 
                                            onclick='orderProduct({$row['product_id']}, {$row['company_id']})'>
                                        <i class='fas fa-plus me-1'></i>Order
                                    </button>
                                  </td>";
                            echo "</tr>";
                        }
                    } catch(PDOException $e) {
                        echo "<tr><td colspan='5' class='text-center text-danger'>Error loading products</td></tr>";
                        error_log("Error fetching low stock products: " . $e->getMessage());
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    <script>
        // Function to refresh the table data
        async function refreshTable() {
            try {
                console.log('Starting table refresh...');
                const response = await fetch('get_low_stock_products.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const html = await response.text();
                console.log('Received response:', html);
                
                const tbody = document.querySelector('table tbody');
                if (tbody) {
                    tbody.innerHTML = html;
                    console.log('Table updated successfully');
                } else {
                    console.error('Could not find table body element');
                }
            } catch (error) {
                console.error('Error refreshing table:', error);
            }
        }

        function orderProduct(productId, companyId) {
            console.log('Opening order modal for product:', productId, 'company:', companyId);
            
            // Remove any existing modal first
            const existingModal = document.getElementById('orderModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.id = 'orderModal';
            modal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            <iframe id="orderFrame" src="order.php?supplier_id=${companyId}&product_id=${productId}&popup=true" 
                                    frameborder="0" style="width:100%; min-height:500px;"></iframe>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();

            // Function to close modal and refresh table
            window.closeOrderModal = function(success = false) {
                console.log('closeOrderModal called');
                bsModal.hide();
                modal.remove();
                if (success) {
                    showToast('Order placed successfully!', 'success');
                    refreshTable();
                }
            };
            
            // Remove modal on close
            modal.addEventListener('hidden.bs.modal', function() {
                console.log('Modal hidden event triggered');
                modal.remove();
            });
        }

        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container') || createToastContainer();
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            toastContainer.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toast.remove();
                }, 500);
            }, 3000);
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container';
            document.body.appendChild(container);
            return container;
        }

        // Add event listener for DOMContentLoaded to ensure the page is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, initializing...');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>