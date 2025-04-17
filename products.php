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
    <title>Products Management</title>
    <link rel="icon" type="image/x-icon" href="assets/icon.jpg">
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-box me-2"></i>
                Products
            </h2>
            <div class="d-flex gap-2">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="productSearch" class="form-control" placeholder="Search products...">
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus me-2"></i>Add Product
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
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
                </div>
            </div>
        </div>

        <!-- Add Product Modal -->
        <div class="modal fade" id="addProductModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="productForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Unit Price (৳)</label>
                                <input type="number" name="unit_price" class="form-control" required min="0" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Restock Threshold</label>
                                <input type="number" name="restock_threshold" class="form-control" required min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Supplier</label>
                                <div class="input-group">
                                    <select name="company_id" class="form-select supplier-select" required>
                                        <option value="">Select a supplier</option>
                                        <?php
                                        try {
                                            $supplierQuery = "SELECT company_id, company_name FROM company ORDER BY company_name";
                                            $supplierStmt = $db->prepare($supplierQuery);
                                            $supplierStmt->execute();
                                            while ($supplier = $supplierStmt->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="'.$supplier['company_id'].'">'.htmlspecialchars($supplier['company_name']).'</option>';
                                            }
                                        } catch(PDOException $e) {
                                            echo '<option value="">Error loading suppliers</option>';
                                        }
                                        ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                                        <i class="fas fa-plus"></i> Add New Supplier
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div class="modal fade" id="editProductModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editProductForm">
                        <input type="hidden" name="product_id" id="editProductId">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="name" id="editProductName" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Unit Price (৳)</label>
                                <input type="number" name="unit_price" id="editUnitPrice" class="form-control" required min="0" step="0.01">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Restock Threshold</label>
                                <input type="number" name="restock_threshold" id="editRestockThreshold" class="form-control" required min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Supplier</label>
                                <div class="input-group">
                                    <select name="company_id" id="editCompanyId" class="form-select supplier-select" required>
                                        <option value="">Select a supplier</option>
                                        <?php
                                        try {
                                            $supplierQuery = "SELECT company_id, company_name FROM company ORDER BY company_name";
                                            $supplierStmt = $db->prepare($supplierQuery);
                                            $supplierStmt->execute();
                                            while ($supplier = $supplierStmt->fetch(PDO::FETCH_ASSOC)) {
                                                echo '<option value="'.$supplier['company_id'].'">'.htmlspecialchars($supplier['company_name']).'</option>';
                                            }
                                        } catch(PDOException $e) {
                                            echo '<option value="">Error loading suppliers</option>';
                                        }
                                        ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                                        <i class="fas fa-plus"></i> Add New Supplier
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Product</button>
                        </div>
                    </form>
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
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Common utility functions
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

        // Format supplier for Select2
        function formatSupplier(supplier) {
            if (!supplier.id) return supplier.text;
            return $('<span>' + supplier.text + '</span>');
        }

        // Initialize Select2 and search functionality
        $(document).ready(function() {
            // Initialize Select2 only when the modal is shown
            $('#editProductModal, #addProductModal').on('shown.bs.modal', function() {
                $(this).find('.supplier-select').each(function() {
                    // Destroy previous Select2 instance (if any)
                    if ($(this).hasClass("select2-hidden-accessible")) {
                        $(this).select2('destroy');
                    }

                    // Re-initialize with the correct dropdown parent
                    $(this).select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Search for a supplier...',
                        allowClear: true,
                        width: '100%',
                        language: {
                            searching: function() {
                                return "Searching...";
                            },
                            noResults: function() {
                                return "No suppliers found";
                            }
                        },
                        templateResult: formatSupplier,
                        templateSelection: formatSupplier,
                        dropdownParent: $(this).closest('.modal') // Dynamically set
                    });
                });
            });

            // Add product search functionality
            $('#productSearch').on('keyup', function() {
                const searchText = $(this).val().toLowerCase();
                $('table tbody tr').each(function() {
                    const rowText = $(this).text().toLowerCase();
                    $(this).toggle(rowText.indexOf(searchText) > -1);
                });
            });
        });

        // Add Product Form Handler
        document.addEventListener('DOMContentLoaded', function() {
            const addProductForm = document.getElementById('productForm');
            if (addProductForm) {
                addProductForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    
                    fetch('process_product.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Product added successfully!', 'success');
                            // Close the modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                            modal.hide();
                            // Refresh the page to show the new product
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showToast(data.message || 'Error adding product', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('An error occurred while adding the product', 'error');
                    });
                });
            }
        });

        // Edit Product Form Handler
        document.addEventListener('DOMContentLoaded', function() {
            const editProductForm = document.getElementById('editProductForm');
            if (editProductForm) {
                editProductForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    
                    fetch('process_product.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Product updated successfully!', 'success');
                            // Close the modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                            modal.hide();
                            // Refresh the page to show the updated product
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showToast(data.message || 'Error updating product', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('An error occurred while updating the product', 'error');
                    });
                });
            }
        });

        // Edit Product Function
        function editProduct(productId) {
            // Fetch product details
            fetch(`get_product.php?product_id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate the edit form
                        document.getElementById('editProductId').value = data.product.product_id;
                        document.getElementById('editProductName').value = data.product.name;
                        document.getElementById('editUnitPrice').value = data.product.unit_price;
                        document.getElementById('editRestockThreshold').value = data.product.restock_threshold;
                        
                        // Set the supplier in Select2
                        const supplierSelect = $('#editCompanyId');
                        supplierSelect.val(data.product.company_id).trigger('change');
                        
                        // Show the edit modal
                        const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
                        modal.show();
                    } else {
                        showToast(data.message || 'Error loading product details', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while loading product details', 'error');
                });
        }

        // Add Supplier Form Handler
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
                    
                    // Fetch latest suppliers data
                    fetch('get_suppliers_select.php')
                        .then(response => response.json())
                        .then(suppliers => {
                            // Clear and update both select2 dropdowns
                            $('.supplier-select').empty();
                            $('.supplier-select').append('<option value="">Select a supplier</option>');
                            
                            suppliers.forEach(supplier => {
                                const newOption = new Option(supplier.company_name, supplier.company_id, false, false);
                                $('.supplier-select').append(newOption);
                            });
                            
                            // Select the newly added supplier
                            $('.supplier-select').val(data.company_id).trigger('change');
                        });
                    
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addSupplierModal'));
                    modal.hide();

                    // Clear the form
                    this.reset();

                    // Refresh tables
                    refreshProductsTable();
                    refreshSuppliersTable();
                } else {
                    showToast(data.message || 'Error adding supplier', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while adding the supplier', 'error');
            });
        });

        // Function to refresh the products table
        function refreshProductsTable() {
            fetch('get_products.php')
                .then(response => response.text())
                .then(html => {
                    document.querySelector('.table-responsive').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error refreshing table:', error);
                    showToast('Error refreshing products table', 'error');
                });
        }

        // Function to refresh the suppliers table
        function refreshSuppliersTable() {
            fetch('get_suppliers.php')
                .then(response => response.text())
                .then(html => {
                    // Update the suppliers table in suppliers.php if it's open
                    const suppliersTable = document.querySelector('#suppliersTable .table-responsive');
                    if (suppliersTable) {
                        suppliersTable.innerHTML = html;
                    }
                })
                .catch(error => {
                    console.error('Error refreshing suppliers table:', error);
                });
        }
    </script>
</body>
</html>