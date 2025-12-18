<?php
session_start();
require_once "../config/db_conn.php";

header('Content-Type: application/json');

try {
    // Get JSON data from request
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    // Debug: Log received data
    error_log("=== SAVE TO DB DEBUG ===");
    error_log("Received data: " . $json_data);
    error_log("Decoded data: " . print_r($data, true));
    
    if (!$data) {
        throw new Exception('Invalid data received');
    }
    
    // Get student ID from session or data
    $student_id = $_SESSION['temp_student_id'] ?? $data['student_id'] ?? null;
    $full_name = $_SESSION['temp_full_name'] ?? $data['full_name'] ?? null;
    
    error_log("Student ID: $student_id, Full Name: $full_name");
    
    if (!$student_id || !$full_name) {
        throw new Exception('Student identification missing');
    }
    
    // Start transaction
    $pdo->beginTransaction();
    error_log("Transaction started");
    
    // Insert into graduates table
    $graduate_stmt = $pdo->prepare("
        INSERT INTO graduates (
            first_name, middle_name, last_name, permanent_address, barangay,
            city_municipality, province, contact_number, gender, date_of_birth,
            civil_status, employed_within_6_months, employment_type
        ) VALUES (
            :first_name, :middle_name, :last_name, :permanent_address, :barangay,
            :city_municipality, :province, :contact_number, :gender, :date_of_birth,
            :civil_status, :employed_within_6_months, :employment_type
        )
    ");
    
    $employment_status = $data['employment_status'] ?? '';
    error_log("Employment Status: '$employment_status'");
    
    $graduate_stmt->execute([
        ':first_name' => $data['first_name'] ?? '',
        ':middle_name' => $data['middle_name'] ?? null,
        ':last_name' => $data['last_name'] ?? '',
        ':permanent_address' => $data['permanent_address'] ?? '',
        ':barangay' => $data['barangay'] ?? '',
        ':city_municipality' => $data['city_municipality'] ?? '',
        ':province' => $data['province'] ?? '',
        ':contact_number' => $data['contact_number'] ?? '',
        ':gender' => $data['gender'] ?? '',
        ':date_of_birth' => $data['date_of_birth'] ?? null,
        ':civil_status' => $data['civil_status'] ?? '',
        ':employed_within_6_months' => $data['employed_within_6_months'] ?? 0,
        ':employment_type' => $employment_status
    ]);
    
    $graduate_id = $pdo->lastInsertId();
    error_log("Graduate ID: $graduate_id");
    
    // Handle employment details based on status
    switch ($employment_status) {
        case 'Employed':
            error_log("Processing Employed case");
            if (!empty($data['company_name'])) {
                $emp_stmt = $pdo->prepare("
                    INSERT INTO employment_details (
                        graduate_id, employment_type, company_name, company_address,
                        type_of_company, position, date_from, date_to, employment_status,
                        job_description, has_previous_experience
                    ) VALUES (
                        :graduate_id, 'Current', :company_name, :company_address,
                        :type_of_company, :position, :date_from, :date_to, :emp_status,
                        :job_description, :has_previous_experience
                    )
                ");
                
                $emp_stmt->execute([
                    ':graduate_id' => $graduate_id,
                    ':company_name' => $data['company_name'] ?? '',
                    ':company_address' => $data['company_address'] ?? '',
                    ':type_of_company' => $data['type_of_company'] ?? null,
                    ':position' => $data['position'] ?? $data['other_position'] ?? '',
                    ':date_from' => $data['date_from'] ?? null,
                    ':date_to' => (!empty($data['is_present'])) ? null : ($data['date_to'] ?? null),
                    ':emp_status' => $data['emp_status'] ?? '',
                    ':job_description' => $data['job_desc'] ?? null,
                    ':has_previous_experience' => ($data['has_previous_exp'] ?? 'NO') === 'YES' ? 1 : 0
                ]);
                error_log("Employed details inserted");
            } else {
                error_log("No company name provided for Employed case");
            }
            break;
            
        case 'OFW':
            error_log("Processing OFW case");
            if (!empty($data['company_name'])) {
                $ofw_stmt = $pdo->prepare("
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
                
                $ofw_stmt->execute([
                    ':graduate_id' => $graduate_id,
                    ':company_name' => $data['company_name'] ?? '',
                    ':company_address' => $data['company_address'] ?? '',
                    ':type_of_company' => $data['company_type'] ?? null,
                    ':position' => $data['job_position'] ?? $data['other_position'] ?? '',
                    ':date_from' => $data['start_year'] ?? null,
                    ':date_to' => $data['end_year'] ?? null,
                    ':employment_status' => $data['ofw_legal_status'] ?? '',
                    ':has_previous_experience' => ($data['has_previous_exp'] ?? 'NO') === 'YES' ? 1 : 0,
                    ':country' => $data['country'] ?? ''
                ]);
                error_log("OFW details inserted");
            } else {
                error_log("No company name provided for OFW case");
            }
            break;
            
        case 'Self-Employed':
            error_log("Processing Self-Employed case");
            if (!empty($data['business_nature'])) {
                $self_stmt = $pdo->prepare("
                    INSERT INTO self_employment_details (
                        graduate_id, nature_of_business, place_of_business,
                        date_from, date_to, has_previous_experience
                    ) VALUES (
                        :graduate_id, :nature_of_business, :place_of_business,
                        :date_from, :date_to, :has_previous_experience
                    )
                ");
                
                $self_stmt->execute([
                    ':graduate_id' => $graduate_id,
                    ':nature_of_business' => $data['business_nature'] ?? '',
                    ':place_of_business' => $data['business_address'] ?? '',
                    ':date_from' => $data['date_started'] ?? null,
                    ':date_to' => $data['date_ended'] ?? null,
                    ':has_previous_experience' => ($data['has_previous_exp'] ?? 'NO') === 'YES' ? 1 : 0
                ]);
                error_log("Self-Employed details inserted");
            } else {
                error_log("No business nature provided for Self-Employed case");
            }
            break;
            
        case 'Unemployed':
            error_log("Processing Unemployed case");
            $unemp_stmt = $pdo->prepare("
                INSERT INTO unemployed_details (
                    graduate_id, has_previous_experience
                ) VALUES (
                    :graduate_id, :has_previous_experience
                )
            ");
            
            $unemp_stmt->execute([
                ':graduate_id' => $graduate_id,
                ':has_previous_experience' => ($data['has_previous_exp'] ?? 'NO') === 'YES' ? 1 : 0
            ]);
            error_log("Unemployed details inserted");
            break;
            
        default:
            error_log("Unknown employment status: '$employment_status'");
    }
    
    // Handle previous experiences if any
    if (($data['has_previous_exp'] ?? 'NO') === 'YES' && !empty($data['previous_experiences'])) {
        error_log("Processing previous experiences");
        foreach ($data['previous_experiences'] as $exp) {
            $prev_stmt = $pdo->prepare("
                INSERT INTO previous_experiences (
                    graduate_id, employment_type, company_name, nature_of_business,
                    position, job_description, company_address, type_of_company,
                    date_from, date_to, employment_status
                ) VALUES (
                    :graduate_id, :employment_type, :company_name, :nature_of_business,
                    :position, :job_description, :company_address, :type_of_company,
                    :date_from, :date_to, :employment_status
                )
            ");
            
            $prev_stmt->execute([
                ':graduate_id' => $graduate_id,
                ':employment_type' => $exp['employment_type'] ?? '',
                ':company_name' => $exp['company_name'] ?? '',
                ':nature_of_business' => $exp['nature_of_business'] ?? '',
                ':position' => $exp['position'] ?? '',
                ':job_description' => $exp['job_description'] ?? '',
                ':company_address' => $exp['company_address'] ?? '',
                ':type_of_company' => $exp['type_of_company'] ?? '',
                ':date_from' => $exp['date_from'] ?? null,
                ':date_to' => $exp['date_to'] ?? null,
                ':employment_status' => $exp['employment_status'] ?? ''
            ]);
        }
        error_log("Previous experiences inserted");
    }
    
    // Mark survey as completed in validated_graduates table
    $update_stmt = $pdo->prepare("
        UPDATE validated_graduates 
        SET survey_completed = 1, 
            survey_completion_date = NOW(),
            graduate_id = :graduate_id
        WHERE student_id = :student_id AND full_name = :full_name
    ");
    
    $update_stmt->execute([
        ':graduate_id' => $graduate_id,
        ':student_id' => $student_id,
        ':full_name' => $full_name
    ]);
    error_log("Validated graduates updated");
    
    // Commit transaction
    $pdo->commit();
    error_log("Transaction committed successfully");
    
    // Clear session data
    unset($_SESSION['temp_student_id'], $_SESSION['temp_full_name'], $_SESSION['is_validated']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Survey data saved successfully',
        'graduate_id' => $graduate_id
    ]);
    
} catch (Exception $e) {
    // Rollback transaction if started
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
        error_log("Transaction rolled back due to error");
    }
    
    error_log("Survey submission error: " . $e->getMessage());
    error_log("Error trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error saving survey data: ' . $e->getMessage()
    ]);
}
?>