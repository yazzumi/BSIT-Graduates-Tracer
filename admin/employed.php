<?php
require_once "includes/auth_check.php";
require_once "../config/db_conn.php";

// Pagination - responsive: 5 for mobile, 10 for desktop
$items_per_page = isset($_GET['limit']) ? max(5, min(15, (int)$_GET['limit'])) : 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $items_per_page;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // Count total employment records
    $count_query = "SELECT COUNT(*) as count FROM employment_details e
                    LEFT JOIN graduates g ON e.graduate_id = g.graduate_id";
    $params = [];
    
    if ($search) {
        $count_query .= " WHERE e.company_name LIKE :search1 OR e.position LIKE :search2 
                          OR g.first_name LIKE :search3 OR g.last_name LIKE :search4
                          OR e.employment_status LIKE :search5";
        $params[':search1'] = "%$search%";
        $params[':search2'] = "%$search%";
        $params[':search3'] = "%$search%";
        $params[':search4'] = "%$search%";
        $params[':search5'] = "%$search%";
    }
    
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_count = $stmt->fetch()['count'];
    $total_pages = ceil($total_count / $items_per_page);
    
    // Fetch employment records with pagination
    $query = "
        SELECT 
            e.employment_id, e.graduate_id, e.employment_type, e.company_name,
            e.company_address, e.type_of_company, e.position, e.date_from, e.date_to,
            e.employment_status, e.job_description, e.has_previous_experience,
            g.first_name, g.middle_name, g.last_name
        FROM employment_details e
        LEFT JOIN graduates g ON e.graduate_id = g.graduate_id
    ";
    
    if ($search) {
        $query .= " WHERE e.company_name LIKE :search1 OR e.position LIKE :search2 
                    OR g.first_name LIKE :search3 OR g.last_name LIKE :search4
                    OR e.employment_status LIKE :search5";
    }
    
    $query .= " ORDER BY e.employment_id DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($query);
    if ($search) {
        $stmt->bindValue(':search1', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':search2', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':search3', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':search4', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':search5', "%$search%", PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $employments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch all graduates for dropdown
    $grad_stmt = $pdo->query("SELECT graduate_id, first_name, middle_name, last_name FROM graduates ORDER BY last_name, first_name");
    $all_graduates = $grad_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    // Database error - will be handled by error display logic
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employment Management | BSIT Tracer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include 'includes/styles.php'; ?>
    <style>
        .search-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .search-input {
            flex: 1;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: #fff;
            font-size: 0.9rem;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--accent);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: var(--accent);
            color: white;
        }
        
        .btn-primary:hover {
            background: #2563eb;
        }
        
        .btn-secondary {
            background: var(--card-bg);
            color: var(--text-muted);
            border: 1px solid var(--border);
        }
        
        .btn-secondary:hover {
            color: #fff;
            border-color: #3f3f46;
        }
        
        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .btn-danger:hover {
            background: #ef4444;
            color: white;
        }
        
        .btn-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        
        .page-link {
            padding: 0.5rem 0.75rem;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
        }
        
        .page-link:hover, .page-link.active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            max-width: 700px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .close-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        .close-btn:hover {
            color: #fff;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .form-input {
            width: 100%;
            background: var(--bg-dark);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.75rem;
            color: #fff;
            font-size: 0.9rem;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--accent);
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .badge-current {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        
        .badge-previous {
            background: rgba(156, 163, 175, 0.1);
            color: #9ca3af;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }

        .delete-modal-content {
            max-width: 420px;
            text-align: center;
        }

        .delete-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .delete-icon.danger {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }

        .delete-icon.success {
            background: rgba(34, 197, 94, 0.15);
            color: #22c55e;
        }

        .delete-modal-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .delete-modal-actions .btn {
            flex: 1;
        }

        .delete-error {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 0.75rem;
            min-height: 1rem;
        }
    </style>
</head>
<body>

    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <?php include 'includes/sidebar.php'; ?>

    <main class="content">
        <div class="header">
            <div>
                <h1 style="margin: 0; font-size: 1.8rem;">Employment Management</h1>
                <p style="color: var(--text-muted); margin: 5px 0 0;">Manage employment records</p>
            </div>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add Employment
            </button>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: #22c55e; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            </div>
            <?php if (isset($_GET['limit'])) unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            </div>
            <?php if (isset($_GET['limit'])) unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="search-bar">
                <form method="GET" style="display: flex; gap: 1rem; flex: 1;">
                    <input 
                        type="text" 
                        name="search" 
                        class="search-input" 
                        placeholder="Search by company, position, or graduate name..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <?php if ($search): ?>
                        <a href="employed.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Graduate</th>
                            <th>Company</th>
                            <th>Position</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Duration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employments)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                    No employment records found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employments as $emp): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($emp['employment_id']); ?></td>
                                    <td style="font-weight: 600;">
                                        <?php 
                                            $fullName = $emp['first_name'] ?? '';
                                            if (!empty($emp['middle_name'])) $fullName .= ' ' . $emp['middle_name'];
                                            $fullName .= ' ' . ($emp['last_name'] ?? '');
                                            echo htmlspecialchars(trim($fullName) ?: 'Unknown'); 
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($emp['company_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($emp['position'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge-<?php echo strtolower($emp['employment_type'] ?? 'current'); ?>">
                                            <?php echo htmlspecialchars($emp['employment_type'] ?? 'Current'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($emp['employment_status'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                            $from = $emp['date_from'] ? date('M Y', strtotime($emp['date_from'])) : '';
                                            $to = $emp['date_to'] ? date('M Y', strtotime($emp['date_to'])) : 'Present';
                                            echo $from ? "$from - $to" : 'N/A';
                                        ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="btn btn-primary btn-sm" onclick="viewEmployedDetails(<?php echo htmlspecialchars(json_encode($emp)); ?>)" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($emp['has_previous_experience']): ?>
                                            <button class="btn btn-info btn-sm" onclick="viewPrevExp(<?php echo $emp['graduate_id']; ?>)" title="View Previous Experience">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="btn btn-secondary btn-sm" onclick="editEmployment(<?php echo $emp['employment_id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteEmployment(<?php echo $emp['employment_id']; ?>, '<?php echo htmlspecialchars($emp['company_name']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                           class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Add/Edit Modal -->
    <div id="employmentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Add Employment</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="employmentForm" method="POST" action="./functions/employed_crud.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="employment_id" id="employmentId">
                
                <div class="form-group">
                    <label class="form-label">Graduate *</label>
                    <select name="graduate_id" id="graduateId" class="form-input" required>
                        <option value="">Select Graduate</option>
                        <?php foreach ($all_graduates as $grad): ?>
                            <option value="<?php echo $grad['graduate_id']; ?>">
                                <?php 
                                    $name = $grad['first_name'];
                                    if (!empty($grad['middle_name'])) $name .= ' ' . $grad['middle_name'];
                                    $name .= ' ' . $grad['last_name'];
                                    echo htmlspecialchars($name);
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Employment Type</label>
                        <select name="employment_type" id="employmentType" class="form-input">
                            <option value="Current">Current</option>
                            <option value="Previous">Previous</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Employment Status</label>
                        <select name="employment_status" id="employmentStatus" class="form-input">
                            <option value="">Select</option>
                            <option value="Regular">Regular</option>
                            <option value="Contractual">Contractual</option>
                            <option value="Probationary">Probationary</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Full-time">Full-time</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Company Name *</label>
                    <input type="text" name="company_name" id="companyName" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Company Address</label>
                    <input type="text" name="company_address" id="companyAddress" class="form-input">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Type of Company</label>
                        <input type="text" name="type_of_company" id="typeOfCompany" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" id="position" class="form-input">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" id="dateFrom" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" id="dateTo" class="form-input">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Job Description</label>
                    <textarea name="job_description" id="jobDescription" class="form-input" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="has_previous_experience" id="hasPreviousExperience" value="1">
                        <span style="font-size: 0.9rem;">Has Previous Work Experience</span>
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i> Save Employment
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Previous Experience Modal -->
    <div id="prevExpModal" class="modal">
        <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <h2 class="modal-title">Previous Work Experience</h2>
                <button class="close-btn" onclick="closePrevExpModal()">&times;</button>
            </div>
            <div id="prevExpContent" style="max-height: 500px; overflow-y: auto; padding: 1rem;">
                <!-- Content loaded via JS -->
            </div>
        </div>
    </div>

    <!-- Employed Details Modal -->
    <div id="employedDetailsModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-briefcase" style="color: var(--accent);"></i> Employment Details</h2>
                <button class="close-btn" onclick="closeEmployedDetailsModal()">&times;</button>
            </div>
            <div id="employedDetailsContent" style="max-height: 600px; overflow-y: auto;"></div>
        </div>
    </div>

    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content delete-modal-content">
            <div class="delete-icon danger">
                <i class="fas fa-trash" style="font-size: 1.5rem;"></i>
            </div>
            <h2 class="modal-title" style="margin-bottom: 0.5rem;">Confirm Delete</h2>
            <p id="deleteConfirmMessage" style="color: var(--text-muted); margin: 0;"></p>
            <div id="deleteConfirmError" class="delete-error"></div>
            <div class="delete-modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteConfirmModal()">Cancel</button>
                <button type="button" id="deleteConfirmBtn" class="btn btn-danger" onclick="confirmDeleteEmployment()">Delete</button>
            </div>
        </div>
    </div>

    <div id="deleteSuccessModal" class="modal">
        <div class="modal-content delete-modal-content">
            <div class="delete-icon success">
                <i class="fas fa-check" style="font-size: 1.5rem;"></i>
            </div>
            <h2 class="modal-title" style="margin-bottom: 0.5rem;">Deleted</h2>
            <p id="deleteSuccessMessage" style="color: var(--text-muted); margin: 0;">Record deleted successfully.</p>
            <div class="delete-modal-actions">
                <button type="button" class="btn btn-primary" onclick="closeDeleteSuccessModal(true)">OK</button>
            </div>
        </div>
    </div>

    <style>
        .btn-info {
            background: #0ea5e9;
            color: white;
        }
        .btn-info:hover {
            background: #0284c7;
        }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .details-section { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; }
        .details-section-title { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent); font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .details-item { margin-bottom: 0.75rem; }
        .details-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.25rem; }
        .details-value { font-size: 0.95rem; font-weight: 500; }
        .details-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-status { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
        .badge-type { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
        .badge-prev-yes { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
        .badge-prev-no { background: rgba(107, 114, 128, 0.2); color: #6b7280; }
        [data-theme="light"] .details-section { background: #ffffff; border-color: #e2e8f0; }
        .prev-exp-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 1rem;
            overflow: hidden;
        }
        .prev-exp-header {
            background: rgba(58, 130, 246, 0.1);
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
        }
        .prev-exp-num {
            font-weight: 700;
            color: var(--accent);
        }
        .prev-exp-type {
            font-size: 0.75rem;
            background: var(--accent);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }
        .prev-exp-body {
            padding: 1rem;
        }
        .prev-exp-row {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border);
            font-size: 0.9rem;
        }
        .prev-exp-row:last-child {
            border-bottom: none;
        }
        .prev-exp-row strong {
            color: var(--text-muted);
            display: inline-block;
            min-width: 140px;
        }
        [data-theme="light"] .prev-exp-card {
            background: #ffffff;
            border-color: #e2e8f0;
        }
        [data-theme="light"] .prev-exp-header {
            background: rgba(37, 99, 235, 0.05);
        }
        [data-theme="light"] .prev-exp-row {
            border-color: #e2e8f0;
        }
    </style>

    <script>
        let _employmentDeleteTarget = null;

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Employment';
            document.getElementById('formAction').value = 'create';
            document.getElementById('employmentForm').reset();
            document.getElementById('employmentModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('employmentModal').classList.remove('active');
        }

        function closeEmployedDetailsModal() {
            document.getElementById('employedDetailsModal').style.display = 'none';
        }

        function viewEmployedDetails(emp) {
            const fullName = [emp.first_name, emp.middle_name, emp.last_name].filter(Boolean).join(' ') || 'Unknown';
            const dateFrom = emp.date_from ? new Date(emp.date_from).toLocaleDateString('en-US', { year: 'numeric', month: 'long' }) : 'N/A';
            const dateTo = emp.date_to ? new Date(emp.date_to).toLocaleDateString('en-US', { year: 'numeric', month: 'long' }) : 'Present';
            const hasPrevExp = emp.has_previous_experience == 1;
            
            const html = `
                <div class="details-section">
                    <div class="details-section-title"><i class="fas fa-user"></i> Graduate Information</div>
                    <div class="details-grid">
                        <div class="details-item">
                            <div class="details-label">Full Name</div>
                            <div class="details-value">${fullName}</div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">Graduate ID</div>
                            <div class="details-value">#${emp.graduate_id || 'N/A'}</div>
                        </div>
                    </div>
                </div>
                
                <div class="details-section">
                    <div class="details-section-title"><i class="fas fa-briefcase"></i> Employment Details</div>
                    <div class="details-grid">
                        <div class="details-item">
                            <div class="details-label">Employment Type</div>
                            <div class="details-value"><span class="details-badge badge-type">${emp.employment_type || 'Current'}</span></div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">Employment Status</div>
                            <div class="details-value"><span class="details-badge badge-status">${emp.employment_status || 'N/A'}</span></div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">Position</div>
                            <div class="details-value">${emp.position || 'N/A'}</div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">Duration</div>
                            <div class="details-value">${dateFrom} - ${dateTo}</div>
                        </div>
                    </div>
                    <div class="details-item" style="margin-top: 0.5rem;">
                        <div class="details-label">Job Description</div>
                        <div class="details-value">${emp.job_description || 'N/A'}</div>
                    </div>
                </div>
                
                <div class="details-section">
                    <div class="details-section-title"><i class="fas fa-building"></i> Company Information</div>
                    <div class="details-grid">
                        <div class="details-item">
                            <div class="details-label">Company Name</div>
                            <div class="details-value">${emp.company_name || 'N/A'}</div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">Type of Company</div>
                            <div class="details-value">${emp.type_of_company || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="details-item" style="margin-top: 0.5rem;">
                        <div class="details-label">Company Address</div>
                        <div class="details-value">${emp.company_address || 'N/A'}</div>
                    </div>
                </div>
                
                <div class="details-section">
                    <div class="details-section-title"><i class="fas fa-history"></i> Previous Experience</div>
                    <div class="details-item">
                        <div class="details-label">Has Previous Work Experience</div>
                        <div class="details-value">
                            <span class="details-badge ${hasPrevExp ? 'badge-prev-yes' : 'badge-prev-no'}">
                                ${hasPrevExp ? 'Yes' : 'No'}
                            </span>
                            ${hasPrevExp ? '<button class="btn btn-info btn-sm" style="margin-left: 1rem;" onclick="closeEmployedDetailsModal(); viewPrevExp(' + emp.graduate_id + ')"><i class="fas fa-eye"></i> View Previous Jobs</button>' : ''}
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('employedDetailsContent').innerHTML = html;
            document.getElementById('employedDetailsModal').style.display = 'flex';
        }

        document.getElementById('employedDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) closeEmployedDetailsModal();
        });

        function editEmployment(id) {
            fetch(`./functions/employed_crud.php?action=get&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalTitle').textContent = 'Edit Employment';
                        document.getElementById('formAction').value = 'update';
                        document.getElementById('employmentId').value = data.employment.employment_id;
                        document.getElementById('graduateId').value = data.employment.graduate_id || '';
                        document.getElementById('employmentType').value = data.employment.employment_type || 'Current';
                        document.getElementById('companyName').value = data.employment.company_name || '';
                        document.getElementById('companyAddress').value = data.employment.company_address || '';
                        document.getElementById('typeOfCompany').value = data.employment.type_of_company || '';
                        document.getElementById('position').value = data.employment.position || '';
                        document.getElementById('dateFrom').value = data.employment.date_from || '';
                        document.getElementById('dateTo').value = data.employment.date_to || '';
                        document.getElementById('employmentStatus').value = data.employment.employment_status || '';
                        document.getElementById('jobDescription').value = data.employment.job_description || '';
                        document.getElementById('hasPreviousExperience').checked = data.employment.has_previous_experience == 1;
                        document.getElementById('employmentModal').classList.add('active');
                    } else {
                        alert('Error loading employment data');
                    }
                })
                .catch(err => alert('Error: ' + err));
        }

        function deleteEmployment(id, company) {
            _employmentDeleteTarget = { id, company };
            openDeleteConfirmModal(`Are you sure you want to delete the employment record at ${company}? This action cannot be undone.`);
        }

        function confirmDeleteEmployment() {
            if (!_employmentDeleteTarget) return;

            const btn = document.getElementById('deleteConfirmBtn');
            btn.disabled = true;
            btn.innerHTML = 'Deleting...';

            fetch('./functions/employed_crud.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `action=delete&employment_id=${encodeURIComponent(_employmentDeleteTarget.id)}`
            })
            .then(async (res) => {
                const data = await res.json().catch(() => null);
                if (!data || data.success !== true) {
                    const msg = (data && data.message) ? data.message : 'Failed to delete record.';
                    throw new Error(msg);
                }
                closeDeleteConfirmModal();
                openDeleteSuccessModal(data.message || 'Employment record deleted successfully!');
            })
            .catch(err => {
                document.getElementById('deleteConfirmError').textContent = err && err.message ? err.message : 'Error deleting record.';
                btn.disabled = false;
                btn.innerHTML = 'Delete';
            });
        }

        // View Previous Experience
        function viewPrevExp(graduateId) {
            fetch('./functions/get_prev_exp.php?graduate_id=' + graduateId)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        let html = '';
                        if (data.experiences.length === 0) {
                            html = '<p style="text-align: center; color: var(--text-muted);">No previous experience records found.</p>';
                        } else {
                            data.experiences.forEach((exp, index) => {
                                html += `
                                    <div class="prev-exp-card">
                                        <div class="prev-exp-header">
                                            <span class="prev-exp-num">#${index + 1}</span>
                                            <span class="prev-exp-type">${exp.employment_type || 'N/A'}</span>
                                        </div>
                                        <div class="prev-exp-body">
                                            <div class="prev-exp-row">
                                                <strong>Company:</strong> ${exp.company_name || 'N/A'}
                                            </div>
                                            <div class="prev-exp-row">
                                                <strong>Position:</strong> ${exp.position || 'N/A'}
                                            </div>
                                            <div class="prev-exp-row">
                                                <strong>Nature of Business:</strong> ${exp.nature_of_business || 'N/A'}
                                            </div>
                                            <div class="prev-exp-row">
                                                <strong>Job Description:</strong> ${exp.job_description || 'N/A'}
                                            </div>
                                            <div class="prev-exp-row">
                                                <strong>Company Address:</strong> ${exp.company_address || 'N/A'}
                                            </div>
                                            <div class="prev-exp-row">
                                                <strong>Duration:</strong> ${exp.date_from || 'N/A'} - ${exp.date_to || 'N/A'}
                                            </div>
                                            <div class="prev-exp-row">
                                                <strong>Status:</strong> ${exp.employment_status || 'N/A'}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        }
                        document.getElementById('prevExpContent').innerHTML = html;
                        document.getElementById('prevExpModal').style.display = 'flex';
                    } else {
                        alert('Error loading previous experience data');
                    }
                })
                .catch(err => alert('Error: ' + err));
        }

        function closePrevExpModal() {
            document.getElementById('prevExpModal').style.display = 'none';
        }

        function openDeleteConfirmModal(message) {
            document.getElementById('deleteConfirmMessage').textContent = message;
            document.getElementById('deleteConfirmError').textContent = '';
            const btn = document.getElementById('deleteConfirmBtn');
            btn.disabled = false;
            btn.innerHTML = 'Delete';
            document.getElementById('deleteConfirmModal').classList.add('active');
        }

        function closeDeleteConfirmModal() {
            document.getElementById('deleteConfirmModal').classList.remove('active');
            _employmentDeleteTarget = null;
        }

        function openDeleteSuccessModal(message) {
            document.getElementById('deleteSuccessMessage').textContent = message || 'Record deleted successfully.';
            document.getElementById('deleteSuccessModal').classList.add('active');
        }

        function closeDeleteSuccessModal(reload) {
            document.getElementById('deleteSuccessModal').classList.remove('active');
            if (reload) window.location.reload();
        }

        // Close modal on outside click
        document.getElementById('employmentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('prevExpModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePrevExpModal();
            }
        });

        document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteConfirmModal();
        });

        document.getElementById('deleteSuccessModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteSuccessModal(true);
        });

        // Mobile Sidebar Toggle
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
            document.querySelector('.sidebar-overlay').classList.toggle('open');
        }

        // Responsive Pagination - detect screen size and reload if needed
        (function() {
            const isMobile = window.innerWidth <= 768;
            const currentLimit = new URLSearchParams(window.location.search).get('limit');
            const targetLimit = isMobile ? 5 : 10;
            
            if (currentLimit === null || parseInt(currentLimit) !== targetLimit) {
                const url = new URL(window.location.href);
                url.searchParams.set('limit', targetLimit);
                url.searchParams.set('page', 1);
                window.location.replace(url.toString());
            }
        })();
    </script>
</body>
</html>
