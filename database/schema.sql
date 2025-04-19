-- Shop Management System Database Schema
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Company Table
-- -----------------------------------------------------

CREATE TABLE Company (
    company_id INT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(255) NOT NULL,
    company_phone_number VARCHAR(20),
    company_address VARCHAR(255)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Salesperson Table
-- -----------------------------------------------------

CREATE TABLE Salesperson (
    salesperson_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    status ENUM('Active', 'Archived') NOT NULL DEFAULT 'Active',
    archived_on DATETIME,
    company_id INT NOT NULL,

    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Customer Table (Only for debt tracking)
-- -----------------------------------------------------

CREATE TABLE Customer (
    customer_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    contact_info VARCHAR(255)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Product Table
-- -----------------------------------------------------

CREATE TABLE Product (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    company_id INT NOT NULL,
    stock_quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    restock_threshold INT,

    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- ProductOrder Table (Renamed from Order)
-- -----------------------------------------------------

CREATE TABLE ProductOrder (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    order_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- OrderDetail Table
-- -----------------------------------------------------

CREATE TABLE OrderDetail (
    order_detail_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    cost DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (order_id) REFERENCES ProductOrder(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Product(product_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Sale Table
-- -----------------------------------------------------

CREATE TABLE Sale (
    sale_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT DEFAULT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    sale_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES Customer(customer_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- SaleDetail Table
-- -----------------------------------------------------

CREATE TABLE SaleDetail (
    sale_detail_id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_per_unit DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (sale_id) REFERENCES Sale(sale_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Product(product_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Damage Table
-- -----------------------------------------------------

CREATE TABLE DamagedProduct (
    damage_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    quantity_damaged INT NOT NULL,
    damage_cost DECIMAL(10,2) NOT NULL,
    refund_amount DECIMAL(10,2),
    action_taken ENUM('Refunded', 'Replaced', 'No Action') NOT NULL DEFAULT 'No Action',
    reason TEXT,
    damage_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id) REFERENCES Product(product_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Transaction Table
-- -----------------------------------------------------

CREATE TABLE Transaction (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    reference_id INT NOT NULL,
    transaction_type ENUM('Order Payment', 'Sale Payment', 'Refund', 'Debt Payment') NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
