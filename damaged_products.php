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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                Damaged Products
            </h2>
            <div>
                <button class="btn btn-outline-secondary me-2" id="toggleView">
                    <i class="fas fa-list me-2"></i>Show All Records
                </button>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#recordDamageModal">
                    <i class="fas fa-plus me-2"></i>Record Damage
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
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Damage Cost</th>
                                <th>Refund Amount</th>
                                <th>Action</th>
                                <th>Reason</th>
                                <th>Take Action</th>
                            </tr>
                        </thead>
                        <tbody id="damageTable">
                            <?php
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
                                        WHERE d.action_taken = 'No Action'
                                        ORDER BY d.damage_date DESC";
                                
                                $stmt = $db->prepare($query);
                                $stmt->execute();

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $badge_class = match($row['action_taken']) {
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
                                            <?php if($row['action_taken'] === 'No Action'): ?>
                                            <button class="btn btn-sm btn-warning ms-2" onclick="showActionModal(<?= $row['damage_id'] ?>)">
                                                <i class="fas fa-edit"></i> Take Action
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } catch(PDOException $e) {
                                echo "<tr><td colspan='8' class='text-center text-danger'>Error loading damage records</td></tr>";
                                error_log("Error fetching damage records: " . $e->getMessage());
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Record Damage Modal -->
    <div class="modal fade" id="recordDamageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record Product Damage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="damageForm" onsubmit="submitDamageForm(event)">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Product</label>
                                <select name="product_id" class="form-select" required id="productSelect">
                                    <option value="">Select Product</option>
                                    <?php
                                    try {
                                        $product_query = "SELECT 
                                            product_id, 
                                            name,
                                            unit_price,
                                            stock_quantity
                                        FROM Product 
                                        ORDER BY name";
                                        $stmt = $db->prepare($product_query);
                                        $stmt->execute();
                                        while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value='" . $product['product_id'] . "' data-stock='" . $product['stock_quantity'] . "'>" 
                                                . htmlspecialchars($product['name']) 
                                                . " (" 
                                                . htmlspecialchars($product['stock_quantity'])
                                                . ")"
                                                . "</option>";
                                        }
                                    } catch(PDOException $e) {
                                        echo "<option value=''>Error loading products</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity Damaged</label>
                                <input type="number" name="quantity_damaged" id="quantityDamaged" class="form-control" required min="1" max="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Damage Cost</label>
                                <input type="number" name="damage_cost" class="form-control" required min="0" step="0.01">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Refund Amount</label>
                                <input type="number" name="refund_amount" class="form-control" min="0" step="0.01">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Action</label>
                                <select name="action_taken" class="form-select" required>
                                    <option value="No Action">No Action</option>
                                    <option value="Refunded">Refunded</option>
                                    <option value="Replaced">Replaced</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Reason</label>
                                <textarea name="reason" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Record Damage</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Action Modal -->
        <div class="modal fade" id="actionModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Take Action on Damaged Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="actionForm" onsubmit="submitActionForm(event)">
                        <input type="hidden" name="damage_id" id="damageId">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Action</label>
                                <select name="action_taken" class="form-select" required id="actionTypeSelect">
                                    <option value="Refunded">Refunded</option>
                                    <option value="Replaced">Replaced</option>
                                </select>
                            </div>
                            
                            <!-- Refund Amount Field (shown when Refunded is selected) -->
                            <div class="mb-3" id="refundAmountField">
                                <label class="form-label">Refund Amount</label>
                                <input type="number" name="refund_amount" class="form-control" min="0" step="0.01">
                            </div>
                            
                            <!-- Replacement Quantity Field (shown when Replaced is selected) -->
                            <div class="mb-3 d-none" id="replacementQuantityField">
                                <label class="form-label">Replacement Quantity</label>
                                <input type="number" name="replacement_quantity" class="form-control" min="1">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Action</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            // Handle action type selection
            const actionTypeSelect = document.getElementById('actionTypeSelect');
            const refundAmountField = document.getElementById('refundAmountField');
            const replacementQuantityField = document.getElementById('replacementQuantityField');

            actionTypeSelect.addEventListener('change', function() {
                if (this.value === 'Refunded') {
                    refundAmountField.classList.remove('d-none');
                    replacementQuantityField.classList.add('d-none');
                } else {
                    refundAmountField.classList.add('d-none');
                    replacementQuantityField.classList.remove('d-none');
                }
            });

            // Initialize fields based on default selection
            document.addEventListener('DOMContentLoaded', function() {
                if (actionTypeSelect.value === 'Refunded') {
                    refundAmountField.classList.remove('d-none');
                } else {
                    replacementQuantityField.classList.remove('d-none');
                }
            });
        </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleButton = document.getElementById('toggleView');
        let showingAll = false;

        toggleButton.addEventListener('click', async () => {
            const tableBody = document.getElementById('damageTable');
            showingAll = !showingAll;
            
            try {
                const response = await fetch(`get_damaged_products.php?show_all=${showingAll ? 1 : 0}`);
                const html = await response.text();
                tableBody.innerHTML = html;
                
                toggleButton.innerHTML = showingAll ? 
                    '<i class="fas fa-filter me-2"></i>Show Pending Only' : 
                    '<i class="fas fa-list me-2"></i>Show All Records';
                    
                toggleButton.classList.toggle('btn-outline-secondary');
                toggleButton.classList.toggle('btn-secondary');
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        });

        function viewDetails(damageId) {
            window.location.href = `damage_details.php?id=${damageId}`;
        }

        // Handle quantity max value
        const productSelect = document.getElementById('productSelect');
        const quantityDamaged = document.getElementById('quantityDamaged');

        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stockQuantity = selectedOption.dataset.stock;
            quantityDamaged.max = stockQuantity;
            quantityDamaged.value = Math.min(quantityDamaged.value, stockQuantity);
        });

        async function submitDamageForm(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            try {
                const response = await fetch('process_damage.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    // Refresh the table
                    const tableBody = document.getElementById('damageTable');
                    const tableResponse = await fetch(`get_damaged_products.php?show_all=${showingAll ? 1 : 0}`);
                    tableBody.innerHTML = await tableResponse.text();
                    
                    // Reset form and close modal
                    form.reset();
                    bootstrap.Modal.getInstance(document.getElementById('recordDamageModal')).hide();
                    
                    // Show toast notification
                    showToast(result.message || 'Damage recorded successfully', 'success');
                } else {
                    showToast(result.message || 'Error recording damage', 'danger');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Error processing request', 'danger');
            }
        }

        function showActionModal(damageId) {
            document.getElementById('damageId').value = damageId;
            const actionModal = new bootstrap.Modal(document.getElementById('actionModal'));
            actionModal.show();
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

        // Update the success/error handling in submitActionForm
        async function submitActionForm(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            
            try {
                const response = await fetch('process_action.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    // Refresh the table
                    const tableBody = document.getElementById('damageTable');
                    const tableResponse = await fetch(`get_damaged_products.php?show_all=${showingAll ? 1 : 0}`);
                    tableBody.innerHTML = await tableResponse.text();
                    
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('actionModal')).hide();
                    
                    // Show success message
                    showToast(result.message || 'Action saved successfully');
                } else {
                    showToast(result.message || 'Error saving action');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Error processing action: ' + error.message);
            }
        }
    </script>
</body>
</html>