<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSIT Tracer | Status Selection</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

        /* Scanline effect remains same */
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

        .status-node {
            background: var(--color-card);
            border: 1px solid var(--color-border);
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            animation: cardFloat 6s ease-in-out infinite;
        }

        .tech-radio:checked + .status-node {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02));
            transform: scale(1.08) translateY(-15px) rotateX(5deg);
            z-index: 20;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3), inset 0 0 30px rgba(255, 255, 255, 0.1);
        }

        /* Enhanced Glow effects matched to DB status */
        .node-employed:hover, .tech-radio[value="Employed"] + .node-employed { 
            border-color: var(--accent-blue); 
            box-shadow: 0 0 60px rgba(58, 130, 246, 0.3), inset 0 0 20px rgba(58, 130, 246, 0.1);
            transform: translateY(-5px) scale(1.02);
        }
        .node-self:hover, .tech-radio[value="Self-Employed"] + .node-self { 
            border-color: var(--accent-yellow); 
            box-shadow: 0 0 60px rgba(234, 179, 8, 0.3), inset 0 0 20px rgba(234, 179, 8, 0.1);
            transform: translateY(-5px) scale(1.02);
        }
        .node-ofw:hover, .tech-radio[value="OFW"] + .node-ofw { 
            border-color: var(--accent-purple); 
            box-shadow: 0 0 60px rgba(168, 85, 247, 0.3), inset 0 0 20px rgba(168, 85, 247, 0.1);
            transform: translateY(-5px) scale(1.02);
        }
        .node-unemployed:hover, .tech-radio[value="Unemployed"] + .node-unemployed { 
            border-color: var(--accent-red); 
            box-shadow: 0 0 60px rgba(239, 68, 68, 0.3), inset 0 0 20px rgba(239, 68, 68, 0.1);
            transform: translateY(-5px) scale(1.02);
        }

        .status-node i { 
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1); 
            color: #262626;
            transform: scale(1);
        }
        .tech-radio:checked + .status-node i { 
            color: #fff; 
            filter: drop-shadow(0 0 15px currentColor);
            transform: scale(1.2) rotateY(10deg);
        }

        .check-indicator {
            position: absolute; top: 20px; right: 20px; width: 10px; height: 10px;
            border-radius: 50%; background: #262626; transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .tech-radio:checked + .status-node .check-indicator { 
            background: #fff; 
            box-shadow: 0 0 25px #fff, 0 0 50px currentColor; 
            transform: scale(2);
        }

        .pulse-active { animation: enhancedPulse 2s infinite; }
        @keyframes enhancedPulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.6); }
            50% { box-shadow: 0 0 0 20px rgba(255, 255, 255, 0.2); }
            100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
        }

        /* Floating animation for cards */
        @keyframes cardFloat {
            0%, 100% { transform: translateY(0px) rotateX(0deg); }
            50% { transform: translateY(-10px) rotateX(2deg); }
        }
        .status-node:nth-child(2) { animation-delay: 0.5s; }
        .status-node:nth-child(3) { animation-delay: 1s; }
        .status-node:nth-child(4) { animation-delay: 1.5s; }

        /* Responsive Styles */
        @media (max-width: 1024px) {
            .status-node {
                height: 200px !important;
            }
        }

        @media (max-width: 768px) {
            body {
                overflow-y: auto;
                height: auto;
                min-height: 100vh;
                padding: 2rem 0;
            }
            
            h1 {
                font-size: 2rem !important;
            }
            
            .status-node {
                height: 150px !important;
                padding: 1rem !important;
            }
            
            .status-node .text-5xl {
                font-size: 2rem !important;
                margin-bottom: 1rem !important;
            }
            
            .status-node h3 {
                font-size: 0.9rem !important;
            }
            
            #initBtn {
                padding: 1rem 2rem !important;
                font-size: 10px !important;
            }
            
            .grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 1rem !important;
            }
            
            .mb-20 {
                margin-bottom: 3rem !important;
            }
            
            .mb-16 {
                margin-bottom: 2rem !important;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem !important;
            }
            
            p {
                font-size: 0.8rem !important;
            }
            
            .grid {
                grid-template-columns: 1fr !important;
            }
            
            .status-node {
                height: 120px !important;
            }
            
            .status-node .text-5xl {
                font-size: 1.5rem !important;
            }
            
            #initBtn {
                padding: 0.75rem 1.5rem !important;
                width: 100%;
            }
            
            .check-indicator {
                top: 10px;
                right: 10px;
                width: 8px;
                height: 8px;
            }
        }
    </style>
</head>
<body>

    <div class="w-full max-w-7xl px-8 flex flex-col items-center">
        
        <div class="text-center mb-16">
            <span class="text-blue-500 font-mono text-xs tracking-[0.5em] uppercase mb-4 block">Classification // Step 02</span>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white tracking-tighter mb-6">Current Career State</h1>
            <p class="text-gray-500 font-medium max-w-xl mx-auto text-sm leading-relaxed">
                Select your current status to initialize the specialized tracking module for your employment profile.
            </p>
        </div>

        <form id="routingForm" class="w-full grid grid-cols-1 md:grid-cols-4 gap-6 mb-20">
            
            <label class="cursor-pointer group">
                <input type="radio" name="status" value="Employed" class="hidden tech-radio" onclick="setRoute('employed.php', 'blue')">
                <div class="status-node node-employed h-72 rounded-3xl p-8 flex flex-col items-center justify-center text-center">
                    <div class="check-indicator"></div>
                    <div class="text-5xl mb-6"><i class="fas fa-briefcase"></i></div>
                    <h3 class="text-lg text-white font-bold tracking-tight">Employed</h3>
                    <p class="text-[10px] text-gray-600 uppercase tracking-widest mt-2">Wage Employment</p>
                </div>
            </label>

            <label class="cursor-pointer group">
                <input type="radio" name="status" value="Self-Employed" class="hidden tech-radio" onclick="setRoute('self_employed.php', 'yellow')">
                <div class="status-node node-self h-72 rounded-3xl p-8 flex flex-col items-center justify-center text-center">
                    <div class="check-indicator"></div>
                    <div class="text-5xl mb-6"><i class="fas fa-rocket"></i></div>
                    <h3 class="text-lg text-white font-bold tracking-tight">Self-Employed</h3>
                    <p class="text-[10px] text-gray-600 uppercase tracking-widest mt-2">Business / Freelance</p>
                </div>
            </label>

            <label class="cursor-pointer group">
                <input type="radio" name="status" value="OFW" class="hidden tech-radio" onclick="setRoute('ofw.php', 'purple')">
                <div class="status-node node-ofw h-72 rounded-3xl p-8 flex flex-col items-center justify-center text-center">
                    <div class="check-indicator"></div>
                    <div class="text-5xl mb-6"><i class="fas fa-globe-asia"></i></div>
                    <h3 class="text-lg text-white font-bold tracking-tight">OFW</h3>
                    <p class="text-[10px] text-gray-600 uppercase tracking-widest mt-2">International Career</p>
                </div>
            </label>

            <label class="cursor-pointer group">
                <input type="radio" name="status" value="Unemployed" class="hidden tech-radio" onclick="setRoute('unemployed.php', 'red')">
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
        let selectedStatus = "";

        function setRoute(page, color) {
            targetRoute = page;
            // Kunin ang text value para sa DB (Employed, Self-Employed, etc.)
            selectedStatus = document.querySelector('input[name="status"]:checked').value;

            const btn = document.getElementById('initBtn');
            const log = document.getElementById('log-text');
            const light = document.getElementById('status-light');
            
            const colors = { blue: '#3a82f6', yellow: '#eab308', purple: '#a855f7', red: '#ef4444' };

            btn.disabled = false;
            btn.className = "bg-white text-black font-black px-20 py-5 rounded-full transition-all duration-500 hover:scale-110 active:scale-95 shadow-[0_20px_50px_rgba(255,255,255,0.1)] uppercase text-xs tracking-[0.3em]";
            
            light.style.backgroundColor = colors[color];
            light.classList.add('pulse-active');
            light.style.boxShadow = `0 0 15px ${colors[color]}`;
            
            log.innerText = `READY: Initializing ${selectedStatus.toUpperCase()} Protocol`;
            log.style.color = colors[color];
        }

        function submitRouting() {
            if(targetRoute !== "") {
                const btn = document.getElementById('initBtn');
                
                // 1. Kunin ang existing data mula sa Step 1
                let tracerData = JSON.parse(localStorage.getItem('tracer_payload')) || {};
                
                // 2. Idagdag ang napiling Employment Status
                tracerData.employment_status = selectedStatus;
                
                // 3. I-save pabalik sa LocalStorage
                localStorage.setItem('tracer_payload', JSON.stringify(tracerData));

                // UI feedback
                btn.innerHTML = 'Establishing Link... <i class="fas fa-circle-notch fa-spin ml-3"></i>';
                btn.style.opacity = "0.7";
                
                setTimeout(() => {
                    window.location.href = targetRoute;
                }, 1000);
            }
        }
    </script>

    <?php include 'includes/theme_toggle.php'; ?>
</body>
</html>