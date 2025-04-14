<?php
session_start();
require_once 'assets/database.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get form data
    $name = $_POST['name'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $address = $_POST['address'] ?? '';
    $salesperson_name = $_POST['salesperson_name'] ?? '';
    $salesperson_phone_number = $_POST['salesperson_phone_number'] ?? '';

    // Validate required fields
    if (empty($name) || empty($contact_number) || empty($salesperson_name) || empty($salesperson_phone_number)) {
        throw new Exception('All fields are required except address');
    }

    // Start transaction
    $db->beginTransaction();

    // Insert company
    $companyQuery = "INSERT INTO company (company_name, company_phone_number, company_address) 
                    VALUES (:name, :phone, :address)";
    $companyStmt = $db->prepare($companyQuery);
    $companyStmt->execute([
        ':name' => $name,
        ':phone' => $contact_number,
        ':address' => $address
    ]);
    $companyId = $db->lastInsertId();

    // Insert salesperson
    $salespersonQuery = "INSERT INTO salesperson (company_id, name, phone, status) 
                        VALUES (:company_id, :name, :phone, 'Active')";
    $salespersonStmt = $db->prepare($salespersonQuery);
    $salespersonStmt->execute([
        ':company_id' => $companyId,
        ':name' => $salesperson_name,
        ':phone' => $salesperson_phone_number
    ]);

    // Commit transaction
    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Supplier added successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction if it was started
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 