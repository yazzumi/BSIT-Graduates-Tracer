<?php
require_once "includes/auth_check.php";
require_once "../config/db_conn.php";

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'create':
            // Create new employment record
            $stmt = $pdo->prepare("
                INSERT INTO employment_details (
                    graduate_id, employment_type, company_name, company_address,
                    type_of_company, position, date_from, date_to, employment_status,
                    job_description, has_previous_experience
                ) VALUES (
                    :graduate_id, :employment_type, :company_name, :company_address,
                    :type_of_company, :position, :date_from, :date_to, :employment_status,
                    :job_description, :has_previous_experience
                )
            ");
            
            $stmt->execute([
                ':graduate_id' => $_POST['graduate_id'],
                ':employment_type' => $_POST['employment_type'] ?? 'Current',
                ':company_name' => $_POST['company_name'],
                ':company_address' => $_POST['company_address'] ?? '',
                ':type_of_company' => $_POST['type_of_company'] ?? null,
                ':position' => $_POST['position'] ?? '',
                ':date_from' => $_POST['date_from'] ?? null,
                ':date_to' => $_POST['date_to'] ?? null,
                ':employment_status' => $_POST['employment_status'] ?? null,
                ':job_description' => $_POST['job_description'] ?? null,
                ':has_previous_experience' => isset($_POST['has_previous_experience']) ? 1 : 0
            ]);
            
            $_SESSION['success_message'] = 'Employment record added successfully!';
            header("Location: employed.php");
            exit();
            
        case 'update':
            // Update existing employment record
            $stmt = $pdo->prepare("
                UPDATE employment_details 
                SET graduate_id = :graduate_id,
                    employment_type = :employment_type,
                    company_name = :company_name,
                    company_address = :company_address,
                    type_of_company = :type_of_company,
                    position = :position,
                    date_from = :date_from,
                    date_to = :date_to,
                    employment_status = :employment_status,
                    job_description = :job_description,
                    has_previous_experience = :has_previous_experience
                WHERE employment_id = :employment_id
            ");
            
            $stmt->execute([
                ':employment_id' => $_POST['employment_id'],
                ':graduate_id' => $_POST['graduate_id'],
                ':employment_type' => $_POST['employment_type'] ?? 'Current',
                ':company_name' => $_POST['company_name'],
                ':company_address' => $_POST['company_address'] ?? '',
                ':type_of_company' => $_POST['type_of_company'] ?? null,
                ':position' => $_POST['position'] ?? '',
                ':date_from' => $_POST['date_from'] ?? null,
                ':date_to' => $_POST['date_to'] ?? null,
                ':employment_status' => $_POST['employment_status'] ?? null,
                ':job_description' => $_POST['job_description'] ?? null,
                ':has_previous_experience' => isset($_POST['has_previous_experience']) ? 1 : 0
            ]);
            
            $_SESSION['success_message'] = 'Employment record updated successfully!';
            header("Location: employed.php");
            exit();
            
        case 'delete':
            // Delete employment record
            $stmt = $pdo->prepare("DELETE FROM employment_details WHERE employment_id = :employment_id");
            $stmt->execute([':employment_id' => $_POST['employment_id']]);
            
            $_SESSION['success_message'] = 'Employment record deleted successfully!';
            header("Location: employed.php");
            exit();
            
        case 'get':
            // Get single employment record for editing
            $stmt = $pdo->prepare("
                SELECT employment_id, graduate_id, employment_type, company_name, 
                       company_address, type_of_company, position, date_from, date_to,
                       employment_status, job_description, has_previous_experience
                FROM employment_details 
                WHERE employment_id = :employment_id
            ");
            $stmt->execute([':employment_id' => $_GET['id']]);
            $employment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($employment) {
                echo json_encode(['success' => true, 'employment' => $employment]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Employment record not found']);
            }
            exit();
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit();
    }
    
} catch (PDOException $e) {
    error_log("CRUD Error: " . $e->getMessage());
    
    if (in_array($action, ['create', 'update', 'delete'])) {
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
        header("Location: employed.php");
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}
?>
