<?php
require_once __DIR__ . "/../includes/auth_check.php";
require_once __DIR__ . "/../../config/db_conn.php";

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

try {
    switch ($action) {
        case 'create':
            // Create new self-employment record
            $stmt = $pdo->prepare("
                INSERT INTO self_employment_details (
                    graduate_id, nature_of_business, place_of_business,
                    date_from, date_to, has_previous_experience
                ) VALUES (
                    :graduate_id, :nature_of_business, :place_of_business,
                    :date_from, :date_to, :has_previous_experience
                )
            ");
            
            $stmt->execute([
                ':graduate_id' => $_POST['graduate_id'],
                ':nature_of_business' => $_POST['nature_of_business'] ?? '',
                ':place_of_business' => $_POST['place_of_business'] ?? '',
                ':date_from' => $_POST['date_from'] ?? null,
                ':date_to' => $_POST['date_to'] ?? null,
                ':has_previous_experience' => isset($_POST['has_previous_experience']) ? 1 : 0
            ]);
            
            // Update graduates table to set employment_type
            $update_stmt = $pdo->prepare("
                UPDATE graduates 
                SET employment_type = 'Self-Employed'
                WHERE graduate_id = :graduate_id
            ");
            $update_stmt->execute([':graduate_id' => $_POST['graduate_id']]);
            
            $_SESSION['success_message'] = 'Self-employment record added successfully!';
            header("Location: ../self_employed.php");
            exit();
            
        case 'update':
            // Update existing self-employment record
            $stmt = $pdo->prepare("
                UPDATE self_employment_details 
                SET graduate_id = :graduate_id,
                    nature_of_business = :nature_of_business,
                    place_of_business = :place_of_business,
                    date_from = :date_from,
                    date_to = :date_to,
                    has_previous_experience = :has_previous_experience
                WHERE self_employment_id = :self_employment_id
            ");
            
            $stmt->execute([
                ':self_employment_id' => $_POST['self_employment_id'],
                ':graduate_id' => $_POST['graduate_id'],
                ':nature_of_business' => $_POST['nature_of_business'] ?? '',
                ':place_of_business' => $_POST['place_of_business'] ?? '',
                ':date_from' => $_POST['date_from'] ?? null,
                ':date_to' => $_POST['date_to'] ?? null,
                ':has_previous_experience' => isset($_POST['has_previous_experience']) ? 1 : 0
            ]);
            
            $_SESSION['success_message'] = 'Self-employment record updated successfully!';
            header("Location: ../self_employed.php");
            exit();
            
        case 'delete':
            // Delete self-employment record
            $stmt = $pdo->prepare("DELETE FROM self_employment_details WHERE self_employment_id = :self_employment_id");
            $stmt->execute([':self_employment_id' => $_POST['self_employment_id']]);

            if ($isAjax) {
                echo json_encode(['success' => true, 'message' => 'Self-employment record deleted successfully!']);
                exit();
            }

            $_SESSION['success_message'] = 'Self-employment record deleted successfully!';
            header("Location: ../self_employed.php");
            exit();
            
        case 'get':
            // Get single self-employment record for editing
            $stmt = $pdo->prepare("
                SELECT self_employment_id, graduate_id, nature_of_business, 
                       place_of_business, date_from, date_to, has_previous_experience
                FROM self_employment_details 
                WHERE self_employment_id = :self_employment_id
            ");
            $stmt->execute([':self_employment_id' => $_GET['id']]);
            $self_employment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($self_employment) {
                echo json_encode(['success' => true, 'self_employment' => $self_employment]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Self-employment record not found']);
            }
            exit();
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit();
    }
    
} catch (PDOException $e) {
    error_log("CRUD Error: " . $e->getMessage());

    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }

    if (in_array($action, ['create', 'update', 'delete'])) {
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
        header("Location: ../self_employed.php");
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}
?>
