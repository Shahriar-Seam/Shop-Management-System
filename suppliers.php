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
    <title>Suppliers Management</title>
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
                <i class="fas fa-truck me-2"></i>
                Suppliers
            </h2>
            <div>
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
                                            salesperson s ON c.company_id = s.company_id AND s.status = 'Active';

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
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
            window.closeOrderModal = function() {
                console.log('closeOrderModal called');
                bsModal.hide();
                modal.remove();
                showToast('Order placed successfully!', 'success');
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
    </script>
</body>
</html>