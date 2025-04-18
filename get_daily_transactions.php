<?php
session_start();
require_once 'assets/database.php';

$database = new Database();
$db = $database->getConnection();

$period = $_GET['period'] ?? 'weekly';
$dates = [];
$income = [];
$expenses = [];

switch ($period) {
    case 'weekly':
        // Get last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dates[] = date('M d', strtotime($date));
            
            // Get income for the day
            $income_query = "SELECT COALESCE(SUM(amount_paid), 0) as total 
                           FROM Transaction 
                           WHERE DATE(timestamp) = :date 
                           AND transaction_type IN ('Sale Payment', 'Debt Payment', 'Refund')";
            
            $stmt = $db->prepare($income_query);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $income[] = round((float)$result['total'], 2);
            
            // Get expenses for the day
            $expenses_query = "SELECT COALESCE(SUM(amount_paid), 0) as total 
                             FROM Transaction 
                             WHERE DATE(timestamp) = :date 
                             AND transaction_type IN ('Order Payment')";
            
            $stmt = $db->prepare($expenses_query);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $expenses[] = round((float)$result['total'], 2);
        }
        break;

    case 'monthly':
        // Get last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $dates[] = date('M Y', strtotime($date));
            
            // Get income for the month
            $income_query = "SELECT COALESCE(SUM(amount_paid), 0) as total 
                           FROM Transaction 
                           WHERE DATE_FORMAT(timestamp, '%Y-%m') = :date 
                           AND transaction_type IN ('Sale Payment', 'Debt Payment', 'Refund')";
            
            $stmt = $db->prepare($income_query);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $income[] = round((float)$result['total'], 2);
            
            // Get expenses for the month
            $expenses_query = "SELECT COALESCE(SUM(amount_paid), 0) as total 
                             FROM Transaction 
                             WHERE DATE_FORMAT(timestamp, '%Y-%m') = :date 
                             AND transaction_type IN ('Order Payment')";
            
            $stmt = $db->prepare($expenses_query);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $expenses[] = round((float)$result['total'], 2);
        }
        break;

    case 'yearly':
        // Get last 5 years
        for ($i = 4; $i >= 0; $i--) {
            $date = date('Y', strtotime("-$i years"));
            $dates[] = $date;
            
            // Get income for the year
            $income_query = "SELECT COALESCE(SUM(amount_paid), 0) as total 
                           FROM Transaction 
                           WHERE YEAR(timestamp) = :date 
                           AND transaction_type IN ('Sale Payment', 'Debt Payment', 'Refund')";
            
            $stmt = $db->prepare($income_query);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $income[] = round((float)$result['total'], 2);
            
            // Get expenses for the year
            $expenses_query = "SELECT COALESCE(SUM(amount_paid), 0) as total 
                             FROM Transaction 
                             WHERE YEAR(timestamp) = :date 
                             AND transaction_type IN ('Order Payment')";
            
            $stmt = $db->prepare($expenses_query);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $expenses[] = round((float)$result['total'], 2);
        }
        break;
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode([
    'dates' => $dates,
    'income' => $income,
    'expenses' => $expenses
]);
?>