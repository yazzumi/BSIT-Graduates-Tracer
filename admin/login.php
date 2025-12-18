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

        <div class="notice-banner">
            <i class="fas fa-lock text-danger text-xs"></i>
            <span class="text-[10px] text-danger font-bold uppercase tracking-widest text-left">
                Authorized Personnel Only
            </span>
        </div>

        <form action="auth.php" method="POST" class="text-left">
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