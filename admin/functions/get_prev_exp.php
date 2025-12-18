<?php
require_once "../includes/auth_check.php";
require_once "../../config/db_conn.php";

header('Content-Type: application/json');

$graduate_id = isset($_GET['graduate_id']) ? (int)$_GET['graduate_id'] : 0;

if (!$graduate_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid graduate ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            experience_id,
            graduate_id,
            employment_type,
            company_name,
            nature_of_business,
            position,
            job_description,
            company_address,
            type_of_company,
            DATE_FORMAT(date_from, '%M %Y') as date_from,
            DATE_FORMAT(date_to, '%M %Y') as date_to,
            employment_status
        FROM previous_experiences 
        WHERE graduate_id = :graduate_id
        ORDER BY date_from DESC
    ");
    $stmt->execute([':graduate_id' => $graduate_id]);
    $experiences = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'experiences' => $experiences
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
