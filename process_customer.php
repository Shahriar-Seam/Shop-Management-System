<?php
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();

header('Content-Type: application/json');

try {
    // Get form data
    $name = $_POST['name'] ?? '';
    $contact_info = $_POST['contact_info'] ?? '';

    // Validate required fields
    if (empty($name) || empty($contact_info)) {
        throw new Exception('All fields are required');
    }

    // Insert new customer
    $query = "INSERT INTO Customer (name, contact_info, debt_amount) VALUES (:name, :contact_info, 0)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':contact_info', $contact_info);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 