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
    // Count total graduates
    $count_query = "SELECT COUNT(*) as count FROM graduates";
    $params = [];
    
    if ($search) {
        $count_query .= " WHERE first_name LIKE :search OR last_name LIKE :search OR middle_name LIKE :search";
        $params[':search'] = "%$search%";
    }
    
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_count = $stmt->fetch()['count'];
    $total_pages = ceil($total_count / $items_per_page);
    
    // Fetch graduates with pagination
    $query = "
        SELECT 
            g.graduate_id, g.first_name, g.middle_name, g.last_name, 
            g.contact_number, g.gender, g.employment_type, g.date_of_birth,
            g.city_municipality, g.province, g.civil_status, g.permanent_address,
            g.barangay, g.employed_within_6_months, g.registration_date, g.last_updated
        FROM graduates g
    ";
    
    if ($search) {
        $query .= " WHERE g.first_name LIKE :search OR g.last_name LIKE :search OR g.middle_name LIKE :search";
    }
    
    $query .= " ORDER BY g.graduate_id DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($query);
    if ($search) {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $graduates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    // Database error - will be handled by error display logic
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graduates Management | BSIT Tracer</title>
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
            max-width: 600px;
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
                <h1 style="margin: 0; font-size: 1.8rem;">Graduates Management</h1>
                <p style="color: var(--text-muted); margin: 5px 0 0;">Manage all graduate records</p>
            </div>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add Graduate
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
                        placeholder="Search by name..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <?php if ($search): ?>
                        <a href="graduates.php" class="btn btn-secondary">
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
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Employment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($graduates)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                    No graduates found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($graduates as $grad): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($grad['graduate_id']); ?></td>
                                    <td style="font-weight: 600;">
                                        <?php 
                                            $fullName = $grad['first_name'];
                                            if (!empty($grad['middle_name'])) $fullName .= ' ' . $grad['middle_name'];
                                            $fullName .= ' ' . $grad['last_name'];
                                            echo htmlspecialchars($fullName); 
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($grad['gender'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($grad['contact_number'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                            $location = [];
                                            if (!empty($grad['city_municipality'])) $location[] = $grad['city_municipality'];
                                            if (!empty($grad['province'])) $location[] = $grad['province'];
                                            echo htmlspecialchars(implode(', ', $location) ?: 'N/A'); 
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($grad['employment_type']): ?>
                                            <span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $grad['employment_type'])); ?>">
                                                <?php echo htmlspecialchars($grad['employment_type']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: var(--text-muted); font-size: 0.8rem;">Not Set</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="btn btn-primary btn-sm" onclick="viewGraduateDetails(<?php echo htmlspecialchars(json_encode($grad)); ?>)" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-secondary btn-sm" onclick="editGraduate(<?php echo $grad['graduate_id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteGraduate(<?php echo $grad['graduate_id']; ?>, '<?php echo htmlspecialchars($grad['first_name'] . ' ' . $grad['last_name']); ?>')">
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
    <div id="graduateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Add Graduate</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="graduateForm" method="POST" action="./functions/graduates_crud.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="graduate_id" id="graduateId">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" id="firstName" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" id="middleName" class="form-input">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" id="lastName" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Permanent Address</label>
                    <input type="text" name="permanent_address" id="permanentAddress" class="form-input">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Barangay</label>
                        <input type="text" name="barangay" id="barangay" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">City/Municipality</label>
                        <input type="text" name="city_municipality" id="cityMunicipality" class="form-input">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Province</label>
                        <input type="text" name="province" id="province" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" id="contactNumber" class="form-input">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-input">
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="dateOfBirth" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Civil Status</label>
                        <select name="civil_status" id="civilStatus" class="form-input">
                            <option value="">Select</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Separated">Separated</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i> Save Graduate
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Graduate Details Modal -->
    <div id="graduateDetailsModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-user-graduate" style="color: var(--accent);"></i> Graduate Details</h2>
                <button class="close-btn" onclick="closeDetailsModal()">&times;</button>
            </div>
            <div id="graduateDetailsContent" style="max-height: 600px; overflow-y: auto;"></div>
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
                <button type="button" id="deleteConfirmBtn" class="btn btn-danger" onclick="confirmDeleteGraduate()">Delete</button>
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
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .details-section { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; }
        .details-section-title { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent); font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .details-item { margin-bottom: 0.75rem; }
        .details-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.25rem; }
        .details-value { font-size: 0.95rem; font-weight: 500; }
        .details-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-employed { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
        .badge-unemployed { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .badge-self-employed { background: rgba(168, 85, 247, 0.2); color: #a855f7; }
        .badge-ofw { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
        .badge-gender { background: rgba(107, 114, 128, 0.2); color: #9ca3af; }
        .badge-civil { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
        [data-theme="light"] .details-section { background: #ffffff; border-color: #e2e8f0; }
    </style>

    <script>
        let _graduateDeleteTarget = null;

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Graduate';
            document.getElementById('formAction').value = 'create';
            document.getElementById('graduateForm').reset();
            document.getElementById('graduateModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('graduateModal').classList.remove('active');
        }

        function closeDetailsModal() {
            document.getElementById('graduateDetailsModal').style.display = 'none';
        }

        function viewGraduateDetails(grad) {
            const fullName = [grad.first_name, grad.middle_name, grad.last_name].filter(Boolean).join(' ') || 'Unknown';
            const dob = grad.date_of_birth ? new Date(grad.date_of_birth).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A';
            const regDate = grad.registration_date ? new Date(grad.registration_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A';
            const empType = grad.employment_type || 'Not Set';
            const empBadgeClass = empType.toLowerCase().replace(' ', '-');
            const within6Months = grad.employed_within_6_months == 1 ? 'Yes' : 'No';
            
            const html = `
                <div class="details-section">
                    <div class="details-section-title"><i class="fas fa-user"></i> Personal Information</div>
                    <div class="details-grid">
                        <div class="details-item">
                            <div class="details-label">Full Name</div>
                            <div class="details-value">${fullName}</div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">Gender</div>
                            <div class="details-value"><span class="details-badge badge-gender">${grad.gender || 'N/A'}</span></div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">Date of Birth</div>
                            <div class="details-value">${dob}</div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">Civil Status</div>
                            <div class="details-value"><span class="details-badge badge-civil">${grad.civil_status || 'N/A'}</span></div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">Contact Number</div>
                            <div class="details-value">${grad.contact_number || 'N/A'}</div>
                        </div>
                    </div>
                </div>
                
                <div class="details-section">
                    <div class="details-section-title"><i class="fas fa-map-marker-alt"></i> Address Information</div>
                    <div class="details-item">
                        <div class="details-label">Permanent Address</div>
                        <div class="details-value">${grad.permanent_address || 'N/A'}</div>
                    </div>
                    <div class="details-grid">
                        <div class="details-item">
                            <div class="details-label">Barangay</div>
                            <div class="details-value">${grad.barangay || 'N/A'}</div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">City/Municipality</div>
                            <div class="details-value">${grad.city_municipality || 'N/A'}</div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">Province</div>
                            <div class="details-value">${grad.province || 'N/A'}</div>
                        </div>
                    </div>
                </div>
                
                <div class="details-section">
                    <div class="details-section-title"><i class="fas fa-briefcase"></i> Employment Information</div>
                    <div class="details-grid">
                        <div class="details-item">
                            <div class="details-label">Employment Type</div>
                            <div class="details-value"><span class="details-badge badge-${empBadgeClass}">${empType}</span></div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">Employed Within 6 Months</div>
                            <div class="details-value">${within6Months}</div>
                        </div>
                    </div>
                </div>
                
                <div class="details-section">
                    <div class="details-section-title"><i class="fas fa-calendar"></i> Record Information</div>
                    <div class="details-grid">
                        <div class="details-item">
                            <div class="details-label">Registration Date</div>
                            <div class="details-value">${regDate}</div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">Graduate ID</div>
                            <div class="details-value">#${grad.graduate_id}</div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('graduateDetailsContent').innerHTML = html;
            document.getElementById('graduateDetailsModal').style.display = 'flex';
        }

        document.getElementById('graduateDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) closeDetailsModal();
        });

        function editGraduate(id) {
            fetch(`./functions/graduates_crud.php?action=get&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalTitle').textContent = 'Edit Graduate';
                        document.getElementById('formAction').value = 'update';
                        document.getElementById('graduateId').value = data.graduate.graduate_id;
                        document.getElementById('firstName').value = data.graduate.first_name || '';
                        document.getElementById('middleName').value = data.graduate.middle_name || '';
                        document.getElementById('lastName').value = data.graduate.last_name || '';
                        document.getElementById('permanentAddress').value = data.graduate.permanent_address || '';
                        document.getElementById('barangay').value = data.graduate.barangay || '';
                        document.getElementById('cityMunicipality').value = data.graduate.city_municipality || '';
                        document.getElementById('province').value = data.graduate.province || '';
                        document.getElementById('contactNumber').value = data.graduate.contact_number || '';
                        document.getElementById('gender').value = data.graduate.gender || '';
                        document.getElementById('dateOfBirth').value = data.graduate.date_of_birth || '';
                        document.getElementById('civilStatus').value = data.graduate.civil_status || '';
                        document.getElementById('graduateModal').classList.add('active');
                    } else {
                        alert('Error loading graduate data');
                    }
                })
                .catch(err => alert('Error: ' + err));
        }

        function deleteGraduate(id, name) {
            _graduateDeleteTarget = { id, name };
            openDeleteConfirmModal(`Are you sure you want to delete ${name}? This will remove related records (employment, ofw, self-employed, unemployed, previous experiences).`);
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
            _graduateDeleteTarget = null;
        }

        function openDeleteSuccessModal(message) {
            document.getElementById('deleteSuccessMessage').textContent = message || 'Record deleted successfully.';
            document.getElementById('deleteSuccessModal').classList.add('active');
        }

        function closeDeleteSuccessModal(reload) {
            document.getElementById('deleteSuccessModal').classList.remove('active');
            if (reload) window.location.reload();
        }

        function confirmDeleteGraduate() {
            if (!_graduateDeleteTarget) return;

            const btn = document.getElementById('deleteConfirmBtn');
            btn.disabled = true;
            btn.innerHTML = 'Deleting...';

            fetch('./functions/graduates_crud.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `action=delete&graduate_id=${encodeURIComponent(_graduateDeleteTarget.id)}`
            })
            .then(async (res) => {
                const data = await res.json().catch(() => null);
                if (!data || data.success !== true) {
                    const msg = (data && data.message) ? data.message : 'Failed to delete record.';
                    throw new Error(msg);
                }
                closeDeleteConfirmModal();
                openDeleteSuccessModal(data.message || 'Graduate deleted successfully!');
            })
            .catch(err => {
                document.getElementById('deleteConfirmError').textContent = err && err.message ? err.message : 'Error deleting record.';
                btn.disabled = false;
                btn.innerHTML = 'Delete';
            });
        }

        // Close modal on outside click
        document.getElementById('graduateModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
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
