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
    // Count total unemployment records
    $count_query = "SELECT COUNT(*) as count FROM unemployed_details u
                    LEFT JOIN graduates g ON u.graduate_id = g.graduate_id";
    $params = [];
    
    if ($search) {
        $count_query .= " WHERE g.first_name LIKE :search1 OR g.last_name LIKE :search2 OR g.middle_name LIKE :search3";
        $params[':search1'] = "%$search%";
        $params[':search2'] = "%$search%";
        $params[':search3'] = "%$search%";
    }
    
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_count = $stmt->fetch()['count'];
    $total_pages = ceil($total_count / $items_per_page);
    
    // Fetch unemployment records with pagination
    $query = "
        SELECT 
            u.unemployed_id, u.graduate_id, u.has_previous_experience,
            g.first_name, g.middle_name, g.last_name, g.date_of_birth,
            g.contact_number, g.city_municipality, g.province
        FROM unemployed_details u
        LEFT JOIN graduates g ON u.graduate_id = g.graduate_id
    ";
    
    if ($search) {
        $query .= " WHERE g.first_name LIKE :search1 OR g.last_name LIKE :search2 OR g.middle_name LIKE :search3";
    }
    
    $query .= " ORDER BY u.unemployed_id DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($query);
    if ($search) {
        $stmt->bindValue(':search1', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':search2', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':search3', "%$search%", PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $unemployed = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch all graduates for dropdown (excluding those already in unemployment)
    $grad_stmt = $pdo->query("
        SELECT g.graduate_id, g.first_name, g.middle_name, g.last_name 
        FROM graduates g 
        LEFT JOIN unemployed_details u ON g.graduate_id = u.graduate_id 
        WHERE u.unemployed_id IS NULL 
        ORDER BY g.last_name, g.first_name
    ");
    $available_graduates = $grad_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unemployment Management | BSIT Tracer</title>
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
            max-width: 500px;
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
        
        .badge-yes {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        
        .badge-no {
            background: rgba(156, 163, 175, 0.1);
            color: #9ca3af;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
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
                <h1 style="margin: 0; font-size: 1.8rem;">Unemployment Management</h1>
                <p style="color: var(--text-muted); margin: 5px 0 0;">Manage unemployment records</p>
            </div>
            <button class="btn btn-primary" onclick="openAddModal()" <?php echo empty($available_graduates) ? 'disabled' : ''; ?>>
                <i class="fas fa-plus"></i> Add Unemployment Record
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

        <?php if (empty($available_graduates)): ?>
            <div style="background: rgba(156, 163, 175, 0.1); border: 1px solid rgba(156, 163, 175, 0.3); color: #9ca3af; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <i class="fas fa-info-circle"></i> All graduates are already registered in the unemployment system. 
                <a href="graduates.php" style="color: #3b82f6; text-decoration: none;">Add more graduates</a> to create unemployment records.
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="search-bar">
                <form method="GET" style="display: flex; gap: 1rem; flex: 1;">
                    <input 
                        type="text" 
                        name="search" 
                        class="search-input" 
                        placeholder="Search by graduate name..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <?php if ($search): ?>
                        <a href="unemployed.php" class="btn btn-secondary">
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
                            <th>Graduate Name</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Age</th>
                            <th>Previous Experience</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($unemployed)): ?>
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    No unemployment records found
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($unemployed as $person): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($person['unemployed_id']); ?></td>
                                    <td style="font-weight: 600;">
                                        <?php 
                                            $fullName = $person['first_name'] ?? '';
                                            if (!empty($person['middle_name'])) $fullName .= ' ' . $person['middle_name'];
                                            $fullName .= ' ' . ($person['last_name'] ?? '');
                                            echo htmlspecialchars(trim($fullName) ?: 'Unknown'); 
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($person['contact_number'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                            $location = [];
                                            if (!empty($person['city_municipality'])) $location[] = $person['city_municipality'];
                                            if (!empty($person['province'])) $location[] = $person['province'];
                                            echo htmlspecialchars(implode(', ', $location) ?: 'N/A'); 
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            if ($person['date_of_birth']) {
                                                $age = date_diff(date_create($person['date_of_birth']), date_create('today'))->y;
                                                echo $age . ' years';
                                            } else {
                                                echo 'N/A';
                                            }
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
                                            <?php if ($person['has_previous_experience']): ?>
                                            <button class="btn btn-info btn-sm" onclick="viewPrevExp(<?php echo $person['graduate_id']; ?>)" title="View Previous Experience">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button class="btn btn-secondary btn-sm" onclick="editUnemployment(<?php echo $person['unemployed_id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteUnemployment(<?php echo $person['unemployed_id']; ?>, '<?php echo htmlspecialchars(trim($person['first_name'] . ' ' . $person['last_name'])); ?>')">
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
    <div id="unemploymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Add Unemployment Record</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="unemploymentForm" method="POST" action="./functions/unemployed_crud.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="unemployed_id" id="unemployedId">
                
                <div class="form-group">
                    <label class="form-label">Graduate *</label>
                    <select name="graduate_id" id="graduateId" class="form-input" required>
                        <option value="">Select Graduate</option>
                        <?php foreach ($available_graduates as $grad): ?>
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
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="has_previous_experience" id="hasPreviousExperience" value="1">
                        <span style="font-size: 0.9rem;">Has Previous Work Experience</span>
                    </label>
                    <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.5rem;">
                        Check if the graduate has any previous work experience before becoming unemployed.
                    </p>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i> Save Record
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
            <div id="prevExpContent" style="max-height: 500px; overflow-y: auto; padding: 1rem;"></div>
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
    </style>

    <script>
        function openAddModal() {
            <?php if (empty($available_graduates)): ?>
                alert('No available graduates to add. Please add more graduates first.');
                return;
            <?php endif; ?>
            
            document.getElementById('modalTitle').textContent = 'Add Unemployment Record';
            document.getElementById('formAction').value = 'create';
            document.getElementById('unemploymentForm').reset();
            document.getElementById('unemploymentModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('unemploymentModal').classList.remove('active');
        }

        function editUnemployment(id) {
            fetch(`./functions/unemployed_crud.php?action=get&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalTitle').textContent = 'Edit Unemployment Record';
                        document.getElementById('formAction').value = 'update';
                        document.getElementById('unemployedId').value = data.unemployment.unemployed_id;
                        
                        // For editing, we need to populate the dropdown with all graduates including the current one
                        const graduateSelect = document.getElementById('graduateId');
                        graduateSelect.innerHTML = '<option value="">Select Graduate</option>';
                        
                        <?php 
                        // Combine all graduates for editing
                        $all_graduates_for_edit = array_merge($available_graduates, $unemployed);
                        usort($all_graduates_for_edit, function($a, $b) {
                            return strcmp($a['last_name'], $b['last_name']);
                        });
                        ?>
                        
                        <?php foreach ($all_graduates_for_edit as $grad): ?>
                            const option<?php echo $grad['graduate_id']; ?> = document.createElement('option');
                            option<?php echo $grad['graduate_id']; ?>.value = '<?php echo $grad['graduate_id']; ?>';
                            option<?php echo $grad['graduate_id']; ?>.textContent = '<?php 
                                $name = $grad['first_name'];
                                if (!empty($grad['middle_name'])) $name .= ' ' . $grad['middle_name'];
                                $name .= ' ' . $grad['last_name'];
                                echo addslashes($name);
                            ?>';
                            if (<?php echo $grad['graduate_id']; ?> === data.unemployment.graduate_id) {
                                option<?php echo $grad['graduate_id']; ?>.selected = true;
                            }
                            graduateSelect.appendChild(option<?php echo $grad['graduate_id']; ?>);
                        <?php endforeach; ?>
                        
                        document.getElementById('hasPreviousExperience').checked = data.unemployment.has_previous_experience == 1;
                        document.getElementById('unemploymentModal').classList.add('active');
                    } else {
                        alert('Error loading unemployment data');
                    }
                })
                .catch(err => alert('Error: ' + err));
        }

        function deleteUnemployment(id, name) {
            if (confirm(`Are you sure you want to delete the unemployment record for ${name}?`)) {
                fetch('./functions/unemployed_crud.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=delete&unemployed_id=${id}`
                })
                .then(res => res.text())
                .then(() => {
                    window.location.reload();
                })
                .catch(err => alert('Error: ' + err));
            }
        }

        // Close modal on outside click
        document.getElementById('unemploymentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
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
