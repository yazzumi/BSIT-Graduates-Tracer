<?php
session_start();
require_once "../config/db_conn.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Get statistics and survey data
try {
    // Total graduates
    $total_graduates = $pdo->query("SELECT COUNT(*) as count FROM graduates")->fetch()['count'];
    
    // Total completed surveys
    $completed_surveys = $pdo->query("SELECT COUNT(*) as count FROM validated_graduates WHERE survey_completed = 1")->fetch()['count'];
    
    // Recent submissions
    $recent_submissions = $pdo->query("
        SELECT g.first_name, g.last_name, g.employment_type, vg.survey_completion_date, vg.student_id
        FROM graduates g
        JOIN validated_graduates vg ON g.graduate_id = vg.graduate_id
        WHERE vg.survey_completed = 1
        ORDER BY vg.survey_completion_date DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Employment breakdown
    $employment_breakdown = $pdo->query("
        SELECT employment_type, COUNT(*) as count
        FROM graduates
        WHERE employment_type IS NOT NULL
        GROUP BY employment_type
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Detailed survey responses
    $detailed_responses = $pdo->query("
        SELECT 
            g.graduate_id, g.first_name, g.last_name, g.employment_type,
            g.contact_number, g.date_of_birth,
            vg.student_id, vg.survey_completion_date,
            CASE 
                WHEN g.employment_type = 'Employed' THEN (
                    SELECT CONCAT(ed.company_name, ' - ', ed.position)
                    FROM employment_details ed
                    WHERE ed.graduate_id = g.graduate_id
                    LIMIT 1
                )
                WHEN g.employment_type = 'OFW' THEN (
                    SELECT CONCAT(od.company_name, ' - ', od.position, ' (', od.country, ')')
                    FROM ofw_details od
                    WHERE od.graduate_id = g.graduate_id
                    LIMIT 1
                )
                WHEN g.employment_type = 'Self-Employed' THEN (
                    SELECT CONCAT('Business: ', sed.nature_of_business)
                    FROM self_employment_details sed
                    WHERE sed.graduate_id = g.graduate_id
                    LIMIT 1
                )
                WHEN g.employment_type = 'Unemployed' THEN 'Unemployed'
                ELSE 'Unknown'
            END as current_position
        FROM graduates g
        JOIN validated_graduates vg ON g.graduate_id = vg.graduate_id
        WHERE vg.survey_completed = 1
        ORDER BY vg.survey_completion_date DESC
        LIMIT 50
    ")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = "Error fetching data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | BSIT Tracer</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: #0a0a0a; 
            color: #fff; 
            font-family: 'Inter', sans-serif;
        }
        .header {
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #333;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #3a82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .logout-btn {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.2);
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .stat-card {
            background: linear-gradient(135deg, #1a1a1a, #252525);
            border: 1px solid #333;
            border-radius: 16px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3a82f6, #8b5cf6, #3a82f6);
            animation: shimmer 2s linear infinite;
        }
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #3a82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stat-label {
            color: #888;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .section {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #fff;
        }
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #333;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: #1a1a1a;
        }
        .data-table th {
            background: #252525;
            color: #3a82f6;
            font-weight: 600;
            text-align: left;
            padding: 1rem;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #3a82f6;
        }
        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #333;
            color: #ccc;
        }
        .data-table tr:hover {
            background: rgba(58, 130, 246, 0.05);
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-employed { background: #10b981; color: white; }
        .badge-ofw { background: #3b82f6; color: white; }
        .badge-self-employed { background: #f59e0b; color: black; }
        .badge-unemployed { background: #6b7280; color: white; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div>
                <div class="logo">BSIT TRACER ADMIN</div>
                <p style="font-size: 0.75rem; color: #666; margin-top: 0.25rem;">
                    Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? 'Administrator'); ?>
                </p>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </header>

    <div class="container">
        <?php if (isset($error)): ?>
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($total_graduates); ?></div>
                <div class="stat-label">Total Graduates</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($completed_surveys); ?></div>
                <div class="stat-label">Completed Surveys</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo round(($completed_surveys / max($total_graduates, 1)) * 100, 1); ?>%</div>
                <div class="stat-label">Completion Rate</div>
            </div>
        </div>

        <!-- Employment Breakdown -->
        <div class="section">
            <h2 class="section-title">Employment Breakdown</h2>
            <div class="stats-grid">
                <?php foreach ($employment_breakdown as $stat): ?>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo number_format($stat['count']); ?></div>
                        <div class="stat-label"><?php echo htmlspecialchars($stat['employment_type']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recent Submissions -->
        <div class="section">
            <h2 class="section-title">Recent Submissions</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Student ID</th>
                            <th>Employment Type</th>
                            <th>Date Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_submissions as $submission): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($submission['first_name'] . ' ' . $submission['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($submission['student_id']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $submission['employment_type'])); ?>">
                                        <?php echo htmlspecialchars($submission['employment_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y g:i A', strtotime($submission['survey_completion_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detailed Responses -->
        <div class="section">
            <h2 class="section-title">All Survey Responses</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Employment Type</th>
                            <th>Current Position</th>
                            <th>Date Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detailed_responses as $response): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($response['graduate_id']); ?></td>
                                <td><?php echo htmlspecialchars($response['first_name'] . ' ' . $response['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($response['contact_number']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $response['employment_type'])); ?>">
                                        <?php echo htmlspecialchars($response['employment_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($response['current_position']); ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($response['survey_completion_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
