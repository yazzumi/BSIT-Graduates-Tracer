<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Profile | BSIT Tracer</title>
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

        /* Wizard Containers */
        .step-container {
            display: none;
            max-width: 1000px;
            width: 100%;
            margin: 0 auto;
            animation: moveUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .step-container.active { 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
            height: 85vh; 
        }

        @keyframes moveUp {
            from { opacity: 0; transform: translateY(40px); filter: blur(10px); }
            to { opacity: 1; transform: translateY(0); filter: blur(0); }
        }

        /* Input Styling */
        .input-focus {
            background: transparent;
            border-bottom: 2px solid #262626;
            font-size: 2.8rem;
            text-align: center;
            width: 100%;
            padding: 1rem;
            transition: all 0.4s ease;
            font-weight: 300;
        }
        .input-focus:focus { 
            outline: none; 
            border-color: #eab308; /* Yellow accent for Self-Employed */
            color: #eab308; 
            font-weight: 600;
        }

        .progress-bar { 
            height: 4px; 
            background: #1a1a1a; 
            position: fixed; 
            top: 0; left: 0; 
            z-index: 100; 
            width: 100%;
        }
        .progress-fill { 
            height: 100%; 
            background: #eab308; 
            box-shadow: 0 0 15px #eab308; 
            width: 0%; 
            transition: width 0.6s ease; 
        }

        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); }
    </style>
</head>
<body>

    <div class="progress-bar">
        <div class="progress-fill" id="progressFill"></div>
    </div>

    <form id="selfEmployedForm" class="px-6 h-screen flex items-center">
        
        <div class="step-container active" id="step-0">
            <span class="text-yellow-500 font-mono text-xs tracking-[0.5em] mb-4 uppercase">01 // Operation</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center tracking-tighter">Nature of Business</h1>
            <input type="text" name="business_nature" placeholder="e.g. E-commerce, Freelance Dev, Retail" class="input-focus" autocomplete="off">
        </div>

        <div class="step-container" id="step-1">
            <span class="text-yellow-500 font-mono text-xs tracking-[0.5em] mb-4 uppercase">02 // Location</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center tracking-tighter">Business Address</h1>
            <input type="text" name="business_address" placeholder="City, Province, or Online" class="input-focus !text-2xl" autocomplete="off">
        </div>

        <div class="step-container" id="step-2">
            <span class="text-yellow-500 font-mono text-xs tracking-[0.5em] mb-4 uppercase">03 // Timeline</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center tracking-tighter">Establishment Period</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 w-full max-w-2xl">
                <div class="flex flex-col">
                    <label class="text-[10px] text-yellow-500 mb-4 uppercase tracking-[0.3em] font-bold text-center">Date Started</label>
                    <input type="date" name="date_started" class="bg-transparent border-b-2 border-gray-800 p-4 focus:border-yellow-500 outline-none text-white text-center text-xl transition-all">
                </div>
                <div class="flex flex-col relative">
                    <label class="text-[10px] text-yellow-500 mb-4 uppercase tracking-[0.3em] font-bold text-center">Current Status</label>
                    <div class="p-4 border-b-2 border-gray-800 text-center text-xl text-gray-500">Presently Active</div>
                    <p class="text-[9px] mt-4 text-center text-gray-600 uppercase tracking-widest leading-relaxed">System logs this as your current primary activity.</p>
                </div>
            </div>
        </div>

        <div class="step-container" id="step-3">
            <span class="text-yellow-500 font-mono text-xs tracking-[0.5em] mb-4 uppercase">04 // History</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center tracking-tighter">Previous Experiences?</h1>
            <p class="text-gray-500 mb-12 text-center max-w-md">Did you have other jobs or businesses before your current one?</p>
            <div class="flex gap-10">
                <button type="button" onclick="finalize('YES')" class="group relative w-56 py-6 bg-yellow-500 rounded-3xl font-black text-2xl text-black hover:scale-110 transition-transform shadow-2xl shadow-yellow-900/40">
                    YES
                    <span class="absolute -bottom-8 left-0 w-full text-[10px] font-mono text-yellow-600 opacity-0 group-hover:opacity-100 transition-opacity">Redirect to History</span>
                </button>
                <button type="button" onclick="finalize('NO')" class="w-56 py-6 border-2 border-gray-800 rounded-3xl font-black text-2xl hover:bg-white hover:text-black transition-all">
                    NO
                </button>
            </div>
        </div>

    </form>

    <div class="fixed bottom-12 left-0 w-full px-12 flex justify-between items-center z-50">
        <button id="prevBtn" class="text-gray-500 hover:text-white transition-colors invisible font-bold tracking-widest text-xs">
            <i class="fas fa-arrow-left mr-2"></i> PREVIOUS
        </button>
        <button id="nextBtn" class="px-12 py-5 rounded-full bg-white text-black font-black text-xs tracking-widest hover:scale-105 transition-all shadow-xl">
            CONTINUE <i class="fas fa-chevron-right ml-2"></i>
        </button>
    </div>

    <script>
        const steps = document.querySelectorAll('.step-container');
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const progressFill = document.getElementById('progressFill');
        let current = 0;

        function updateUI() {
            steps.forEach((s, i) => s.classList.toggle('active', i === current));
            const percent = ((current + 1) / steps.length) * 100;
            progressFill.style.width = percent + "%";

            prevBtn.classList.toggle('invisible', current === 0);
            nextBtn.style.display = current === steps.length - 1 ? "none" : "block";
        }

        nextBtn.addEventListener('click', () => {
            if(current < steps.length - 1) {
                current++;
                updateUI();
            }
        });

        prevBtn.addEventListener('click', () => {
            if(current > 0) {
                current--;
                updateUI();
            }
        });

        function finalize(hasExperience) {
            // Adjust the URL based on your file names
            if(hasExperience === 'YES') {
                window.location.href = "previous_jobs.php";
            } else {
                window.location.href = "success_page.php";
            }
        }

        window.addEventListener('keydown', (e) => {
            if(e.key === "Enter" && current < steps.length - 1) {
                e.preventDefault();
                nextBtn.click();
            }
        });

        updateUI();
    </script>
</body>
</html>