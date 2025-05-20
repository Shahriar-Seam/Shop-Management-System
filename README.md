# Shop Management System

A comprehensive web-based shop management system built with PHP and MySQL, designed to help businesses manage their inventory, sales, customers, suppliers, and transactions efficiently.

## Features

- Product Management
- Sales Tracking
- Customer Management
- Supplier Management
- Order Processing
- Low Stock Alerts
- Damaged Products Tracking
- Transaction History
- Sales Reports
- Customer Debt Management

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web Server (Apache/WAMP/XAMPP)

## Installation

1. **Set up your web server**
   - If you're using WAMP:
     - Make sure WAMP is installed and running
     - Place the project files in the `www` directory (e.g., `C:/wamp64/www/Shop-Management-System`)

2. **Database Setup**

   You can set up the database using phpMyAdmin (GUI Method) or MySQL command line.

   ### Using phpMyAdmin (Recommended)
   1. Open phpMyAdmin in your browser:
      - If using WAMP: Click on the WAMP icon in the system tray → phpMyAdmin
      - Or visit: `http://localhost/phpmyadmin`
   
   2. Login to phpMyAdmin (default username is 'root' with no password for WAMP)
   
   3. Create a new database:
      - Click "New" in the left sidebar
      - Enter "shop_db" as the database name
      - Click "Create"
   
   4. Import the database schema:
      - Select the "shop_db" database from the left sidebar
      - Click the "Import" tab at the top
      - Click "Choose File" and select `database/schema.sql`
      - Scroll down to "other options"
      - Uncheck the box for "Enable foreign key checks"
      - Click "Import"
   
   5. (Optional) Import sample data:
      - Repeat the import process
      - Select `database/sample-data.sql`
      - Make sure "Enable foreign key checks" is unchecked
      - Click "Import"

   ### Using MySQL Command Line (Alternative)
   ```bash
   # Log in to MySQL and create a new database
   mysql -u root -p
   CREATE DATABASE shop_db;
   USE shop_db;
   
   # Import the database schema
   mysql -u root -p shop_db < database/schema.sql
   
   # (Optional) Import sample data
   mysql -u root -p shop_db < database/sample-data.sql
   ```

3. **Configure Database Connection**
   - Open `assets/database.php` and update the database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');  // default 'root' for WAMP
     define('DB_PASS', 'your_password');  // default '' (empty) for WAMP
     define('DB_NAME', 'shop_db');
     ```

## Running the Application

1. Start your WAMP/XAMPP server
2. Open your web browser and navigate to:
   ```
   http://localhost/Shop-Management-System/
   ```

## Directory Structure

- `assets/` - Contains CSS, JavaScript, and image files
  - `database.php` - Database configuration and connection
  - `style.css` - Stylesheet files
  - `script.js` - JavaScript files
  - `image.png` - Image assets (logo)

- `database/` - Contains database schema and sample data
  - `schema.sql` - Database structure and tables
  - `sample-data.sql` - Sample data for testing

- Core PHP Files:
  - `index.php` - Main entry point and dashboard
  - `products.php` - Product management and inventory
  - `sales.php` - Sales management and tracking
  - `customers.php` - Customer information and management
  - `suppliers.php` - Supplier management and details
  - `order.php` - Order processing and management
  - `transactions.php` - Financial transactions tracking
  - `low_stock.php` - Low stock alerts and management
  - `damaged_products.php` - Damaged products tracking
  - `template.php` - Common template and layout

- Process Files:
  - `process_order.php` - Order processing logic
  - `process_supplier.php` - Supplier data processing
  - `process_salesperson.php` - Salesperson data processing
  - `process_product.php` - Product data processing
  - `process_sale.php` - Sale transaction processing
  - `process_debt_payment.php` - Debt payment processing
  - `process_customer.php` - Customer data processing
  - `process_damage.php` - Damage report processing
  - `process_action.php` - General action processing

- API Endpoints:
  - `get_supplier_products.php` - Fetch supplier products
  - `get_sales_history.php` - Retrieve sales history
  - `get_debt_history.php` - Fetch debt records
  - `get_unpaid_sales.php` - List unpaid sales
  - `get_suppliers_select.php` - Supplier selection data
  - `get_suppliers.php` - Supplier listing
  - `get_salesperson_history.php` - Salesperson records
  - `get_products.php` - Product listing
  - `get_salesperson.php` - Salesperson data
  - `get_product.php` - Product details
  - `get_order_history.php` - Order history
  - `get_low_stock_products.php` - Low stock items
  - `get_order_details.php` - Order information
  - `get_daily_transactions.php` - Daily transaction records
  - `get_damaged_products.php` - Damaged items list
  - `get_customers.php` - Customer listing
  - `get_customer_debt.php` - Customer debt information

## Authors

This project is built by students from the Computer Science and Engineering discipline, Khulna University:

- **Ibnul Abrar Shahriar Seam**
  - Student ID: 230201

- **Tarique Shams**
  - Student ID: 230229

- **Md. Mustafizur Rahman**
  - Student ID: 230233

