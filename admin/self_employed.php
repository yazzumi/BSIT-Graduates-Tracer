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
    // Count total self-employment records
    $count_query = "SELECT COUNT(*) as count FROM self_employment_details s
                    LEFT JOIN graduates g ON s.graduate_id = g.graduate_id";
    $params = [];
    
    if ($search) {
        $count_query .= " WHERE s.nature_of_business LIKE :search1 OR s.place_of_business LIKE :search2 
                          OR g.first_name LIKE :search3 OR g.last_name LIKE :search4";
        $params[':search1'] = "%$search%";
        $params[':search2'] = "%$search%";
        $params[':search3'] = "%$search%";
        $params[':search4'] = "%$search%";
    }
    
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_count = $stmt->fetch()['count'];
    $total_pages = ceil($total_count / $items_per_page);
    
    // Fetch self-employment records with pagination
    $query = "
        SELECT 
            s.self_employment_id, s.graduate_id, s.nature_of_business,
            s.place_of_business, s.date_from, s.date_to, s.has_previous_experience,
            g.first_name, g.middle_name, g.last_name, g.contact_number
        FROM self_employment_details s
        LEFT JOIN graduates g ON s.graduate_id = g.graduate_id
    ";
    
    if ($search) {
        $query .= " WHERE s.nature_of_business LIKE :search1 OR s.place_of_business LIKE :search2 
                    OR g.first_name LIKE :search3 OR g.last_name LIKE :search4";
    }
    
    $query .= " ORDER BY s.self_employment_id DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($query);
    if ($search) {
        $stmt->bindValue(':search1', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':search2', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':search3', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':search4', "%$search%", PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $self_employed = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch all graduates for dropdown
    $grad_stmt = $pdo->query("SELECT graduate_id, first_name, middle_name, last_name FROM graduates ORDER BY last_name, first_name");
    $all_graduates = $grad_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Self-Employed Management | BSIT Tracer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include 'includes/styles.php'; ?>
    <style>
        .search-bar { display: flex; gap: 1rem; margin-bottom: 2rem; }
        .search-input { flex: 1; background: var(--card-bg); border: 1px solid var(--border); border-radius: 10px; padding: 0.75rem 1rem; color: #fff; font-size: 0.9rem; }
        .search-input:focus { outline: none; border-color: var(--accent); }
        .btn { padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 600; font-size: 0.85rem; border: none; cursor: pointer; transition: 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: #2563eb; }
        .btn-secondary { background: var(--card-bg); color: var(--text-muted); border: 1px solid var(--border); }
        .btn-secondary:hover { color: #fff; border-color: #3f3f46; }
        .btn-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
        .btn-danger:hover { background: #ef4444; color: white; }
        .btn-sm { padding: 0.5rem 0.75rem; font-size: 0.75rem; }
        .actions { display: flex; gap: 0.5rem; }
        .pagination { display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem; }
        .page-link { padding: 0.5rem 0.75rem; background: var(--card-bg); border: 1px solid var(--border); border-radius: 6px; color: var(--text-muted); text-decoration: none; font-size: 0.85rem; }
        .page-link:hover, .page-link.active { background: var(--accent); color: white; border-color: var(--accent); }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; padding: 2rem; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .modal-title { font-size: 1.5rem; font-weight: 700; }
        .close-btn { background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; }
        .close-btn:hover { color: #fff; }
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem; font-weight: 600; }
        .form-input { width: 100%; background: var(--bg-dark); border: 1px solid var(--border); border-radius: 8px; padding: 0.75rem; color: #fff; font-size: 0.9rem; }
        .form-input:focus { outline: none; border-color: var(--accent); }
        .form-actions { display: flex; gap: 1rem; margin-top: 2rem; }
        .badge-yes { background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; }
        .badge-no { background: rgba(156, 163, 175, 0.1); color: #9ca3af; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; }
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
                <h1 style="margin: 0; font-size: 1.8rem;">Self-Employed Management</h1>
                <p style="color: var(--text-muted); margin: 5px 0 0;">Manage self-employment records</p>
            </div>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add Self-Employed
            </button>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); color: #22c55e; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="search-bar">
                <form method="GET" style="display: flex; gap: 1rem; flex: 1;">
                    <input type="text" name="search" class="search-input" placeholder="Search by business, location, or graduate name..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                    <?php if ($search): ?>
                        <a href="self_employed.php" class="btn btn-secondary"><i class="fas fa-times"></i> Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Graduate</th>
                            <th>Nature of Business</th>
                            <th>Location</th>
                            <th>Duration</th>
                            <th>Prev. Experience</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($self_employed)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                    No self-employment records found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($self_employed as $person): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($person['self_employment_id']); ?></td>
                                    <td style="font-weight: 600;">
                                        <?php 
                                            $fullName = $person['first_name'] ?? '';
                                            if (!empty($person['middle_name'])) $fullName .= ' ' . $person['middle_name'];
                                            $fullName .= ' ' . ($person['last_name'] ?? '');
                                            echo htmlspecialchars(trim($fullName) ?: 'Unknown'); 
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($person['nature_of_business'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($person['place_of_business'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                            $from = $person['date_from'] ? date('M Y', strtotime($person['date_from'])) : '';
                                            $to = $person['date_to'] ? date('M Y', strtotime($person['date_to'])) : 'Present';
                                            echo $from ? "$from - $to" : 'N/A';
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($person['has_previous_experience']): ?>
                                            <span class="badge-yes">Yes</span>
                                        <?php else: ?>
                                            <span class="badge-no">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="btn btn-primary btn-sm" onclick="viewSelfEmployedDetails(<?php echo htmlspecialchars(json_encode($person)); ?>)" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($person['has_previous_experience']): ?>
                                            <button class="btn btn-info btn-sm" onclick="viewPrevExp(<?php echo $person['graduate_id']; ?>)" title="View Previous Experience">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="btn btn-secondary btn-sm" onclick="editSelfEmployed(<?php echo $person['self_employment_id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteSelfEmployed(<?php echo $person['self_employment_id']; ?>, '<?php echo htmlspecialchars($person['nature_of_business']); ?>')">
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
                        <a href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="page-link"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Add/Edit Modal -->
    <div id="selfEmployedModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Add Self-Employed Record</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="selfEmployedForm" method="POST" action="./functions/self_employed_crud.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="self_employment_id" id="selfEmploymentId">
                
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
                
                <div class="form-group">
                    <label class="form-label">Nature of Business *</label>
                    <input type="text" name="nature_of_business" id="natureOfBusiness" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Place of Business</label>
                    <input type="text" name="place_of_business" id="placeOfBusiness" class="form-input">
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
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="has_previous_experience" id="hasPreviousExperience" value="1">
                        <span style="font-size: 0.9rem;">Has Previous Work Experience</span>
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" style="flex: 1;"><i class="fas fa-save"></i> Save Record</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
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
            <div id="prevExpContent" style="max-height: 500px; overflow-y: auto; padding: 1rem;"></div>
        </div>
    </div>

    <!-- Self-Employed Details Modal -->
    <div id="selfEmployedDetailsModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-store" style="color: var(--accent);"></i> Self-Employment Details</h2>
                <button class="close-btn" onclick="closeSelfEmployedDetailsModal()">&times;</button>
            </div>
            <div id="selfEmployedDetailsContent" style="max-height: 600px; overflow-y: auto;"></div>
        </div>
    </div>

    <style>
        .btn-info { background: #0ea5e9; color: white; }
        .btn-info:hover { background: #0284c7; }
        .prev-exp-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 1rem; overflow: hidden; }
        .prev-exp-header { background: rgba(58, 130, 246, 0.1); padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
        .prev-exp-num { font-weight: 700; color: var(--accent); }
        .prev-exp-type { font-size: 0.75rem; background: var(--accent); color: white; padding: 0.25rem 0.75rem; border-radius: 20px; }
        .prev-exp-body { padding: 1rem; }
        .prev-exp-row { padding: 0.5rem 0; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        .prev-exp-row:last-child { border-bottom: none; }
        .prev-exp-row strong { color: var(--text-muted); display: inline-block; min-width: 140px; }
        [data-theme="light"] .prev-exp-card { background: #ffffff; border-color: #e2e8f0; }
        [data-theme="light"] .prev-exp-header { background: rgba(37, 99, 235, 0.05); }
        [data-theme="light"] .prev-exp-row { border-color: #e2e8f0; }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .details-section { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; }
        .details-section-title { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent); font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .details-item { margin-bottom: 0.75rem; }
        .details-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.25rem; }
        .details-value { font-size: 0.95rem; font-weight: 500; }
        .details-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-prev-yes { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
        .badge-prev-no { background: rgba(107, 114, 128, 0.2); color: #6b7280; }
        [data-theme="light"] .details-section { background: #ffffff; border-color: #e2e8f0; }
    </style>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Self-Employed Record';
            document.getElementById('formAction').value = 'create';
            document.getElementById('selfEmployedForm').reset();
            document.getElementById('selfEmployedModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('selfEmployedModal').classList.remove('active');
        }

        function closeSelfEmployedDetailsModal() {
            document.getElementById('selfEmployedDetailsModal').style.display = 'none';
        }

        function viewSelfEmployedDetails(person) {
            const fullName = [person.first_name, person.middle_name, person.last_name].filter(Boolean).join(' ') || 'Unknown';
            const dateFrom = person.date_from ? new Date(person.date_from).toLocaleDateString('en-US', { year: 'numeric', month: 'long' }) : 'N/A';
            const dateTo = person.date_to ? new Date(person.date_to).toLocaleDateString('en-US', { year: 'numeric', month: 'long' }) : 'Present';
            const hasPrevExp = person.has_previous_experience == 1;
            
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
                            <div class="details-value">#${person.graduate_id || 'N/A'}</div>
                        </div>
                    </div>
                </div>
                
                <div class="details-section">
                    <div class="details-section-title"><i class="fas fa-store"></i> Business Details</div>
                    <div class="details-item">
                        <div class="details-label">Nature of Business</div>
                        <div class="details-value">${person.nature_of_business || 'N/A'}</div>
                    </div>
                    <div class="details-item">
                        <div class="details-label">Place of Business</div>
                        <div class="details-value">${person.place_of_business || 'N/A'}</div>
                    </div>
                    <div class="details-grid">
                        <div class="details-item">
                            <div class="details-label">Date Started</div>
                            <div class="details-value">${dateFrom}</div>
                        </div>
                        <div class="details-item">
                            <div class="details-label">Date Ended</div>
                            <div class="details-value">${dateTo}</div>
                        </div>
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
                            ${hasPrevExp ? '<button class="btn btn-info btn-sm" style="margin-left: 1rem;" onclick="closeSelfEmployedDetailsModal(); viewPrevExp(' + person.graduate_id + ')"><i class="fas fa-eye"></i> View Previous Jobs</button>' : ''}
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('selfEmployedDetailsContent').innerHTML = html;
            document.getElementById('selfEmployedDetailsModal').style.display = 'flex';
        }

        document.getElementById('selfEmployedDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) closeSelfEmployedDetailsModal();
        });

        function editSelfEmployed(id) {
            fetch(`./functions/self_employed_crud.php?action=get&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalTitle').textContent = 'Edit Self-Employed Record';
                        document.getElementById('formAction').value = 'update';
                        document.getElementById('selfEmploymentId').value = data.self_employment.self_employment_id;
                        document.getElementById('graduateId').value = data.self_employment.graduate_id || '';
                        document.getElementById('natureOfBusiness').value = data.self_employment.nature_of_business || '';
                        document.getElementById('placeOfBusiness').value = data.self_employment.place_of_business || '';
                        document.getElementById('dateFrom').value = data.self_employment.date_from || '';
                        document.getElementById('dateTo').value = data.self_employment.date_to || '';
                        document.getElementById('hasPreviousExperience').checked = data.self_employment.has_previous_experience == 1;
                        document.getElementById('selfEmployedModal').classList.add('active');
                    } else {
                        alert('Error loading data');
                    }
                })
                .catch(err => alert('Error: ' + err));
        }

        function deleteSelfEmployed(id, business) {
            if (confirm(`Are you sure you want to delete the self-employment record for "${business}"?`)) {
                fetch('./functions/self_employed_crud.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=delete&self_employment_id=${id}`
                })
                .then(res => res.text())
                .then(() => {
                    window.location.reload();
                })
                .catch(err => alert('Error: ' + err));
            }
        }

        document.getElementById('selfEmployedModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

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
                                html += `<div class="prev-exp-card"><div class="prev-exp-header"><span class="prev-exp-num">#${index + 1}</span><span class="prev-exp-type">${exp.employment_type || 'N/A'}</span></div><div class="prev-exp-body"><div class="prev-exp-row"><strong>Company:</strong> ${exp.company_name || 'N/A'}</div><div class="prev-exp-row"><strong>Position:</strong> ${exp.position || 'N/A'}</div><div class="prev-exp-row"><strong>Nature of Business:</strong> ${exp.nature_of_business || 'N/A'}</div><div class="prev-exp-row"><strong>Job Description:</strong> ${exp.job_description || 'N/A'}</div><div class="prev-exp-row"><strong>Company Address:</strong> ${exp.company_address || 'N/A'}</div><div class="prev-exp-row"><strong>Duration:</strong> ${exp.date_from || 'N/A'} - ${exp.date_to || 'N/A'}</div><div class="prev-exp-row"><strong>Status:</strong> ${exp.employment_status || 'N/A'}</div></div></div>`;
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

        document.getElementById('prevExpModal').addEventListener('click', function(e) {
            if (e.target === this) closePrevExpModal();
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
