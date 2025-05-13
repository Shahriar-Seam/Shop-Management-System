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
    <title>Customers</title>
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Customers</h2>
            <div class="d-flex gap-2">
                <div class="input-group" style="width: 300px;">
                    <input type="text" class="form-control" id="searchCustomer" placeholder="Search customers...">
                    <button class="btn btn-outline-secondary" type="button" id="searchButton">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                    <i class="fas fa-plus me-2"></i>Add Customer
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Contact Info</th>
                                <th>Current Debt</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="customersTableBody">
                            <?php
                            try {
                                $query = "SELECT 
                                            c.customer_id,
                                            c.name,
                                            c.contact_info,
                                            COALESCE(SUM(s.total_amount - s.discount_amount), 0) AS total_owed,
                                            COALESCE((
                                                SELECT SUM(amount_paid)
                                                FROM Transaction 
                                                WHERE transaction_type = 'Sale Payment' 
                                                AND reference_id IN (SELECT sale_id FROM Sale WHERE customer_id = c.customer_id)
                                            ), 0) 
                                            + COALESCE((
                                                SELECT SUM(amount_paid)
                                                FROM Transaction 
                                                WHERE transaction_type = 'Debt Payment' 
                                                AND reference_id = c.customer_id
                                            ), 0) AS total_paid,
                                            (COALESCE(SUM(s.total_amount - s.discount_amount), 0) 
                                             - 
                                             (
                                                COALESCE((
                                                    SELECT SUM(amount_paid)
                                                    FROM Transaction 
                                                    WHERE transaction_type = 'Sale Payment' 
                                                    AND reference_id IN (SELECT sale_id FROM Sale WHERE customer_id = c.customer_id)
                                                ), 0) 
                                                + COALESCE((
                                                    SELECT SUM(amount_paid)
                                                    FROM Transaction 
                                                    WHERE transaction_type = 'Debt Payment' 
                                                    AND reference_id = c.customer_id
                                                ), 0)
                                             )
                                            ) AS total_debt_remaining
                                        FROM 
                                            Customer c
                                        LEFT JOIN 
                                            Sale s ON s.customer_id = c.customer_id
                                        GROUP BY 
                                            c.customer_id, c.name, c.contact_info
                                        ORDER BY 
                                            total_debt_remaining DESC, c.name";

                                $stmt = $db->prepare($query);
                                $stmt->execute();

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $currentDebt = $row['total_debt_remaining'];
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['name']) ?></td>
                                        <td><?= htmlspecialchars($row['contact_info']) ?></td>
                                        <td>
                                            <span class="badge <?= $currentDebt > 0 ? 'bg-danger' : 'bg-success' ?>">
                                                ৳<?= number_format($currentDebt, 2) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info me-2" onclick="viewDebtHistory(<?= $row['customer_id'] ?>)">
                                                <i class="fas fa-history"></i> History
                                            </button>
                                            <button class="btn btn-sm btn-success" onclick="addPayment(<?= $row['customer_id'] ?>)">
                                                <i class="fas fa-money-bill"></i> Add Payment
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } catch(PDOException $e) {
                                echo "<tr><td colspan='4' class='text-center text-danger'>Error loading customers</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Customer Modal -->
        <div class="modal fade" id="addCustomerModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="customerForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Information</label>
                                <input type="text" name="contact_info" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Payment Modal -->
        <div class="modal fade" id="addPaymentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Payment for <span id="paymentCustomerName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="paymentForm">
                            <input type="hidden" id="paymentCustomerId">
                            <div class="mb-3">
                                <label class="form-label">Amount</label>
                                <input type="number" class="form-control" id="paymentAmount" step="0.01" min="0.01" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitPayment()">Add Payment</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Debt History Modal -->
        <div class="modal fade" id="debtHistoryModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="debtHistoryModalLabel">Debt History</h5>
                        &nbsp;
                        <button type="button" class="btn btn-primary" onclick="printDebtHistory()">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="debt-summary mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Total Owed</h6>
                                            <p class="card-text" id="totalOwed">৳0.00</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Total Paid</h6>
                                            <p class="card-text" id="totalPaid">৳0.00</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Remaining Debt</h6>
                                            <p class="card-text" id="remainingDebt">৳0.00</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 10%">Date</th>
                                        <th style="width: 8%">Type</th>
                                        <th style="width: 25%">Items</th>
                                        <th style="width: 8%">Sale</th>
                                        <th style="width: 8%">Discount</th>
                                        <th style="width: 8%">Owed</th>
                                        <th style="width: 8%">Sale Pay</th>
                                        <th style="width: 8%">Debt Pay</th>
                                        <th style="width: 8%">Total Paid</th>
                                        <th style="width: 9%">Remaining</th>
                                    </tr>
                                </thead>
                                <tbody id="debtHistoryBody">
                                    <!-- Debt history will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form submission handler for adding customer
        document.getElementById('customerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('process_customer.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Customer added successfully!', 'success');
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addCustomerModal'));
                    modal.hide();
                    // Refresh the page to show the new customer
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast(data.message || 'Error adding customer', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while adding the customer', 'error');
            });
        });

        // Add search functionality
        let searchTimeout;
        const searchInput = document.getElementById('searchCustomer');
        const searchButton = document.getElementById('searchButton');

        function performSearch() {
            const searchTerm = searchInput.value.trim();
            fetch(`get_customers.php?search=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('customersTableBody');
                        tbody.innerHTML = '';
                        
                        data.customers.forEach(customer => {
                            tbody.innerHTML += `
                                <tr>
                                    <td>${customer.name}</td>
                                    <td>${customer.contact_info}</td>
                                    <td>
                                        <span class="badge ${customer.total_debt_remaining > 0 ? 'bg-danger' : 'bg-success'}">
                                            ৳${Number(customer.total_debt_remaining).toFixed(2)}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-2" onclick="viewDebtHistory(${customer.customer_id})">
                                            <i class="fas fa-history me-1"></i>History
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="addPayment(${customer.customer_id})">
                                            <i class="fas fa-money-bill me-1"></i>Add Payment
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        showToast('Error searching customers', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while searching customers', 'error');
                });
        }

        // Add event listeners for search
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });

        searchButton.addEventListener('click', performSearch);

        // Modify the existing fetchCustomers function to accept search parameter
        function fetchCustomers(searchTerm = '') {
            fetch(`get_customers.php?search=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('customersTableBody');
                        tbody.innerHTML = '';
                        
                        data.customers.forEach(customer => {
                            tbody.innerHTML += `
                                <tr>
                                    <td>${customer.name}</td>
                                    <td>${customer.contact_info}</td>
                                    <td>
                                        <span class="badge ${customer.total_debt_remaining > 0 ? 'bg-danger' : 'bg-success'}">
                                            ৳${Number(customer.total_debt_remaining).toFixed(2)}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-2" onclick="viewDebtHistory(${customer.customer_id})">
                                            <i class="fas fa-history me-1"></i>History
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="addPayment(${customer.customer_id})">
                                            <i class="fas fa-money-bill me-1"></i>Add Payment
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        showToast('Error loading customers', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while loading customers', 'error');
                });
        }

        function viewDebtHistory(customerId) {
            // Show loading state
            document.getElementById('debtHistoryBody').innerHTML = '<tr><td colspan="9" class="text-center">Loading...</td></tr>';
            
            // Fetch debt summary
            fetch(`get_customer_debt.php?customer_id=${customerId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('totalOwed').textContent = '৳' + data.debt.total_owed;
                        document.getElementById('totalPaid').textContent = '৳' + data.debt.total_paid;
                        document.getElementById('remainingDebt').textContent = '৳' + data.debt.total_debt_remaining;
                        // Update modal title with customer name
                        document.getElementById('debtHistoryModalLabel').textContent = `Debt History - ${data.debt.customer_name}`;
                    }
                });

            // Fetch debt history
            fetch(`get_debt_history.php?customer_id=${customerId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let html = '';
                        data.history.forEach(record => {
                            html += `
                                <tr>
                                    <td>${record.transaction_date}</td>
                                    <td>${record.transaction_type}</td>
                                    <td>${record.items.split('||').join('<br>')}</td>
                                    <td>${record.total_amount}</td>
                                    <td>${record.discount_amount}</td>
                                    <td>${record.amount_owed}</td>
                                    <td>${record.sale_payment}</td>
                                    <td>${record.debt_payment}</td>
                                    <td>${record.total_paid}</td>
                                    <td>${record.remaining_due}</td>
                                </tr>
                            `;
                        });
                        document.getElementById('debtHistoryBody').innerHTML = html || '<tr><td colspan="9" class="text-center">No records found</td></tr>';
                    }
                });

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('debtHistoryModal'));
            modal.show();
        }

        function addPayment(customerId) {
            // Get the customer's row and remaining debt
            const customerRow = document.querySelector(`button[onclick="addPayment(${customerId})"]`).closest('tr');
            const customerName = customerRow.querySelector('td:first-child').textContent;
            const remainingDebt = parseFloat(customerRow.querySelector('.badge').textContent.replace('৳', '').replace(',', '')) || 0;
            
            showAddPaymentModal(customerId, customerName);
            document.getElementById('paymentAmount').setAttribute('max', remainingDebt);
        }

        function submitPayment() {
            const formData = new FormData();
            const amountInput = document.getElementById('paymentAmount');
            const amount = parseFloat(amountInput.value) || 0;
            const customerId = document.getElementById('paymentCustomerId').value;
            const maxAmount = parseFloat(amountInput.getAttribute('max')) || 0;

            // Reset validation state
            amountInput.classList.remove('is-invalid');
            const existingFeedback = amountInput.nextElementSibling;
            if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                existingFeedback.remove();
            }

            // Validate amount
            if (amount <= 0) {
                amountInput.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Amount must be greater than 0';
                amountInput.parentNode.appendChild(feedback);
                return;
            }

            if (amount > maxAmount) {
                amountInput.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Amount cannot exceed remaining debt';
                amountInput.parentNode.appendChild(feedback);
                return;
            }

            formData.append('customer_id', customerId);
            formData.append('amount', amount);
            formData.append('transaction_type', 'Debt Payment');

            fetch('process_debt_payment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addPaymentModal'));
                    modal.hide();
                    
                    showToast('Payment added successfully', 'success');
                    
                    // Refresh the page to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast(data.message || 'Error adding payment', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while adding the payment', 'error');
            });
        }

        // Add real-time validation for payment amount
        document.getElementById('paymentAmount')?.addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            const maxAmount = parseFloat(this.getAttribute('max')) || 0;
            
            // Reset validation state
            this.classList.remove('is-invalid');
            const existingFeedback = this.nextElementSibling;
            if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
                existingFeedback.remove();
            }

            if (amount <= 0) {
                this.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Amount must be greater than 0';
                this.parentNode.appendChild(feedback);
            } else if (amount > maxAmount) {
                this.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Amount cannot exceed remaining debt';
                this.parentNode.appendChild(feedback);
            }
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

        function showAddPaymentModal(customerId, customerName) {
            document.getElementById('paymentCustomerId').value = customerId;
            document.getElementById('paymentCustomerName').textContent = customerName;
            document.getElementById('paymentAmount').value = '';
            
            const modal = new bootstrap.Modal(document.getElementById('addPaymentModal'));
            modal.show();
        }

        function printDebtHistory() {
            const printWindow = window.open('', '_blank');
            
            const printContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Debt History</title>
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
                        .debt-summary {
                            margin-bottom: 30px;
                        }
                        .summary-cards {
                            display: flex;
                            justify-content: space-between;
                            margin-bottom: 20px;
                        }
                        .summary-card {
                            flex: 1;
                            padding: 15px;
                            background-color: #f8f9fa;
                            border-radius: 5px;
                            margin: 0 10px;
                        }
                        .summary-card:first-child {
                            margin-left: 0;
                        }
                        .summary-card:last-child {
                            margin-right: 0;
                        }
                        .summary-label {
                            font-weight: bold;
                            color: #666;
                            margin-bottom: 5px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 20px 0;
                            font-size: 14px;
                        }
                        th, td {
                            padding: 8px;
                            text-align: left;
                            border: 1px solid #ddd;
                            vertical-align: top;
                        }
                        th {
                            background-color: #f5f5f5;
                            font-weight: bold;
                            white-space: nowrap;
                        }
                        .items-cell {
                            white-space: pre-line;
                            max-width: 300px;
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
                        @media print {
                            @page {
                                size: landscape;
                                margin: 10mm;
                            }
                            body {
                                margin: 0;
                                padding: 0;
                            }
                            .header {
                                margin-bottom: 10px;
                            }
                            .debt-summary {
                                margin-bottom: 15px;
                            }
                            .summary-cards {
                                margin-bottom: 10px;
                            }
                            .summary-card {
                                padding: 8px;
                                margin: 0 5px;
                            }
                            table {
                                margin: 10px 0;
                            }
                            th, td {
                                padding: 4px;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Debt History</h1>
                        <h2>${document.getElementById('debtHistoryModalLabel').textContent}</h2>
                    </div>

                    <div class="debt-summary">
                        <div class="summary-cards">
                            <div class="summary-card">
                                <div class="summary-label">Total Owed</div>
                                <div>${document.getElementById('totalOwed').textContent}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-label">Total Paid</div>
                                <div>${document.getElementById('totalPaid').textContent}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-label">Remaining Debt</div>
                                <div>${document.getElementById('remainingDebt').textContent}</div>
                            </div>
                        </div>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th style="width: 10%">Date</th>
                                <th style="width: 8%">Type</th>
                                <th style="width: 25%">Items</th>
                                <th style="width: 8%">Sale</th>
                                <th style="width: 8%">Discount</th>
                                <th style="width: 8%">Owed</th>
                                <th style="width: 8%">Sale Pay</th>
                                <th style="width: 8%">Debt Pay</th>
                                <th style="width: 8%">Total Paid</th>
                                <th style="width: 9%">Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${Array.from(document.getElementById('debtHistoryBody').querySelectorAll('tr'))
                                .map(row => {
                                    const cells = Array.from(row.cells);
                                    const itemsCell = cells[2];
                                    return `<tr>
                                        <td>${cells[0].textContent}</td>
                                        <td>${cells[1].textContent}</td>
                                        <td class="items-cell">${itemsCell.innerHTML.replace(/<br>/g, '\n')}</td>
                                        <td>${cells[3].textContent}</td>
                                        <td>${cells[4].textContent}</td>
                                        <td>${cells[5].textContent}</td>
                                        <td>${cells[6].textContent}</td>
                                        <td>${cells[7].textContent}</td>
                                        <td>${cells[8].textContent}</td>
                                        <td>${cells[9].textContent}</td>
                                    </tr>`;
                                })
                                .join('')}
                        </tbody>
                    </table>

                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="signature-label">Authorized Signature</div>
                    </div>
                </body>
                </html>
            `;
            
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html> 