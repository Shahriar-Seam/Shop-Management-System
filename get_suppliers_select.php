<?php
session_start();
require_once 'assets/database.php';
$database = new Database();
$db = $database->getConnection();

header('Content-Type: application/json');

try {
    $query = "SELECT company_id, company_name FROM company ORDER BY company_name";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $suppliers = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $suppliers[] = [
            'company_id' => $row['company_id'],
            'company_name' => $row['company_name']
        ];
    }
    
    echo json_encode($suppliers);
} catch(PDOException $e) {
    echo json_encode([]);
}
?> 