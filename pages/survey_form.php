<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSIT Tracer | Survey Module</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    
    <style>

        body {
            background-color: var(--color-1);
            color: #a3a3a3;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* High-Performance Inputs */
        .tech-input {
            background: #0a0a0a;
            border: 1px solid var(--color-7);
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .tech-input:focus {
            border-color: var(--accent);
            background: #0f0f0f;
            box-shadow: 0 0 15px rgba(58, 130, 246, 0.2);
            outline: none;
            transform: translateY(-1px);
        }

        /* Section Transitions */
        .survey-section {
            display: none;
            opacity: 0;
            transform: translateY(20px);
        }

        .survey-section.active {
            display: block;
            animation: sectionIn 0.5s ease-out forwards;
        }

        @keyframes sectionIn {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Staggered Content Animation */
        .survey-section.active .animate-item {
            animation: slideUp 0.5s ease-out forwards;
            opacity: 0;
        }
        
        @keyframes slideUp {
            from { transform: translateY(15px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }

        /* Progress Bar */
        .progress-segment {
            height: 3px;
            transition: all 0.5s ease;
            background: var(--color-7);
        }
        .progress-segment.completed { 
            background: var(--accent); 
            box-shadow: 0 0 10px var(--accent); 
        }

        /* Radio Scale 105 & Bold */
        .tech-radio:checked + label {
            border-color: var(--accent);
            background: rgba(58, 130, 246, 0.1);
            color: white;
            font-weight: 800;
            transform: scale(1.05);
        }

        .radio-label {
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.2);
            background: #0a0a0a;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-black">

    <nav class="w-full p-8 flex justify-between items-center z-20">
        <div class="flex items-center gap-6">
            <div class="w-10 h-10 border border-accent rounded flex items-center justify-center text-white font-black text-sm">IT</div>
            <div>
                <h1 class="text-white text-[10px] font-black tracking-[0.4em] uppercase">Graduate_Tracer_v2</h1>
                <div class="flex gap-2 mt-2" id="progress-container"></div>
            </div>
        </div>
    </nav>

    <main class="flex-1 flex flex-col items-center justify-center px-8">
        <form id="surveyForm" class="w-full max-w-5xl">
            
            <div class="survey-section active" id="section1">
                <div class="mb-10">
                    <span class="text-accent font-mono text-[10px] tracking-[0.4em] uppercase">Module // 01</span>
                    <h2 class="text-5xl text-white font-black tracking-tighter mt-1">Personal Identity</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="animate-item delay-1 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">First Name</label>
                        <input type="text" name="fname" placeholder="Enter first name" class="tech-input w-full p-5 rounded-xl">
                    </div>
                    <div class="animate-item delay-2 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Middle Name</label>
                        <input type="text" name="mname" placeholder="Optional" class="tech-input w-full p-5 rounded-xl">
                    </div>
                    <div class="animate-item delay-3 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Last Name</label>
                        <input type="text" name="lname" placeholder="Enter last name" class="tech-input w-full p-5 rounded-xl">
                    </div>
                </div>
            </div>

            <div class="survey-section" id="section2">
                <div class="mb-10">
                    <span class="text-accent font-mono text-[10px] tracking-[0.4em] uppercase">Module // 02</span>
                    <h2 class="text-5xl text-white font-black tracking-tighter mt-1">Demographics</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="animate-item delay-1 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Gender</label>
                        <select name="gender" class="tech-input w-full p-5 rounded-xl appearance-none">
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </div>
                    <div class="animate-item delay-2 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Date of Birth</label>
                        <input type="date" name="dob" class="tech-input w-full p-5 rounded-xl">
                    </div>
                    <div class="animate-item delay-3 md:col-span-2 space-y-4">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Civil Status</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <input type="radio" name="civil" id="single" value="Single" class="hidden tech-radio"><label for="single" class="radio-label border border-color-7 p-4 text-center rounded-xl cursor-pointer">Single</label>
                            <input type="radio" name="civil" id="married" value="Married" class="hidden tech-radio"><label for="married" class="radio-label border border-color-7 p-4 text-center rounded-xl cursor-pointer">Married</label>
                            <input type="radio" name="civil" id="widowed" value="Widowed" class="hidden tech-radio"><label for="widowed" class="radio-label border border-color-7 p-4 text-center rounded-xl cursor-pointer">Widowed</label>
                            <input type="radio" name="civil" id="separated" value="Separated" class="hidden tech-radio"><label for="separated" class="radio-label border border-color-7 p-4 text-center rounded-xl cursor-pointer">Separated</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="survey-section" id="section3">
                <div class="mb-10">
                    <span class="text-accent font-mono text-[10px] tracking-[0.4em] uppercase">Module // 03</span>
                    <h2 class="text-5xl text-white font-black tracking-tighter mt-1">Location & Contact</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="animate-item delay-1 md:col-span-2 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Permanent Address</label>
                        <input type="text" name="address" placeholder="House No. / Street / City" class="tech-input w-full p-5 rounded-xl">
                    </div>
                    <div class="animate-item delay-2 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Email Address</label>
                        <input type="email" name="email" placeholder="name@email.com" class="tech-input w-full p-5 rounded-xl">
                    </div>
                    <div class="animate-item delay-3 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Contact Number</label>
                        <input type="tel" name="phone" placeholder="+63 9xx" class="tech-input w-full p-5 rounded-xl">
                    </div>
                </div>
            </div>

            <div class="survey-section" id="section4">
                <div class="mb-10 text-center">
                    <span class="text-accent font-mono text-[10px] tracking-[0.4em] uppercase">Module // Final</span>
                    <h2 class="text-5xl text-white font-black tracking-tighter mt-1">Employment Track</h2>
                </div>
                
                <div class="bg-[#050505] border border-gray-900 p-8 md:p-12 rounded-3xl max-w-2xl mx-auto shadow-2xl">
                    <h3 class="text-xl text-center text-white mb-8 font-light">Were you employed within <span class="text-accent font-bold">6 months</span> after graduation?</h3>
                    <div class="flex flex-col gap-5">
                        <input type="radio" name="employed" id="emp_yes" value="YES" class="hidden tech-radio">
                        <label for="emp_yes" onclick="celebrate()" class="radio-label border border-color-7 p-6 rounded-2xl flex items-center justify-between cursor-pointer group">
                            <span class="text-lg">Yes, I was employed immediately.</span>
                            <i class="fas fa-crown text-accent opacity-0 group-hover:opacity-100 transition-all"></i>
                        </label>

                        <input type="radio" name="employed" id="emp_no" value="NO" class="hidden tech-radio">
                        <label for="emp_no" class="radio-label border border-color-7 p-6 rounded-2xl flex items-center justify-between cursor-pointer group">
                            <span class="text-lg">No, it took longer than 6 months.</span>
                            <i class="fas fa-clock text-gray-600 opacity-0 group-hover:opacity-100 transition-all"></i>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-16 flex justify-between items-center max-w-5xl mx-auto">
                <button type="button" id="prevBtn" class="opacity-0 pointer-events-none text-gray-600 hover:text-white font-bold tracking-widest text-[10px] transition-all">
                    <i class="fas fa-arrow-left mr-2"></i> PREVIOUS
                </button>
                <button type="button" id="nextBtn" class="bg-white text-black font-black px-12 py-5 rounded-full hover:scale-105 active:scale-95 transition-all text-[10px] tracking-widest shadow-xl">
                    CONTINUE <i class="fas fa-chevron-right ml-2"></i>
                </button>
            </div>
        </form>
    </main>

    <footer class="p-8 flex justify-between text-[9px] font-mono text-gray-800">
        <div id="step-counter">STEP 01 // 04</div>
        <div class="flex items-center gap-2">
            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
            SYSTEM_READY
        </div>
    </footer>

    <script>
        const sections = document.querySelectorAll('.survey-section');
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const stepCounter = document.getElementById('step-counter');
        const progressContainer = document.getElementById('progress-container');
        let currentSection = 0;

        // Progress Bar Creation
        sections.forEach((_, i) => {
            const seg = document.createElement('div');
            seg.className = `progress-segment w-12 ${i === 0 ? 'completed' : ''}`;
            progressContainer.appendChild(seg);
        });
        const segments = document.querySelectorAll('.progress-segment');

        // Confetti Function
        function celebrate() {
            confetti({
                particleCount: 150,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#3a82f6', '#ffffff', '#60a5fa']
            });
        }

        nextBtn.addEventListener('click', () => {
            if (currentSection < sections.length - 1) {
                currentSection++;
                updateSurvey();
            } else {
                nextBtn.disabled = true;
                nextBtn.innerHTML = 'SYNCING... <i class="fas fa-spinner fa-spin ml-2"></i>';
                setTimeout(() => { window.location.href = 'status_selection.php'; }, 1200);
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentSection > 0) {
                currentSection--;
                updateSurvey();
            }
        });

        function updateSurvey() {
            sections.forEach((sec, i) => {
                sec.classList.toggle('active', i === currentSection);
                segments[i].classList.toggle('completed', i <= currentSection);
            });
            prevBtn.style.opacity = currentSection === 0 ? '0' : '1';
            prevBtn.style.pointerEvents = currentSection === 0 ? 'none' : 'auto';
            nextBtn.innerHTML = currentSection === sections.length - 1 ? 'FINISH <i class="fas fa-check ml-2"></i>' : 'CONTINUE <i class="fas fa-chevron-right ml-2"></i>';
            stepCounter.innerText = `STEP 0${currentSection + 1} // 04`;
        }
    </script>
</body>
</html>