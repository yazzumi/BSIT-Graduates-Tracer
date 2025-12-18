<?php
require_once __DIR__ . "/../includes/auth_check.php";
require_once __DIR__ . "/../../config/db_conn.php";

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'create':
            // Create new graduate
            $stmt = $pdo->prepare("
                INSERT INTO graduates (first_name, middle_name, last_name, permanent_address, barangay, 
                    city_municipality, province, contact_number, gender, date_of_birth, civil_status)
                VALUES (:first_name, :middle_name, :last_name, :permanent_address, :barangay,
                    :city_municipality, :province, :contact_number, :gender, :date_of_birth, :civil_status)
            ");
            
            $stmt->execute([
                ':first_name' => $_POST['first_name'],
                ':middle_name' => $_POST['middle_name'] ?? null,
                ':last_name' => $_POST['last_name'],
                ':permanent_address' => $_POST['permanent_address'] ?? null,
                ':barangay' => $_POST['barangay'] ?? null,
                ':city_municipality' => $_POST['city_municipality'] ?? '',
                ':province' => $_POST['province'] ?? '',
                ':contact_number' => $_POST['contact_number'] ?? '',
                ':gender' => $_POST['gender'] ?? '',
                ':date_of_birth' => $_POST['date_of_birth'] ?? null,
                ':civil_status' => $_POST['civil_status'] ?? ''
            ]);
            
            $_SESSION['success_message'] = 'Graduate added successfully!';
            header("Location: ../graduates.php");
            exit();
            
        case 'update':
            // Update existing graduate
            $stmt = $pdo->prepare("
                UPDATE graduates 
                SET first_name = :first_name,
                    middle_name = :middle_name,
                    last_name = :last_name,
                    permanent_address = :permanent_address,
                    barangay = :barangay,
                    city_municipality = :city_municipality,
                    province = :province,
                    contact_number = :contact_number,
                    gender = :gender,
                    date_of_birth = :date_of_birth,
                    civil_status = :civil_status
                WHERE graduate_id = :graduate_id
            ");
            
            $stmt->execute([
                ':graduate_id' => $_POST['graduate_id'],
                ':first_name' => $_POST['first_name'],
                ':middle_name' => $_POST['middle_name'] ?? null,
                ':last_name' => $_POST['last_name'],
                ':permanent_address' => $_POST['permanent_address'] ?? null,
                ':barangay' => $_POST['barangay'] ?? null,
                ':city_municipality' => $_POST['city_municipality'] ?? '',
                ':province' => $_POST['province'] ?? '',
                ':contact_number' => $_POST['contact_number'] ?? '',
                ':gender' => $_POST['gender'] ?? '',
                ':date_of_birth' => $_POST['date_of_birth'] ?? null,
                ':civil_status' => $_POST['civil_status'] ?? ''
            ]);
            
            $_SESSION['success_message'] = 'Graduate updated successfully!';
            header("Location: ../graduates.php");
            exit();
            
        case 'delete':
            // Delete graduate
            $pdo->beginTransaction();
            
            // Delete from validated_graduates first (foreign key constraint)
            $stmt = $pdo->prepare("DELETE FROM validated_graduates WHERE graduate_id = :graduate_id");
            $stmt->execute([':graduate_id' => $_POST['graduate_id']]);
            
            // Delete employment details
            $stmt = $pdo->prepare("DELETE FROM employment_details WHERE graduate_id = :graduate_id");
            $stmt->execute([':graduate_id' => $_POST['graduate_id']]);
            
            $stmt = $pdo->prepare("DELETE FROM ofw_details WHERE graduate_id = :graduate_id");
            $stmt->execute([':graduate_id' => $_POST['graduate_id']]);
            
            $stmt = $pdo->prepare("DELETE FROM self_employment_details WHERE graduate_id = :graduate_id");
            $stmt->execute([':graduate_id' => $_POST['graduate_id']]);
            
            $stmt = $pdo->prepare("DELETE FROM unemployed_details WHERE graduate_id = :graduate_id");
            $stmt->execute([':graduate_id' => $_POST['graduate_id']]);
            
            $stmt = $pdo->prepare("DELETE FROM previous_experiences WHERE graduate_id = :graduate_id");
            $stmt->execute([':graduate_id' => $_POST['graduate_id']]);
            
            // Finally delete the graduate
            $stmt = $pdo->prepare("DELETE FROM graduates WHERE graduate_id = :graduate_id");
            $stmt->execute([':graduate_id' => $_POST['graduate_id']]);
            
            $pdo->commit();
            
            $_SESSION['success_message'] = 'Graduate deleted successfully!';
            header("Location: ../graduates.php");
            exit();
            
        case 'get':
            // Get single graduate for editing
            $stmt = $pdo->prepare("
                SELECT graduate_id, first_name, middle_name, last_name, 
                       permanent_address, barangay, city_municipality, province,
                       contact_number, gender, date_of_birth, civil_status, employment_type
                FROM graduates 
                WHERE graduate_id = :graduate_id
            ");
            $stmt->execute([':graduate_id' => $_GET['id']]);
            $graduate = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($graduate) {
                echo json_encode(['success' => true, 'graduate' => $graduate]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Graduate not found']);
            }
            exit();
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit();
    }
    
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("CRUD Error: " . $e->getMessage());
    
    if (in_array($action, ['create', 'update', 'delete'])) {
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
        header("Location: ../graduates.php");
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}
?>
