<?php
session_start();
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();

// Check if opened as popup
$isPopup = isset($_GET['popup']) && $_GET['popup'] == 'true';
$supplierId = $_GET['company_id'] ?? 0;
$productId = $_GET['product_id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isPopup ? '' : 'Order Products' ?></title>
    <?php if($isPopup): ?>
    <style>
        body {
            padding: 0;
            margin: 0;
            background: transparent;
        }
        .container {
            max-width: 100%;
            padding: 0;
        }
    </style>
    <?php endif; ?>
    <link rel="icon" type="image/x-icon" href="assets/icon.jpg">
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php if($isPopup): ?>
    <style>
        body {
            padding: 0;
            margin: 0;
            background: transparent;
        }
        .container {
            max-width: 100%;
            padding: 0;
        }
    </style>
    <?php endif; ?>
    <style>
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
            --secondary-color: #6c757d;
            --light-green: #d4edda;
        }
        
        .card-header {
            background-color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        .table-hover tbody tr:hover {
            background-color: var(--light-green);
        }
        
        .table thead th {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <main class="container mt-4">
        <div class="card">
            <div class="card-header text-white">
                <h4 class="mb-0">
                    <i class="fas fa-cart-plus me-2"></i>
                    Order Products from Supplier
                </h4>
            </div>
            <div class="card-body">
                <form id="orderForm">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Supplier</label>
                            <select class="form-select" id="supplierSelect" name="supplier_id" onchange="loadProducts(this.value)">
                                <option value="">Select a supplier</option>
                                <?php
                                try {
                                    $query = "SELECT company_id, company_name FROM company";
                                    $stmt = $db->prepare($query);
                                    $stmt->execute();
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="'.$row['company_id'].'">'.$row['company_name'].'</option>';
                                    }
                                } catch(PDOException $e) {
                                    echo '<option value="">Error loading suppliers</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="productsTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>Order Quantity</th>
                                    <th>Total (৳)</th>
                                </tr>
                            </thead>
                            <tbody id="orderItems">
                                <!-- Products will be loaded via AJAX -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Grand Total:</td>
                                    <td class="fw-bold" id="grandTotal">৳0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i> Place Order
                        </button>
                        <?php if($isPopup): ?>
                        <button type="button" class="btn btn-secondary ms-2" id="cancelButton">
                            <i class="fas fa-times me-2"></i> Cancel
                        </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function loadProducts(supplierId) {
            if(supplierId) {
                fetch(`get_supplier_products.php?supplier_id=${supplierId}`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('orderItems').innerHTML = html;
                        calculateTotal();
                        
                        // After products load, check if we need to select a specific product
                        const urlParams = new URLSearchParams(window.location.search);
                        const productId = urlParams.get('product_id');
                        if(productId) {
                            const productRow = document.querySelector(`.order-row[data-product-id="${productId}"]`);
                            if(productRow) {
                                const stockElement = productRow.querySelector('.stock-quantity');
                                const thresholdElement = productRow.querySelector('.restock-threshold');
                                const quantityInput = productRow.querySelector('.quantity');
                                
                                if(stockElement && thresholdElement && quantityInput) {
                                    quantityInput.focus();
                                    // Set default quantity to restock amount
                                    const stock = parseFloat(stockElement.textContent) || 0;
                                    const threshold = parseFloat(thresholdElement.textContent) || 0;
                                    const suggestedQty = Math.max(threshold - stock, 0);
                                    quantityInput.value = suggestedQty;
                                    // Trigger total calculation
                                    const changeEvent = new Event('change');
                                    quantityInput.dispatchEvent(changeEvent);
                                }
                            }
                        }
                    });
            } else {
                document.getElementById('orderItems').innerHTML = '';
                document.getElementById('grandTotal').textContent = '৳0.00';
            }
        }

        // Auto-load products if supplier_id is provided in URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const supplierId = urlParams.get('supplier_id');
            
            if(supplierId) {
                const supplierSelect = document.getElementById('supplierSelect');
                supplierSelect.value = supplierId;
                loadProducts(supplierId);
            }
        });

        // Calculate order total
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.order-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const price = parseFloat(row.querySelector('.price').value) || 0;
                // row.querySelector('.row-total').textContent = '৳' + price.toFixed(2);
                total += price;
            });
            document.getElementById('grandTotal').textContent = '৳' + total.toFixed(2);
        }

        // Add cancel button handler
        document.getElementById('cancelButton')?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (window.parent && window.parent.closeOrderModal) {
                window.parent.closeOrderModal();
            }
        });

        // Function to validate quantity
        function validateQuantity(input) {
            const quantity = parseFloat(input.value) || 0;
            const row = input.closest('.order-row');
            const feedback = row.querySelector('.quantity-feedback');
            
            if (quantity <= 0) {
                // input.classList.add('is-invalid');
                // if (!feedback) {
                //     const div = document.createElement('div');
                //     div.className = 'invalid-feedback quantity-feedback';
                //     div.textContent = 'Quantity must be greater than 0';
                //     input.parentNode.appendChild(div);
                // }
                return false;
            } else {
                // input.classList.remove('is-invalid');
                // if (feedback) {
                //     feedback.remove();
                // }
                return true;
            }
        }

        // Add quantity validation on input and change
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity')) {
                validateQuantity(e.target);
                calculateTotal();
            }
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('quantity')) {
                validateQuantity(e.target);
                calculateTotal();
            }
        });

        // Add form submission validation
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            let isValid = false;
            let hasValidQuantity = false;
            
            document.querySelectorAll('.quantity').forEach(input => {
                if (validateQuantity(input)) {
                    isValid = true;
                }
                if (parseFloat(input.value) > 0) {
                    hasValidQuantity = true;
                }
            });

            if (!isValid || !hasValidQuantity) {
                e.preventDefault();
                if (!hasValidQuantity) {
                    showToast('Please add at least one product with quantity greater than 0', 'danger');
                }
                return;
            }

            const total = parseFloat(document.getElementById('grandTotal').textContent.replace('৳', '').replace(',', ''));
            if (total <= 0) {
                e.preventDefault();
                return;
            }

            const formData = new FormData(this);
            const orderData = {
                supplier_id: formData.get('supplier_id'),
                products: []
            };

            // Calculate total amount and collect products
            let totalAmount = 0;
            document.querySelectorAll('.order-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                if(quantity > 0) {
                    const productId = row.dataset.productId;
                    const price = parseFloat(row.querySelector('.price').value) || 0;
                    totalAmount += price;
                    
                    orderData.products.push({
                        product_id: productId,
                        quantity: quantity,
                        price: price
                    });
                }
            });

            if(orderData.products.length === 0) {
                alert('Please add at least one product with quantity greater than 0');
                return;
            }

            fetch('process_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Close the modal and refresh the parent window's table
                    if (window.parent && window.parent.closeOrderModal) {
                        window.parent.closeOrderModal(true);
                        showToast('Order placed successfully', 'success');
                    } else {
                        console.error('Parent window or closeOrderModal function not found');
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while placing the order');
            });
        });

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
    </script>
</body>
</html>