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

        :root {
            --accent: #3a82f6;
            --accent-glow: rgba(58, 130, 246, 0.4);
        }

        body {
            background-color: #000;
            color: #fff;
            font-family: 'Inter', sans-serif;
            overflow: hidden; /* Keeps the experience contained */
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
            box-shadow: 0 15px 40px rgba(58, 130, 246, 0.4);
        }

        /* Subtle Grid Overlay */
        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image: linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: -1;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="bg-glow"></div>
    <div class="grid-overlay"></div>

    <main class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        
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
                <p class="text-gray-500 text-[11px]">DPA 2012 Compliant data encryption.</p>
            </div>

            <div class="glass-panel p-6 space-y-4 translate-y-8 float-icon" style="animation-delay: 0.5s;">
                <i class="fas fa-chart-line text-accent text-2xl"></i>
                <h3 class="text-white font-bold text-sm">Impact</h3>
                <p class="text-gray-500 text-[11px]">Align subjects with industry needs.</p>
            </div>

            <div class="glass-panel p-6 space-y-4 float-icon" style="animation-delay: 0.2s;">
                <i class="fas fa-bolt text-accent text-2xl"></i>
                <h3 class="text-white font-bold text-sm">Fast</h3>
                <p class="text-gray-500 text-[11px]">Optimized 3-minute workflow.</p>
            </div>

            <div class="glass-panel p-6 space-y-4 translate-y-8 float-icon" style="animation-delay: 0.7s;">
                <i class="fas fa-university text-accent text-2xl"></i>
                <h3 class="text-white font-bold text-sm">Accredited</h3>
                <p class="text-gray-500 text-[11px]">Supporting quality assurance.</p>
            </div>
        </div>
    </main>

    <footer class="fixed bottom-8 w-full px-12 flex justify-between items-end">
        <div class="text-[10px] font-mono text-gray-600 uppercase tracking-[0.3em]">
            &copy; SBSITGraduatesTracer 2025
        </div>
        <div class="flex gap-6 text-gray-500 text-xs">
            <a href="#" class="hover:text-accent transition-colors">Privacy</a>
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
</body>
</html>