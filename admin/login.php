<?php
session_start();
require_once "../config/db_conn.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        try {
            // Query admin user from existing admin table
            $stmt = $pdo->prepare("SELECT admin_id, username, password FROM admin WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin) {
                // Check password (supports both plain text and hashed passwords)
                $passwordMatch = false;
                
                // Try hashed password first
                if (password_verify($password, $admin['password'])) {
                    $passwordMatch = true;
                } 
                // Fallback to plain text comparison for existing passwords
                elseif ($password === $admin['password']) {
                    $passwordMatch = true;
                }
                
                if ($passwordMatch) {
                    // Set session variables
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['admin_id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_name'] = $admin['username']; // Use username as name
                    
                    // Redirect to dashboard
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = 'Invalid username or password';
                }
            } else {
                $error = 'Invalid username or password';
            }
        } catch (PDOException $e) {
            error_log("Admin login error: " . $e->getMessage());
            $error = 'Database error. Please try again later.';
        }
    } else {
        $error = 'Please enter both username and password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSIT Tracer | Admin Login</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap');

        :root {
            --accent: #3a82f6;
            --accent-glow: rgba(58, 130, 246, 0.4);
            --danger: #ef4444;
        }

        body {
            background-color: #000;
            color: #fff;
            font-family: 'Inter', sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
        }

        /* Same Background as Welcome Page */
        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image: linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: -1;
        }

        .bg-glow {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--accent-glow) 0%, transparent 70%);
            filter: blur(80px);
            z-index: -1;
            opacity: 0.5;
        }

        /* Clean Glass Card */
        .glass-card {
            background: rgba(10, 10, 10, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            width: 100%;
            max-width: 400px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-field {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 14px 16px 14px 45px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 15px rgba(58, 130, 246, 0.2);
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.3);
            font-size: 14px;
        }

        /* Simple Notice Banner */
        .notice-banner {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Matching Premium Button */
        .btn-login {
            width: 100%;
            background: #fff;
            color: #000;
            padding: 14px;
            border-radius: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .btn-login:hover {
            background: var(--accent);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px var(--accent-glow);
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .back-link:hover { color: #fff; }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .bg-glow {
                width: 300px;
                height: 300px;
            }
            
            .glass-card {
                margin: 1rem;
                padding: 30px 25px;
            }
        }

        @media (max-width: 480px) {
            .glass-card {
                padding: 25px 20px;
            }
            
            h2 {
                font-size: 1.5rem !important;
            }
            
            .input-field {
                padding: 12px 14px 12px 40px;
                font-size: 13px;
            }
            
            .btn-login {
                padding: 12px;
                font-size: 11px;
            }
        }
    </style>
</head>
<body>

    <div class="grid-overlay"></div>
    <div class="bg-glow"></div>

    <div class="glass-card text-center">
        <div class="w-12 h-12 bg-accent/20 rounded-xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-user-shield text-accent"></i>
        </div>

        <h2 class="text-2xl font-black tracking-tight mb-2">Admin Login</h2>
        <p class="text-gray-500 text-[10px] uppercase tracking-[0.2em] mb-6">Management Portal</p>

        <?php if (!empty($error)): ?>
        <div class="notice-banner" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3);">
            <i class="fas fa-exclamation-triangle text-danger text-xs"></i>
            <span class="text-[10px] text-danger font-bold uppercase tracking-widest text-left">
                <?php echo htmlspecialchars($error); ?>
            </span>
        </div>
        <?php else: ?>
        <div class="notice-banner">
            <i class="fas fa-lock text-danger text-xs"></i>
            <span class="text-[10px] text-danger font-bold uppercase tracking-widest text-left">
                Authorized Personnel Only
            </span>
        </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="text-left">
            <div class="input-group">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="username" class="input-field" placeholder="Username" required>
            </div>

            <div class="input-group">
                <i class="fas fa-key input-icon"></i>
                <input type="password" name="password" class="input-field" placeholder="Password" required>
            </div>

            <button type="submit" class="btn-login">
                Login <i class="fas fa-sign-in-alt ml-2"></i>
            </button>
        </form>

        <a href="../pages/welcome_page.php" class="back-link">
            <i class="fas fa-arrow-left mr-2"></i> Back to Home
        </a>
    </div>

</body>
</html>