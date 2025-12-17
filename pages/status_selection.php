<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSIT Tracer | Status Selection</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap');

        :root {
            --color-bg: #000000;
            --color-card: #0a0a0a;
            --color-border: #1a1a1a;
            --accent-blue: #3a82f6;
            --accent-yellow: #eab308;
            --accent-purple: #a855f7;
            --accent-red: #ef4444;
        }

        body {
            background-color: var(--color-bg);
            color: #fff;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Subtle Background Scanline Effect */
        body::before {
            content: " ";
            position: fixed;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), 
                        linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
            z-index: -1;
            background-size: 100% 2px, 3px 100%;
            pointer-events: none;
        }

        /* Status Node Base Styling */
        .status-node {
            background: var(--color-card);
            border: 1px solid var(--color-border);
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        /* Logic for selection states */
        .tech-radio:checked + .status-node {
            background: rgba(255, 255, 255, 0.03);
            transform: scale(1.02) translateY(-10px);
            z-index: 20;
        }

        /* Specialized Glows */
        .node-employed:hover, .tech-radio[value="employed.php"]:checked + .node-employed { border-color: var(--accent-blue); box-shadow: 0 0 40px rgba(58, 130, 246, 0.1); }
        .node-self:hover, .tech-radio[value="self_employed.php"]:checked + .node-self { border-color: var(--accent-yellow); box-shadow: 0 0 40px rgba(234, 179, 8, 0.1); }
        .node-ofw:hover, .tech-radio[value="ofw.php"]:checked + .node-ofw { border-color: var(--accent-purple); box-shadow: 0 0 40px rgba(168, 85, 247, 0.1); }
        .node-unemployed:hover, .tech-radio[value="unemployed.php"]:checked + .node-unemployed { border-color: var(--accent-red); box-shadow: 0 0 40px rgba(239, 68, 68, 0.1); }

        /* Icon Transition */
        .status-node i {
            transition: all 0.5s ease;
            color: #262626;
        }
        .group:hover i { transform: scale(1.1); }
        .tech-radio:checked + .status-node i { color: #fff; filter: drop-shadow(0 0 10px currentColor); }

        /* The Selection Indicator (Check-tag) */
        .check-indicator {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #262626;
            transition: all 0.3s ease;
        }
        .tech-radio:checked + .status-node .check-indicator {
            background: #fff;
            box-shadow: 0 0 15px #fff;
            transform: scale(1.5);
        }

        /* Global Progress Bar to match other steps */
        .top-progress {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 3px;
            background: #111;
        }
        .progress-fill {
            width: 80%; /* Step 5 of 6 roughly */
            height: 100%;
            background: var(--accent-blue);
            box-shadow: 0 0 20px var(--accent-blue);
        }

        /* Pulse Animation */
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(58, 130, 246, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(58, 130, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(58, 130, 246, 0); }
        }
        .pulse-active { animation: pulse 2s infinite; }
    </style>
</head>
<body>

    <div class="top-progress"><div class="progress-fill"></div></div>

    <div class="w-full max-w-7xl px-8 flex flex-col items-center">
        
        <div class="text-center mb-16">
            <span class="text-blue-500 font-mono text-xs tracking-[0.5em] uppercase mb-4 block">Deployment // Classification</span>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white tracking-tighter mb-6">Current Career State</h1>
            <p class="text-gray-500 font-medium max-w-xl mx-auto text-sm leading-relaxed">
                Select your current status to initialize the specialized tracking module.
            </p>
        </div>

        <form id="routingForm" class="w-full grid grid-cols-1 md:grid-cols-4 gap-6 mb-20">
            
            <label class="cursor-pointer group">
                <input type="radio" name="status" value="employed.php" class="hidden tech-radio" onclick="setRoute('employed.php', 'blue')">
                <div class="status-node node-employed h-72 rounded-3xl p-8 flex flex-col items-center justify-center text-center">
                    <div class="check-indicator"></div>
                    <div class="text-5xl mb-6"><i class="fas fa-briefcase"></i></div>
                    <h3 class="text-lg text-white font-bold tracking-tight">Employed</h3>
                    <p class="text-[10px] text-gray-600 uppercase tracking-widest mt-2">Wage Employment</p>
                </div>
            </label>

            <label class="cursor-pointer group">
                <input type="radio" name="status" value="self_employed.php" class="hidden tech-radio" onclick="setRoute('self_employed.php', 'yellow')">
                <div class="status-node node-self h-72 rounded-3xl p-8 flex flex-col items-center justify-center text-center">
                    <div class="check-indicator"></div>
                    <div class="text-5xl mb-6"><i class="fas fa-rocket"></i></div>
                    <h3 class="text-lg text-white font-bold tracking-tight">Self-Employed</h3>
                    <p class="text-[10px] text-gray-600 uppercase tracking-widest mt-2">Business / Freelance</p>
                </div>
            </label>

            <label class="cursor-pointer group">
                <input type="radio" name="status" value="ofw.php" class="hidden tech-radio" onclick="setRoute('ofw.php', 'purple')">
                <div class="status-node node-ofw h-72 rounded-3xl p-8 flex flex-col items-center justify-center text-center">
                    <div class="check-indicator"></div>
                    <div class="text-5xl mb-6"><i class="fas fa-globe-asia"></i></div>
                    <h3 class="text-lg text-white font-bold tracking-tight">OFW</h3>
                    <p class="text-[10px] text-gray-600 uppercase tracking-widest mt-2">International Career</p>
                </div>
            </label>

            <label class="cursor-pointer group">
                <input type="radio" name="status" value="unemployed.php" class="hidden tech-radio" onclick="setRoute('unemployed.php', 'red')">
                <div class="status-node node-unemployed h-72 rounded-3xl p-8 flex flex-col items-center justify-center text-center">
                    <div class="check-indicator"></div>
                    <div class="text-5xl mb-6"><i class="fas fa-user-ninja"></i></div>
                    <h3 class="text-lg text-white font-bold tracking-tight">Unemployed</h3>
                    <p class="text-[10px] text-gray-600 uppercase tracking-widest mt-2">Available for Work</p>
                </div>
            </label>

        </form>

        <div class="flex flex-col items-center gap-6">
            <button type="button" onclick="submitRouting()" id="initBtn" disabled 
                class="bg-white/5 text-gray-700 border border-white/5 font-black px-20 py-5 rounded-full transition-all duration-500 cursor-not-allowed uppercase text-xs tracking-[0.3em]">
                Initialize Module
            </button>
            
            <div class="flex items-center gap-3">
                <div id="status-light" class="w-2 h-2 rounded-full bg-gray-800 transition-all duration-500"></div>
                <p id="log-text" class="text-[10px] font-mono text-gray-600 uppercase tracking-[0.2em]">System: Awaiting Node Selection</p>
            </div>
        </div>
    </div>

    <script>
        let targetRoute = "";

        function setRoute(page, color) {
            targetRoute = page;
            const btn = document.getElementById('initBtn');
            const log = document.getElementById('log-text');
            const light = document.getElementById('status-light');
            
            // Define colors
            const colors = {
                blue: '#3a82f6',
                yellow: '#eab308',
                purple: '#a855f7',
                red: '#ef4444'
            };

            // Enable Button with "Light-up" effect
            btn.disabled = false;
            btn.classList.remove('bg-white/5', 'text-gray-700', 'cursor-not-allowed', 'border-white/5');
            btn.classList.add('bg-white', 'text-black', 'hover:scale-110', 'active:scale-95', 'shadow-[0_20px_50px_rgba(255,255,255,0.1)]');
            
            // Update System Status
            light.style.backgroundColor = colors[color];
            light.classList.add('pulse-active');
            light.style.boxShadow = `0 0 15px ${colors[color]}`;
            
            log.innerText = `Node Linked: ${page.replace('.php', '').toUpperCase()}`;
            log.style.color = colors[color];
        }

        function submitRouting() {
            if(targetRoute !== "") {
                const btn = document.getElementById('initBtn');
                btn.innerHTML = 'Establishing Link... <i class="fas fa-circle-notch fa-spin ml-3"></i>';
                btn.style.opacity = "0.7";
                
                setTimeout(() => {
                    window.location.href = targetRoute;
                }, 1000);
            }
        }
    </script>
</body>
</html>