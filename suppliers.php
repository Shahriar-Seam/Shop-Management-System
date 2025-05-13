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
    <title>Suppliers</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-truck me-2"></i>
                Suppliers
            </h2>
            <div class="d-flex gap-2">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search suppliers...">
                </div>
                <button class="btn btn-success me-2" onclick="orderFromSupplier()">
                    <i class="fas fa-shopping-cart me-2"></i>Order
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                    <i class="fas fa-plus me-2"></i>Add Supplier
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
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
                                        ORDER BY c.company_id DESC;

                                ";
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
                                            <button class="btn btn-sm btn-info me-2" onclick="viewOrderHistory(<?= $row['company_id'] ?>)">
                                                <i class="fas fa-history"></i> Order History
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
                                echo "<tr><td colspan='7' class='text-center text-danger'>Error loading suppliers</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Supplier Modal -->
        <div class="modal fade" id="addSupplierModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="supplierForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Supplier Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_number" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Salesperson Name</label>
                                <input type="text" name="salesperson_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Salesperson Phone Number</label>
                                <input type="text" name="salesperson_phone_number" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Supplier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Salesperson Modal -->
        <div class="modal fade" id="updateSalespersonModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Salesperson</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="salespersonForm">
                        <input type="hidden" id="supplierId" name="supplier_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="salesperson_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="salesperson_phone_number" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Salesperson History Modal -->
        <div class="modal fade" id="salespersonHistoryModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Salesperson History</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>End Date</th>
                                    </tr>
                                </thead>
                                <tbody id="salespersonHistoryBody">
                                    <!-- History will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order History Modal -->
        <div class="modal fade" id="orderHistoryModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Order History</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount Paid</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="orderHistoryBody">
                                    <!-- History will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Details Modal -->
        <div class="modal fade" id="orderDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Order Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <h6>Supplier Information</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Name:</strong> <span id="supplierName"></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Phone:</strong> <span id="supplierPhone"></span></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Address:</strong> <span id="supplierAddress"></span></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6>Order Information</h6>
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Order Date:</strong> <span id="orderDate"></span></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6>Salesperson Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> <span id="salespersonName"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Phone:</strong> <span id="salespersonPhone"></span></p>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                                <tbody id="orderProductsBody">
                                    <!-- Products will be loaded here -->
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 text-end">
                            <h5>Total Amount: <span id="totalAmount" class="text-primary"></span></h5>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="printOrderDetails()">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to filter suppliers
        function filterSuppliers() {
            const searchText = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const cells = row.cells;
                const supplierName = cells[0].textContent.toLowerCase();
                const phone = cells[1].textContent.toLowerCase();
                const address = cells[2].textContent.toLowerCase();
                const salesperson = cells[3].textContent.toLowerCase();
                const salespersonPhone = cells[4].textContent.toLowerCase();
                
                const searchMatch = !searchText || 
                    supplierName.includes(searchText) || 
                    phone.includes(searchText) || 
                    address.includes(searchText) || 
                    salesperson.includes(searchText) || 
                    salespersonPhone.includes(searchText);
                
                row.style.display = searchMatch ? '' : 'none';
            });
        }

        // Add event listener for search
        document.getElementById('searchInput').addEventListener('input', filterSuppliers);

        // Function to open order modal
        function orderFromSupplier(supplierId) {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.id = 'orderModal';
            modal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            <iframe id="orderFrame" src="order.php?supplier_id=${supplierId}&popup=true" 
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
                }
            };
            
            // Remove modal on close
            modal.addEventListener('hidden.bs.modal', function() {
                console.log('Modal hidden event triggered');
                modal.remove();
            });
        }

        function updateSalesperson(supplierId) {
            console.log('Update salesperson for supplier:', supplierId);
            // Set the supplier ID in the form
            document.getElementById('supplierId').value = supplierId;
            
            // Get current salesperson data
            fetch(`get_salesperson.php?supplier_id=${supplierId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Set the form values
                        document.querySelector('#salespersonForm input[name="salesperson_name"]').value = data.salesperson.name;
                        document.querySelector('#salespersonForm input[name="salesperson_phone_number"]').value = data.salesperson.phone;
                    }
                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('updateSalespersonModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error loading salesperson data', 'error');
                });
        }

        // Form submission handlers
        document.getElementById('supplierForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('process_supplier.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Supplier added successfully!', 'success');
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addSupplierModal'));
                    modal.hide();
                    // Refresh the page to show the new supplier
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast(data.message || 'Error adding supplier', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while adding the supplier', 'error');
            });
        });

        document.getElementById('salespersonForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            const formData = new FormData(this);
            console.log('Form data:', Object.fromEntries(formData));
            
            fetch('process_salesperson.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response received');
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    showToast('Salesperson updated successfully!', 'success');
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('updateSalespersonModal'));
                    modal.hide();
                    // Refresh the page to show the updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast(data.message || 'Error updating salesperson', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while updating the salesperson', 'error');
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

        function viewSalespersonHistory(supplierId) {
            console.log('Viewing salesperson history for supplier:', supplierId);
            
            fetch(`get_salesperson_history.php?supplier_id=${supplierId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('salespersonHistoryBody');
                        tbody.innerHTML = '';
                        
                        data.history.forEach(salesperson => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${salesperson.name}</td>
                                <td>${salesperson.phone}</td>
                                <td>
                                    <span class="badge ${salesperson.status === 'Active' ? 'bg-success' : 'bg-secondary'}">
                                        ${salesperson.status}
                                    </span>
                                </td>
                                <td>${salesperson.archived_on || '-'}</td>
                            `;
                            tbody.appendChild(row);
                        });
                        
                        const modal = new bootstrap.Modal(document.getElementById('salespersonHistoryModal'));
                        modal.show();
                    } else {
                        showToast(data.message || 'Error loading salesperson history', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while loading salesperson history', 'error');
                });
        }

        function viewOrderHistory(supplierId) {
            console.log('Viewing order history for supplier:', supplierId);
            
            fetch(`get_order_history.php?supplier_id=${supplierId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('orderHistoryBody');
                        tbody.innerHTML = '';
                        
                        data.orders.forEach(order => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${order.order_date}</td>
                                <td>৳${order.amount_paid.toLocaleString()}</td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewOrderDetails(${order.order_id})">
                                        <i class="fas fa-info-circle"></i> Details
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                        
                        const modal = new bootstrap.Modal(document.getElementById('orderHistoryModal'));
                        modal.show();
                    } else {
                        showToast(data.message || 'Error loading order history', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while loading order history', 'error');
                });
        }

        function viewOrderDetails(orderId) {
            fetch(`get_order_details.php?order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const order = data.order;
                        const products = data.products;

                        // Set supplier information
                        document.getElementById('supplierName').textContent = order.company_name;
                        document.getElementById('supplierPhone').textContent = order.company_phone_number;
                        document.getElementById('supplierAddress').textContent = order.company_address;

                        // Set order information
                        document.getElementById('orderDate').textContent = order.order_time;

                        // Set salesperson information
                        document.getElementById('salespersonName').textContent = order.salesperson_name || 'N/A';
                        document.getElementById('salespersonPhone').textContent = order.salesperson_phone || 'N/A';

                        // Set products
                        const tbody = document.getElementById('orderProductsBody');
                        tbody.innerHTML = '';
                        products.forEach(product => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${product.product_name}</td>
                                <td>${product.quantity}</td>
                                <td>৳${product.total_price.toLocaleString()}</td>
                            `;
                            tbody.appendChild(row);
                        });

                        // Set total amount at the bottom
                        document.getElementById('totalAmount').textContent = `৳${order.total_amount.toLocaleString()}`;

                        const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
                        modal.show();
                    } else {
                        showToast(data.message || 'Error loading order details', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while loading order details', 'error');
                });
        }

        function printOrderDetails() {
            const printWindow = window.open('', '_blank');
            const modalContent = document.querySelector('#orderDetailsModal .modal-content').cloneNode(true);
            
            // Remove buttons and add print-specific styles
            const footer = modalContent.querySelector('.modal-footer');
            footer.remove();
            
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Order Details</title>
                    <style>
                        @page {
                            size: A4;
                            margin: 20mm;
                        }
                        body { 
                            font-family: Arial, sans-serif;
                            line-height: 1.4;
                            color: #333;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 20px;
                        }
                        .header h1 {
                            color: #333;
                            margin-bottom: 5px;
                            font-size: 24px;
                        }
                        .info-section {
                            margin-bottom: 30px;
                        }
                        .info-row {
                            display: flex;
                            justify-content: space-between;
                            margin-bottom: 10px;
                        }
                        .info-group {
                            flex: 1;
                        }
                        .info-group:first-child {
                            margin-right: 20px;
                        }
                        .info-label {
                            font-weight: bold;
                            color: #666;
                            margin-bottom: 2px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 20px 0;
                        }
                        th, td {
                            padding: 8px;
                            text-align: left;
                            border: 1px solid #ddd;
                        }
                        th {
                            background-color: #f5f5f5;
                            font-weight: bold;
                        }
                        .total-section {
                            text-align: right;
                            margin-top: 20px;
                            padding-top: 10px;
                            border-top: 1px solid #333;
                        }
                        .total-section h3 {
                            font-size: 18px;
                            margin: 0;
                        }
                        .signature-section {
                            margin-top: 40px;
                            text-align: right;
                        }
                        .signature-line {
                            width: 200px;
                            border-top: 1px solid #333;
                            margin-top: 30px;
                            display: inline-block;
                        }
                        .signature-label {
                            margin-top: 5px;
                            font-size: 14px;
                            color: #666;
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Order Details</h1>
                    </div>

                    <div class="info-section">
                        <div class="info-row">
                            <div class="info-group">
                                <div class="info-label">Supplier Name</div>
                                <div id="printSupplierName"></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Order Date</div>
                                <div id="printOrderDate"></div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-group">
                                <div class="info-label">Supplier Phone</div>
                                <div id="printSupplierPhone"></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Supplier Address</div>
                                <div id="printSupplierAddress"></div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-group">
                                <div class="info-label">Salesperson Name</div>
                                <div id="printSalespersonName"></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Salesperson Phone</div>
                                <div id="printSalespersonPhone"></div>
                            </div>
                        </div>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Total Price (৳)</th>
                            </tr>
                        </thead>
                        <tbody id="printOrderProductsBody">
                        </tbody>
                    </table>

                    <div class="total-section">
                        <h3>Total Amount: <span id="printTotalAmount"></span></h3>
                    </div>

                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="signature-label">Authorized Signature</div>
                    </div>
                </body>
                </html>
            `;
            
            printWindow.document.write(printContent);
            
            // Copy data to print version
            printWindow.document.getElementById('printSupplierName').textContent = document.getElementById('supplierName').textContent;
            printWindow.document.getElementById('printSupplierPhone').textContent = document.getElementById('supplierPhone').textContent;
            printWindow.document.getElementById('printSupplierAddress').textContent = document.getElementById('supplierAddress').textContent;
            printWindow.document.getElementById('printOrderDate').textContent = document.getElementById('orderDate').textContent;
            printWindow.document.getElementById('printSalespersonName').textContent = document.getElementById('salespersonName').textContent;
            printWindow.document.getElementById('printSalespersonPhone').textContent = document.getElementById('salespersonPhone').textContent;
            printWindow.document.getElementById('printTotalAmount').textContent = document.getElementById('totalAmount').textContent;

            // Copy products table
            const productsBody = document.getElementById('orderProductsBody');
            const printProductsBody = printWindow.document.getElementById('printOrderProductsBody');
            productsBody.querySelectorAll('tr').forEach(row => {
                const newRow = printProductsBody.insertRow();
                newRow.innerHTML = row.innerHTML;
            });

            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>