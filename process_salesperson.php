<?php
session_start();
require_once 'assets/database.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get form data
    $supplier_id = $_POST['supplier_id'] ?? '';
    $salesperson_name = $_POST['salesperson_name'] ?? '';
    $salesperson_phone_number = $_POST['salesperson_phone_number'] ?? '';

    // Debug log
    error_log("Received data: supplier_id=$supplier_id, name=$salesperson_name, phone=$salesperson_phone_number");

    // Validate required fields
    if (empty($supplier_id) || empty($salesperson_name) || empty($salesperson_phone_number)) {
        throw new Exception('All fields are required');
    }

    // Start transaction
    $db->beginTransaction();

    // Get current active salesperson
    $currentSalespersonQuery = "SELECT salesperson_id FROM salesperson 
                              WHERE company_id = :company_id AND status = 'Active'";
    $currentStmt = $db->prepare($currentSalespersonQuery);
    $currentStmt->execute([':company_id' => $supplier_id]);
    $currentSalesperson = $currentStmt->fetch(PDO::FETCH_ASSOC);

    if ($currentSalesperson) {
        // Archive current salesperson
        $archiveQuery = "UPDATE salesperson 
                        SET status = 'Archived', 
                            archived_on = CURRENT_TIMESTAMP 
                        WHERE salesperson_id = :salesperson_id";
        $archiveStmt = $db->prepare($archiveQuery);
        $archiveStmt->execute([':salesperson_id' => $currentSalesperson['salesperson_id']]);
        error_log("Archived salesperson: " . $currentSalesperson['salesperson_id']);
    }

    // Insert new salesperson
    $newSalespersonQuery = "INSERT INTO salesperson (company_id, name, phone, status) 
                          VALUES (:company_id, :name, :phone, 'Active')";
    $newStmt = $db->prepare($newSalespersonQuery);
    $newStmt->execute([
        ':company_id' => $supplier_id,
        ':name' => $salesperson_name,
        ':phone' => $salesperson_phone_number
    ]);
    $newSalespersonId = $db->lastInsertId();
    error_log("Created new salesperson: " . $newSalespersonId);

    // Commit transaction
    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Salesperson updated successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction if it was started
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    error_log("Error updating salesperson: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 