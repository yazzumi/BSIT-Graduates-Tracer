<?php
require_once "includes/auth_check.php";
require_once "../config/db_conn.php";

try {
    // 1. Core Counts
    $total_graduates = $pdo->query("SELECT COUNT(*) as count FROM graduates")->fetch()['count'];
    $completed_surveys = $pdo->query("SELECT COUNT(*) as count FROM validated_graduates WHERE survey_completed = 1")->fetch()['count'];
    
    // 2. Data for Charts
    $employment_breakdown = $pdo->query("
        SELECT employment_type, COUNT(*) as count
        FROM graduates
        WHERE employment_type IS NOT NULL
        GROUP BY employment_type
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Prepare JS variables for Chart.js
    $chart_labels = [];
    $chart_values = [];
    foreach ($employment_breakdown as $row) {
        $chart_labels[] = $row['employment_type'];
        $chart_values[] = $row['count'];
    }

    // 3. IT Relevance Data - Analyze positions from all employment tables
    $it_positions = ['IT Academician', 'Teacher', 'Programmer', 'Developer', 'Network Administrator', 
                     'IT Support', 'VA', 'IT Engineer', 'IT Analyst', 'Quality Control', 
                     'IT Researcher', 'IT Technician', 'IT Consultant', 'Computer Forensic', 
                     'Investigator', 'Software', 'Web', 'System', 'Data', 'Database', 
                     'Cyber', 'Security', 'Cloud', 'DevOps', 'Full Stack', 'Backend', 'Frontend'];
    
    // Build LIKE conditions for IT positions
    $like_conditions = [];
    foreach ($it_positions as $pos) {
        $like_conditions[] = "position LIKE '%" . $pos . "%'";
    }
    $it_where = implode(' OR ', $like_conditions);
    
    // Count IT-related jobs from employment_details
    $it_employed = $pdo->query("SELECT COUNT(*) as count FROM employment_details WHERE $it_where")->fetch()['count'];
    $total_employed = $pdo->query("SELECT COUNT(*) as count FROM employment_details")->fetch()['count'];
    $non_it_employed = $total_employed - $it_employed;
    
    // Count IT-related jobs from OFW
    $it_ofw = $pdo->query("SELECT COUNT(*) as count FROM ofw_details WHERE $it_where")->fetch()['count'];
    $total_ofw = $pdo->query("SELECT COUNT(*) as count FROM ofw_details")->fetch()['count'];
    $non_it_ofw = $total_ofw - $it_ofw;
    
    // Self-employed - check nature_of_business for IT keywords
    $self_like_conditions = [];
    foreach ($it_positions as $pos) {
        $self_like_conditions[] = "nature_of_business LIKE '%" . $pos . "%'";
    }
    $self_it_where = implode(' OR ', $self_like_conditions);
    $it_self = $pdo->query("SELECT COUNT(*) as count FROM self_employment_details WHERE $self_it_where")->fetch()['count'];
    $total_self = $pdo->query("SELECT COUNT(*) as count FROM self_employment_details")->fetch()['count'];
    $non_it_self = $total_self - $it_self;
    
    // Total IT relevance
    $total_it_related = $it_employed + $it_ofw + $it_self;
    $total_non_it = $non_it_employed + $non_it_ofw + $non_it_self;
    $total_jobs = $total_it_related + $total_non_it;
    
    // Prepare IT relevance chart data
    $it_relevance_labels = ['IT-Related Jobs', 'Non-IT Jobs'];
    $it_relevance_values = [$total_it_related, $total_non_it];

    // 4. Recent Activity (Simplified)
    $recent_submissions = $pdo->query("
        SELECT g.first_name, g.last_name, g.employment_type, vg.survey_completion_date
        FROM graduates g
        JOIN validated_graduates vg ON g.graduate_id = vg.graduate_id
        WHERE vg.survey_completed = 1
        ORDER BY vg.survey_completion_date DESC
        LIMIT 6
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error = "System Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | BSIT Tracer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php include 'includes/styles.php'; ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --sidebar-width: 260px;
            --bg-dark: #09090b;
            --card-bg: #18181b;
            --border: #27272a;
            --accent: #3b82f6;
            --text-muted: #a1a1aa;
        }

        body { 
            background-color: var(--bg-dark); 
            color: #fafafa; 
            font-family: 'Inter', sans-serif;
            margin: 0;
            display: flex;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #000;
            border-right: 1px solid var(--border);
            position: fixed;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
        }

        .brand {
            font-weight: 700;
            font-size: 1.1rem;
            padding: 0 1rem 2rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: 0.2s;
            margin-bottom: 4px;
        }

        .nav-item:hover, .nav-item.active {
            background: var(--card-bg);
            color: #fff;
        }

        .nav-item.active { border: 1px solid var(--border); }

        .nav-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #52525b;
            font-weight: 700;
            margin: 1.5rem 0 0.5rem 1rem;
        }

        /* --- Main Content --- */
        .content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 3rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 3rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1.5rem;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.5rem;
        }

        .col-4 { grid-column: span 4; }
        .col-8 { grid-column: span 8; }
        .col-12 { grid-column: span 12; }

        .stat-val { font-size: 2rem; font-weight: 700; margin-top: 0.5rem; }
        .stat-lab { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; }

        /* --- Chart Styling --- */
        .chart-box { height: 300px; width: 100%; position: relative; }

        /* --- Mini Table --- */
        .activity-list { list-style: none; padding: 0; margin: 0; }
        .activity-item {
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .activity-item:last-child { border: none; }

        .logout-link { margin-top: auto; color: #ef4444; }

        /* Mobile Menu Toggle */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.75rem;
            color: #fff;
            cursor: pointer;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        /* Responsive Styles */
        @media (max-width: 1024px) {
            .col-4 { grid-column: span 6; }
            .col-8 { grid-column: span 12; }
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1000;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay.open {
                display: block;
            }

            .content {
                margin-left: 0;
                width: 100%;
                padding: 1.5rem;
                padding-top: 4rem;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .header > div:last-child {
                text-align: left !important;
            }

            .col-4, .col-8, .col-12 {
                grid-column: span 12;
            }

            .grid {
                gap: 1rem;
            }

            .stat-val {
                font-size: 1.5rem;
            }

            .chart-box {
                height: 250px;
            }
        }

        @media (max-width: 480px) {
            .content {
                padding: 1rem;
                padding-top: 4rem;
            }

            h1 {
                font-size: 1.4rem !important;
            }

            .card {
                padding: 1rem;
            }

            .stat-val {
                font-size: 1.25rem;
            }

            .chart-box {
                height: 200px;
            }
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
                <h1 style="margin: 0; font-size: 1.8rem;">Executive Dashboard</h1>
                <p style="color: var(--text-muted); margin: 5px 0 0;">Overview of alumni employment status</p>
            </div>
            <div style="text-align: right">
                <div style="font-size: 0.8rem; color: var(--text-muted);">Admin Session</div>
                <div style="font-weight: 600;"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
            </div>
        </div>

        <div class="grid">
            <div class="card col-4">
                <div class="stat-lab">Database Records</div>
                <div class="stat-val"><?php echo number_format($total_graduates); ?></div>
            </div>
            <div class="card col-4">
                <div class="stat-lab">Survey Participation</div>
                <div class="stat-val" style="color: var(--accent);"><?php echo number_format($completed_surveys); ?></div>
            </div>
            <div class="card col-4">
                <div class="stat-lab">Participation Rate</div>
                <div class="stat-val"><?php echo round(($completed_surveys / max($total_graduates, 1)) * 100, 1); ?>%</div>
            </div>

            <div class="card col-8">
                <div class="stat-lab" style="margin-bottom: 1.5rem;">Employment Distribution</div>
                <div class="chart-box">
                    <canvas id="employmentChart"></canvas>
                </div>
            </div>

            <div class="card col-4">
                <div class="stat-lab" style="margin-bottom: 1.5rem;">IT Industry Relevance</div>
                <div class="chart-box" style="height: 250px;">
                    <canvas id="itRelevanceChart"></canvas>
                </div>
                <div style="display: flex; justify-content: space-around; margin-top: 1rem; font-size: 0.75rem;">
                    <div style="text-align: center;">
                        <div style="color: #22c55e; font-weight: 700; font-size: 1.2rem;"><?php echo $total_it_related; ?></div>
                        <div style="color: var(--text-muted);">IT-Related</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="color: #f59e0b; font-weight: 700; font-size: 1.2rem;"><?php echo $total_non_it; ?></div>
                        <div style="color: var(--text-muted);">Non-IT</div>
                    </div>
                </div>
            </div>

            <div class="card col-4">
                <div class="stat-lab" style="margin-bottom: 1rem;">Recent Submissions</div>
                <ul class="activity-list">
                    <?php foreach ($recent_submissions as $sub): ?>
                    <li class="activity-item">
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 600;"><?php echo $sub['first_name'] . ' ' . $sub['last_name']; ?></div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);"><?php echo $sub['employment_type']; ?></div>
                        </div>
                        <div style="font-size: 0.7rem; color: #52525b; font-family: monospace;">
                            <?php echo date('M d', strtotime($sub['survey_completion_date'])); ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <a href="graduates.php" style="display: block; text-align: center; font-size: 0.75rem; color: var(--accent); margin-top: 1.5rem; text-decoration: none; font-weight: 600;">VIEW FULL LOGS</a>
            </div>

            <div class="card col-4">
                <div class="stat-lab" style="margin-bottom: 1rem;">IT Relevance Rate</div>
                <div class="stat-val" style="color: #22c55e;"><?php echo $total_jobs > 0 ? round(($total_it_related / $total_jobs) * 100, 1) : 0; ?>%</div>
                <p style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.5rem;">of employed graduates work in IT-related positions</p>
            </div>
        </div>
    </main>

    <script>
        // Employment Distribution Chart
        const ctx = document.getElementById('employmentChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Graduates',
                    data: <?php echo json_encode($chart_values); ?>,
                    backgroundColor: '#3b82f6',
                    borderRadius: 6,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#27272a' },
                        ticks: { color: '#71717a', font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#71717a', font: { size: 10 } }
                    }
                }
            }
        });

        // IT Relevance Doughnut Chart
        const itCtx = document.getElementById('itRelevanceChart').getContext('2d');
        new Chart(itCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($it_relevance_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($it_relevance_values); ?>,
                    backgroundColor: ['#22c55e', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            color: '#a1a1aa',
                            font: { size: 10 },
                            padding: 15
                        }
                    }
                }
            }
        });

        // Mobile Sidebar Toggle
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
            document.querySelector('.sidebar-overlay').classList.toggle('open');
        }
    </script>
</body>
</html>