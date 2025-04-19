<?php
session_start();
require_once 'assets/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Fetch customers for dropdown
$stmt = $conn->query("SELECT customer_id, name, contact_info FROM Customer ORDER BY name");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch products for dropdown
$stmt = $conn->query("SELECT product_id, name, unit_price, stock_quantity FROM Product WHERE stock_quantity > 0 ORDER BY name");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Sale</title>
    <link rel="icon" type="image/x-icon" href="assets/icon.jpg">
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
</head>
<body>
    <!-- navbar -->
    <header class="header">
        <?php include 'assets/navbar.php'; ?>
    </header>

    <!-- main -->
    <main class="container mt-4">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <h2 class="mb-4">Create New Sale</h2>

        <form action="process_sale.php" method="POST">
        <!-- Customer Info (optional) -->
        <div class="mb-3">
            <label for="customer_id" class="form-label">Customer (for credit sale)</label>
                <select class="form-select select2-customer" id="customer_id" name="customer_id">
                <option value="">Select customer (optional)</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['customer_id']; ?>">
                            <?php echo htmlspecialchars($customer['name']); ?> 
                            (<?php echo htmlspecialchars($customer['contact_info']); ?>)
                        </option>
                    <?php endforeach; ?>
            </select>
                <div class="invalid-feedback" id="customer-error">
                    Customer must be selected for partial payment
                </div>
        </div>

        <!-- Product Selection -->
        <div id="product-list">
            <div class="row g-2 mb-3 product-item">
                    <div class="col-md-4">
                    <label class="form-label">Product</label>
                        <select class="form-select select2-product product-select" name="product_id[]" required>
                        <option value="">Select product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['product_id']; ?>" 
                                        data-price="<?php echo $product['unit_price']; ?>"
                                        data-stock="<?php echo $product['stock_quantity']; ?>">
                                    <?php echo htmlspecialchars($product['name']); ?> 
                                    (Stock: <?php echo $product['stock_quantity']; ?>)
                                </option>
                            <?php endforeach; ?>
                    </select>
                </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit Price</label>
                        <input type="text" class="form-control unit-price" readonly>
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                        <input type="number" class="form-control quantity-input" name="quantity[]" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Subtotal</label>
                        <input type="text" class="form-control subtotal" readonly>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-product"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-outline-success mb-3" id="add-product">
            <i class="fas fa-plus"></i> Add Another Product
        </button>

        <!-- Discount -->
        <div class="mb-3">
            <label for="discount" class="form-label">Discount (Taka)</label>
                <input type="number" class="form-control" id="discount" name="discount" placeholder="Enter discount amount" min="0">
        </div>

            <!-- Amount Paid -->
        <div class="mb-3">
                <label for="amount_paid" class="form-label">Amount Paid (Taka)</label>
                <input type="number" class="form-control" id="amount_paid" name="amount_paid" placeholder="Enter amount paid" min="0" required>
        </div>

        <!-- Total & Submit -->
        <div class="mb-4">
            <h5>Total: <span id="total-amount">৳0</span></h5>
                <h5>Amount Paid: <span id="paid-amount">৳0</span></h5>
                <h5>Debt Amount: <span id="debt-amount">৳0</span></h5>
                <button type="submit" class="btn btn-primary" id="submit-btn">Confirm Sale</button>
        </div>
        </form>
    </main>

    <!-- Scripts -->
    <!-- jQuery must be loaded first -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Then Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Then Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Initialize Select2 for customer dropdown
        $(document).ready(function() {
            // Initialize Select2 for customer dropdown
            $('.select2-customer').select2({
                theme: 'bootstrap-5',
                placeholder: 'Search for a customer...',
                allowClear: true
            });

            // Initialize Select2 for product dropdowns
            $('.select2-product').select2({
                theme: 'bootstrap-5',
                placeholder: 'Search for a product...',
                allowClear: true
            });

            // Set up event handlers for the first product row
            $('.product-select').on('change', function() {
                updateQuantityMax(this);
                calculateSubtotal(this);
            });
            $('.quantity-input').on('input', function() {
                validateQuantity(this);
                calculateSubtotal(this);
            });

            // Function to update max quantity based on selected product
            function updateQuantityMax(selectElement) {
                const $row = $(selectElement).closest('.product-item');
                const $quantityInput = $row.find('.quantity-input');
                const selectedOption = $(selectElement).find('option:selected');
                const maxStock = parseInt(selectedOption.data('stock')) || 0;
                
                $quantityInput.attr('max', maxStock);
                $quantityInput.val(Math.min(parseInt($quantityInput.val()) || 0, maxStock));
                
                // Update validation message
                validateQuantity($quantityInput[0]);
            }

            // Function to validate quantity against stock
            function validateQuantity(inputElement) {
                const $row = $(inputElement).closest('.product-item');
                const $select = $row.find('.product-select');
                const $quantityInput = $(inputElement);
                const $feedback = $row.find('.quantity-feedback');
                
                const selectedOption = $select.find('option:selected');
                const maxStock = parseInt(selectedOption.data('stock')) || 0;
                const quantity = parseInt($quantityInput.val()) || 0;
                
                if (quantity > maxStock) {
                    $quantityInput.addClass('is-invalid');
                    if ($feedback.length === 0) {
                        $row.find('.quantity-col').append(
                            $('<div>', {
                                class: 'invalid-feedback quantity-feedback',
                                text: `Available stock: ${maxStock}`
                            })
                        );
                    }
                    return false;
                } else {
                    $quantityInput.removeClass('is-invalid');
                    $feedback.remove();
                    return true;
                }
            }

            // Function to create a new product row
            function createProductRow() {
                const row = $('<div>', { class: 'row g-2 mb-3 product-item' });
                
                // Product select column
                const productCol = $('<div>', { class: 'col-md-4' });
                productCol.append($('<label>', { class: 'form-label', text: 'Product' }));
                const productSelect = $('<select>', { 
                    class: 'form-select select2-product product-select',
                    name: 'product_id[]',
                    required: true
                });
                productSelect.append($('<option>', { value: '', text: 'Select product' }));
                <?php foreach ($products as $product): ?>
                productSelect.append($('<option>', { 
                    value: '<?php echo $product['product_id']; ?>',
                    'data-price': '<?php echo $product['unit_price']; ?>',
                    'data-stock': '<?php echo $product['stock_quantity']; ?>',
                    text: '<?php echo htmlspecialchars($product['name']); ?> (Stock: <?php echo $product['stock_quantity']; ?>)'
                }));
                <?php endforeach; ?>
                productCol.append(productSelect);
                
                // Unit price column
                const priceCol = $('<div>', { class: 'col-md-2' });
                priceCol.append($('<label>', { class: 'form-label', text: 'Unit Price' }));
                priceCol.append($('<input>', { 
                    type: 'text',
                    class: 'form-control unit-price',
                    readonly: true,
                    placeholder: '৳0.00'
                }));
                
                // Quantity column
                const quantityCol = $('<div>', { class: 'col-md-2 quantity-col' });
                quantityCol.append($('<label>', { class: 'form-label', text: 'Quantity' }));
                quantityCol.append($('<input>', { 
                    type: 'number',
                    class: 'form-control quantity-input',
                    name: 'quantity[]',
                    min: '1',
                    required: true,
                    placeholder: 'Enter quantity'
                }));
                
                // Subtotal column
                const subtotalCol = $('<div>', { class: 'col-md-2' });
                subtotalCol.append($('<label>', { class: 'form-label', text: 'Subtotal' }));
                subtotalCol.append($('<input>', { 
                    type: 'text',
                    class: 'form-control subtotal',
                    readonly: true,
                    placeholder: '৳0.00'
                }));
                
                // Remove button column
                const removeCol = $('<div>', { class: 'col-md-2 d-flex align-items-end' });
                removeCol.append($('<button>', { 
                    type: 'button',
                    class: 'btn btn-danger remove-product'
                }).append($('<i>', { class: 'fas fa-trash-alt' })));
                
                // Assemble the row
                row.append(productCol, priceCol, quantityCol, subtotalCol, removeCol);
                
                return row;
            }

            // Add product button handler
            $('#add-product').on('click', function () {
                const newRow = createProductRow();
                $('#product-list').append(newRow);
                
                // Initialize Select2 for the new product dropdown
                newRow.find('.select2-product').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Search for a product...',
                    allowClear: true
                });

                // Set up event handlers for the new row
                newRow.find('.product-select').on('change', function() {
                    updateQuantityMax(this);
                    calculateSubtotal(this);
                });
                newRow.find('.quantity-input').on('input', function() {
                    validateQuantity(this);
                    calculateSubtotal(this);
                });
            });

            // Remove product row
            $(document).on('click', '.remove-product', function() {
                const allItems = $('.product-item');
                if (allItems.length > 1) {
                    $(this).closest('.product-item').remove();
                    updateTotal();
                }
            });

            // Calculate subtotal when discount changes
            $('#discount').on('input', updateTotal);

            // Form submission validation
            $('form').on('submit', function(e) {
                let isValid = true;
                let errorMessage = '';

                // Validate quantities
                $('.quantity-input').each(function() {
                    if (!validateQuantity(this)) {
                        isValid = false;
                        errorMessage = 'Please correct the quantities to match available stock.';
                    }
                });

                // Validate discount
                if (!validateDiscountAmount()) {
                    isValid = false;
                    errorMessage = 'Discount cannot exceed total amount.';
                }

                // Validate amount paid
                if (!validateAmountPaid()) {
                    isValid = false;
                    errorMessage = 'Amount paid cannot exceed total amount.';
                }

                // Validate form (customer selection for partial payment)
                if (!validateForm()) {
                    isValid = false;
                    errorMessage = 'Customer must be selected for partial payment.';
                }

                if (!isValid) {
                    e.preventDefault();
                    showToast(errorMessage, 'danger');
                }
            });

            // Function to update all amounts
            function updateAllAmounts() {
                let total = 0;
                $('.subtotal').each(function() {
                    const value = parseFloat($(this).val().replace('৳', '')) || 0;
                    total += value;
                });
                
                const discount = parseFloat($('#discount').val()) || 0;
                total -= discount;
                
                const amountPaid = parseFloat($('#amount_paid').val()) || 0;
                const debt = Math.max(0, total - amountPaid);
                
                $('#total-amount').text('৳' + total.toFixed(2));
                $('#paid-amount').text('৳' + amountPaid.toFixed(2));
                $('#debt-amount').text('৳' + debt.toFixed(2));
                
                // Update debt amount color based on value
                const $debtAmount = $('#debt-amount');
                if (debt > 0) {
                    $debtAmount.addClass('text-danger');
                } else {
                    $debtAmount.removeClass('text-danger');
                }

                // Validate discount amount doesn't exceed total
                validateDiscountAmount();

                // Validate amount paid doesn't exceed total
                validateAmountPaid();
            }

            // Function to validate amount paid
            function validateAmountPaid() {
                let total = 0;
                $('.subtotal').each(function() {
                    const value = parseFloat($(this).val().replace('৳', '')) || 0;
                    total += value;
                });

                const amountPaid = parseFloat($('#amount_paid').val()) || 0;
                const $amountPaidInput = $('#amount_paid');
                const $existingFeedback = $amountPaidInput.next('.invalid-feedback');
                
                if (amountPaid > total) {
                    $amountPaidInput.addClass('is-invalid');
                    if ($existingFeedback.length === 0) {
                        $amountPaidInput.after('<div class="invalid-feedback">Amount paid cannot exceed total amount</div>');
                    }
                    return false;
                } else {
                    $amountPaidInput.removeClass('is-invalid');
                    $existingFeedback.remove();
                    return true;
                }
            }

            // Function to validate discount amount
            function validateDiscountAmount() {
                let total = 0;
                $('.subtotal').each(function() {
                    const value = parseFloat($(this).val().replace('৳', '')) || 0;
                    total += value;
                });

                const discount = parseFloat($('#discount').val()) || 0;
                const $discountInput = $('#discount');
                const $existingFeedback = $discountInput.next('.invalid-feedback');
                
                if (discount > total) {
                    $discountInput.addClass('is-invalid');
                    if ($existingFeedback.length === 0) {
                        $discountInput.after('<div class="invalid-feedback">Discount cannot exceed total amount</div>');
                    }
                    return false;
                } else {
                    $discountInput.removeClass('is-invalid');
                    $existingFeedback.remove();
                    return true;
                }
            }

            // Update amounts when discount or amount paid changes
            $('#discount, #amount_paid').on('input', function() {
                updateAllAmounts();
            });

            // Update amounts when product selection or quantity changes
            $(document).on('change input', '.product-select, .quantity-input', function() {
                updateAllAmounts();
            });
        });

        function calculateSubtotal(element) {
            const $item = $(element).closest('.product-item');
            const $productSelect = $item.find('.product-select');
            const $quantityInput = $item.find('.quantity-input');
            const $subtotalInput = $item.find('.subtotal');
            const $unitPriceInput = $item.find('.unit-price');
            
            const selectedOption = $productSelect.find('option:selected');
            const price = parseFloat(selectedOption.data('price')) || 0;
            const quantity = parseInt($quantityInput.val()) || 0;
            const subtotal = price * quantity;
            
            $unitPriceInput.val('৳' + price.toFixed(2));
            $subtotalInput.val('৳' + subtotal.toFixed(2));
            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            $('.subtotal').each(function() {
                const value = parseFloat($(this).val().replace('৳', '')) || 0;
                total += value;
            });
            
            const discount = parseFloat($('#discount').val()) || 0;
            total -= discount;
            
            $('#total-amount').text('৳' + total.toFixed(2));
            validateForm();
        }

        function validateForm() {
            const total = parseFloat($('#total-amount').text().replace('৳', ''));
            const amountPaid = parseFloat($('#amount_paid').val()) || 0;
            const $customerSelect = $('#customer_id');
            
            if (amountPaid < total && !$customerSelect.val()) {
                $customerSelect.addClass('is-invalid');
                return false;
            } else {
                $customerSelect.removeClass('is-invalid');
                return true;
            }
        }

        // Add event listeners for amount paid and customer selection
        $('#amount_paid, #customer_id').on('input change', validateForm);
    </script>
</body>
</html>

