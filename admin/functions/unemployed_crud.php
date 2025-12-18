<?php
require_once __DIR__ . "/../includes/auth_check.php";
require_once __DIR__ . "/../../config/db_conn.php";

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

try {
    switch ($action) {
        case 'create':
            // Create new unemployment record
            $stmt = $pdo->prepare("
                INSERT INTO unemployed_details (
                    graduate_id, has_previous_experience
                ) VALUES (
                    :graduate_id, :has_previous_experience
                )
            ");
            
            $stmt->execute([
                ':graduate_id' => $_POST['graduate_id'],
                ':has_previous_experience' => isset($_POST['has_previous_experience']) ? 1 : 0
            ]);
            
            // Update graduates table to set employment_type to 'Unemployed'
            $update_stmt = $pdo->prepare("
                UPDATE graduates 
                SET employment_type = 'Unemployed'
                WHERE graduate_id = :graduate_id
            ");
            $update_stmt->execute([':graduate_id' => $_POST['graduate_id']]);
            
            $_SESSION['success_message'] = 'Unemployment record added successfully!';
            header("Location: ../unemployed.php");
            exit();
            
        case 'update':
            // Update existing unemployment record
            $stmt = $pdo->prepare("
                UPDATE unemployed_details 
                SET graduate_id = :graduate_id,
                    has_previous_experience = :has_previous_experience
                WHERE unemployed_id = :unemployed_id
            ");
            
            $stmt->execute([
                ':unemployed_id' => $_POST['unemployed_id'],
                ':graduate_id' => $_POST['graduate_id'],
                ':has_previous_experience' => isset($_POST['has_previous_experience']) ? 1 : 0
            ]);
            
            // Update graduates table to maintain employment_type
            $update_stmt = $pdo->prepare("
                UPDATE graduates 
                SET employment_type = 'Unemployed'
                WHERE graduate_id = :graduate_id
            ");
            $update_stmt->execute([':graduate_id' => $_POST['graduate_id']]);
            
            $_SESSION['success_message'] = 'Unemployment record updated successfully!';
            header("Location: ../unemployed.php");
            exit();
            
        case 'delete':
            // Delete unemployment record
            $stmt = $pdo->prepare("DELETE FROM unemployed_details WHERE unemployed_id = :unemployed_id");
            $stmt->execute([':unemployed_id' => $_POST['unemployed_id']]);

            if ($isAjax) {
                echo json_encode(['success' => true, 'message' => 'Unemployment record deleted successfully!']);
                exit();
            }

            $_SESSION['success_message'] = 'Unemployment record deleted successfully!';
            header("Location: ../unemployed.php");
            exit();
            
        case 'get':
            // Get single unemployment record for editing
            $stmt = $pdo->prepare("
                SELECT unemployed_id, graduate_id, has_previous_experience
                FROM unemployed_details 
                WHERE unemployed_id = :unemployed_id
            ");
            $stmt->execute([':unemployed_id' => $_GET['id']]);
            $unemployment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($unemployment) {
                echo json_encode(['success' => true, 'unemployment' => $unemployment]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Unemployment record not found']);
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
        header("Location: ../unemployed.php");
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}
?>
