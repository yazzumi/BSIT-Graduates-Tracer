<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSIT Graduate Tracer | Welcome</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap');

        body {
            background-color: #000;
            color: #fff;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        /* Ambient Background Glow */
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

        /* Glass Card */
        .glass-panel {
            background: rgba(10, 10, 10, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
        }

        /* Nav Login Button & Premium Animation */
        .nav-login {
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.02);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .nav-login:hover {
            background: var(--accent);
            border-color: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 0 30px var(--accent-glow);
            color: white !important;
        }

        /* Modern Cursor */
        .cursor {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: var(--accent);
            border-radius: 50%;
            margin-left: 8px;
            animation: blink 1s infinite;
        }

        @keyframes blink { 0%, 50% { opacity: 1; } 51%, 100% { opacity: 0; } }

        /* Icon Floating Animation */
        .float-icon { animation: float 3s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* Premium Button */
        .btn-premium {
            background: #fff;
            color: #000;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.1);
        }

        .btn-premium:hover {
            transform: scale(1.05) translateY(-3px);
            background: var(--accent);
            color: #fff;
            box-shadow: 0 15px 40px var(--accent-glow);
        }

        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image: linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: -1;
        }

        /* Responsive Styles */
        @media (max-width: 1024px) {
            .bg-glow {
                width: 400px;
                height: 400px;
            }
        }

        @media (max-width: 768px) {
            body {
                overflow-y: auto;
                padding-bottom: 100px;
            }
            
            .bg-glow {
                width: 300px;
                height: 300px;
            }
            
            h1 {
                font-size: 2.5rem !important;
            }
            
            .glass-panel {
                padding: 1rem;
            }
            
            .btn-premium {
                padding: 1rem 2rem;
                font-size: 0.75rem;
            }
            
            footer {
                position: relative !important;
                bottom: auto !important;
                flex-direction: column;
                gap: 1rem;
                text-align: center;
                padding: 2rem 1rem !important;
                margin-top: 2rem;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 2rem !important;
            }
            
            .nav-login {
                padding: 0.5rem 1rem;
                font-size: 8px;
            }
            
            p {
                font-size: 0.9rem !important;
            }
        }

        /* Theme Toggle Button */
        .theme-toggle {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            z-index: 1000;
            background: rgba(10, 10, 10, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #a3a3a3;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .theme-toggle:hover {
            color: var(--accent);
            transform: scale(1.1);
        }

        /* Light Theme Overrides */
        [data-theme="light"] body {
            background-color: #f8fafc;
            color: #0f172a;
        }

        [data-theme="light"] .bg-glow {
            background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, transparent 70%);
        }

        [data-theme="light"] .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            border-color: #e2e8f0;
        }

        [data-theme="light"] .glass-panel h3 {
            color: #0f172a;
        }

        [data-theme="light"] .glass-panel p {
            color: #64748b;
        }

        [data-theme="light"] h1 {
            color: #0f172a;
        }

        [data-theme="light"] .nav-login {
            background: rgba(0, 0, 0, 0.05);
            border-color: #e2e8f0;
            color: #0f172a !important;
        }

        [data-theme="light"] .btn-premium {
            background: #0f172a;
            color: #fff;
        }

        [data-theme="light"] .theme-toggle {
            background: rgba(255, 255, 255, 0.9);
            border-color: #e2e8f0;
            color: #64748b;
        }

        [data-theme="light"] #dynamic-text {
            color: #0f172a;
        }

        [data-theme="light"] footer a,
        [data-theme="light"] footer div {
            color: #64748b;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center p-6">

    <div class="bg-glow"></div>
    <div class="grid-overlay"></div>

    <div class="w-full max-w-5xl space-y-12">
        
        <header class="flex justify-between items-end w-full">
            <div>

            </div>
            <a href="../admin/login.php"
               class="nav-login px-6 py-2 rounded-full text-[10px] font-bold tracking-[0.2em] text-white uppercase flex items-center gap-2">
                <i class="fas fa-lock text-[9px]"></i>
                Admin Login
            </a>

        </header>

        <main class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-7 space-y-8 text-left">
                <div class="space-y-2">
                    <span class="text-accent font-mono text-xs tracking-[0.5em] uppercase">College of Information Technology</span>
                    <h1 class="text-6xl md:text-7xl font-black tracking-tighter leading-none">
                        BSIT <br>
                        <span class="text-accent">TRACER.</span>
                    </h1>
                </div>

                <div class="glass-panel p-6 inline-flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-accent/20 flex items-center justify-center text-accent">
                        <i class="fas fa-terminal"></i>
                    </div>
                    <div class="text-sm">
                        <span class="text-gray-500 block font-mono text-[10px] uppercase tracking-widest">System Status</span>
                        <span id="dynamic-text" class="text-white font-medium">Initializing...</span><span class="cursor"></span>
                    </div>
                </div>

                <p class="text-gray-400 text-lg max-w-md leading-relaxed">
                    Your career journey helps shape the future of our curriculum. Securely synchronize your employment data in 3 minutes.
                </p>

                <form action="validation.php" method="POST" class="pt-4">
                    <button type="submit" class="btn-premium px-12 py-5 rounded-full text-sm font-black tracking-widest uppercase flex items-center gap-4">
                        Begin Survey <i class="fas fa-chevron-right text-[10px]"></i>
                    </button>
                </form>
            </div>

            <div class="lg:col-span-5 grid grid-cols-2 gap-4 relative">
                <div class="absolute -z-10 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 border border-accent/20 rounded-full"></div>
                
                <div class="glass-panel p-6 space-y-4 float-icon" style="animation-delay: 0s;">
                    <i class="fas fa-shield-halved text-accent text-2xl"></i>
                    <h3 class="text-white font-bold text-sm">Secure</h3>
                    <p class="text-gray-500 text-[11px]">DPA 2012 Compliant encryption.</p>
                </div>

                <div class="glass-panel p-6 space-y-4 translate-y-8 float-icon" style="animation-delay: 0.5s;">
                    <i class="fas fa-chart-line text-accent text-2xl"></i>
                    <h3 class="text-white font-bold text-sm">Impact</h3>
                    <p class="text-gray-500 text-[11px]">Directly influence IT curriculum.</p>
                </div>

                <div class="glass-panel p-6 space-y-4 float-icon" style="animation-delay: 0.2s;">
                    <i class="fas fa-bolt text-accent text-2xl"></i>
                    <h3 class="text-white font-bold text-sm">Fast</h3>
                    <p class="text-gray-500 text-[11px]">Quick 3-minute workflow.</p>
                </div>

                <div class="glass-panel p-6 space-y-4 translate-y-8 float-icon" style="animation-delay: 0.7s;">
                    <i class="fas fa-university text-accent text-2xl"></i>
                    <h3 class="text-white font-bold text-sm">Accredited</h3>
                    <p class="text-gray-500 text-[11px]">CHED QA standards support.</p>
                </div>
            </div>
        </main>
    </div>

    <footer class="fixed bottom-8 w-full px-12 flex justify-between items-end">
        <div class="text-[10px] font-mono text-gray-600 uppercase tracking-[0.3em]">
            &copy; SBSITGraduatesTracer 2025
        </div>
        <div class="flex gap-6 text-gray-500 text-xs font-bold uppercase tracking-widest">
            <a href="#" class="hover:text-accent transition-colors">Privacy</a>
            <span class="text-gray-800">/</span>
            <a href="#" class="hover:text-accent transition-colors">Terms</a>
        </div>
    </footer>

    <script>
        const dynamicTexts = [
            "Gathering alumni insights...",
            "Analyzing career paths...",
            "Improving IT standards...",
            "Syncing with industry...",
            "Optimizing curriculum..."
        ];
        
        const dynamicTextElement = document.getElementById('dynamic-text');
        let textIndex = 0;
        let charIndex = 0;
        let isDeleting = false;

        function typeWriter() {
            const currentText = dynamicTexts[textIndex];
            
            if (!isDeleting && charIndex < currentText.length) {
                dynamicTextElement.textContent = currentText.substring(0, charIndex + 1);
                charIndex++;
                setTimeout(typeWriter, 50);
            } else if (isDeleting && charIndex > 0) {
                dynamicTextElement.textContent = currentText.substring(0, charIndex - 1);
                charIndex--;
                setTimeout(typeWriter, 30);
            } else if (!isDeleting && charIndex === currentText.length) {
                isDeleting = true;
                setTimeout(typeWriter, 2000);
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                textIndex = (textIndex + 1) % dynamicTexts.length;
                setTimeout(typeWriter, 500);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(typeWriter, 1000);
        });

    </script>

    <?php include 'includes/theme_toggle.php'; ?>
</body>
</html>