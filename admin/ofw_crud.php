<?php
require_once "includes/auth_check.php";
require_once "../config/db_conn.php";

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'create':
            // Create new OFW record
            $stmt = $pdo->prepare("
                INSERT INTO ofw_details (
                    graduate_id, company_name, company_address, type_of_company,
                    position, date_from, date_to, employment_status,
                    has_previous_experience, country
                ) VALUES (
                    :graduate_id, :company_name, :company_address, :type_of_company,
                    :position, :date_from, :date_to, :employment_status,
                    :has_previous_experience, :country
                )
            ");
            
            $stmt->execute([
                ':graduate_id' => $_POST['graduate_id'],
                ':company_name' => $_POST['company_name'] ?? '',
                ':company_address' => $_POST['company_address'] ?? '',
                ':type_of_company' => $_POST['type_of_company'] ?? null,
                ':position' => $_POST['position'] ?? '',
                ':date_from' => $_POST['date_from'] ?? null,
                ':date_to' => $_POST['date_to'] ?? null,
                ':employment_status' => $_POST['employment_status'] ?? null,
                ':has_previous_experience' => isset($_POST['has_previous_experience']) ? 1 : 0,
                ':country' => $_POST['country'] ?? ''
            ]);
            
            // Update graduates table to set employment_type
            $update_stmt = $pdo->prepare("
                UPDATE graduates 
                SET employment_type = 'OFW'
                WHERE graduate_id = :graduate_id
            ");
            $update_stmt->execute([':graduate_id' => $_POST['graduate_id']]);
            
            $_SESSION['success_message'] = 'OFW record added successfully!';
            header("Location: ofw.php");
            exit();
            
        case 'update':
            // Update existing OFW record
            $stmt = $pdo->prepare("
                UPDATE ofw_details 
                SET graduate_id = :graduate_id,
                    company_name = :company_name,
                    company_address = :company_address,
                    type_of_company = :type_of_company,
                    position = :position,
                    date_from = :date_from,
                    date_to = :date_to,
                    employment_status = :employment_status,
                    has_previous_experience = :has_previous_experience,
                    country = :country
                WHERE ofw_id = :ofw_id
            ");
            
            $stmt->execute([
                ':ofw_id' => $_POST['ofw_id'],
                ':graduate_id' => $_POST['graduate_id'],
                ':company_name' => $_POST['company_name'] ?? '',
                ':company_address' => $_POST['company_address'] ?? '',
                ':type_of_company' => $_POST['type_of_company'] ?? null,
                ':position' => $_POST['position'] ?? '',
                ':date_from' => $_POST['date_from'] ?? null,
                ':date_to' => $_POST['date_to'] ?? null,
                ':employment_status' => $_POST['employment_status'] ?? null,
                ':has_previous_experience' => isset($_POST['has_previous_experience']) ? 1 : 0,
                ':country' => $_POST['country'] ?? ''
            ]);
            
            $_SESSION['success_message'] = 'OFW record updated successfully!';
            header("Location: ofw.php");
            exit();
            
        case 'delete':
            // Delete OFW record
            $stmt = $pdo->prepare("DELETE FROM ofw_details WHERE ofw_id = :ofw_id");
            $stmt->execute([':ofw_id' => $_POST['ofw_id']]);
            
            $_SESSION['success_message'] = 'OFW record deleted successfully!';
            header("Location: ofw.php");
            exit();
            
        case 'get':
            // Get single OFW record for editing
            $stmt = $pdo->prepare("
                SELECT ofw_id, graduate_id, company_name, company_address, 
                       type_of_company, position, date_from, date_to,
                       employment_status, has_previous_experience, country
                FROM ofw_details 
                WHERE ofw_id = :ofw_id
            ");
            $stmt->execute([':ofw_id' => $_GET['id']]);
            $ofw = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($ofw) {
                echo json_encode(['success' => true, 'ofw' => $ofw]);
            } else {
                echo json_encode(['success' => false, 'message' => 'OFW record not found']);
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
        header("Location: ofw.php");
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}
?>
