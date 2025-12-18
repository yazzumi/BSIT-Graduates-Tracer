<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSIT Tracer | Survey Module</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { background-color: var(--color-1); color: #a3a3a3; font-family: 'Inter', sans-serif; overflow: hidden; }

        .tech-input { 
            background: #0a0a0a; border: 1px solid var(--color-7); color: white; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        .tech-input:focus { border-color: var(--accent); background: #0f0f0f; outline: none; box-shadow: 0 0 15px rgba(58, 130, 246, 0.2); }
        .tech-input.invalid { border-color: var(--error); }

        /* Enhanced Section Transitions - Minimized Delays */
        .survey-section { display: none; opacity: 0; transform: translateY(20px); }
        .survey-section.active { display: block; animation: sectionIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes sectionIn { 
            0% { opacity: 0; transform: translateY(15px) scale(0.98); filter: blur(2px); }
            100% { opacity: 1; transform: translateY(0) scale(1); filter: blur(0); } 
        }

        /* Enhanced Staggered Content Animation - Minimized Delays */
        .survey-section.active .animate-item { opacity: 0; animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes slideUp { 
            0% { transform: translateY(10px) scale(0.95); opacity: 0; }
            100% { transform: translateY(0) scale(1); opacity: 1; } 
        }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.1s; }
        .delay-3 { animation-delay: 0.15s; }

        /* Enhanced Progress Bar */
        .progress-segment { height: 3px; background: var(--color-7); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); border-radius: 10px; }
        .progress-segment.completed { 
            background: linear-gradient(90deg, var(--accent), #60a5fa); 
            box-shadow: 0 0 20px var(--accent-glow), inset 0 0 10px rgba(255,255,255,0.3);
            transform: scaleY(1.5);
        }

        /* Enhanced Radio Buttons */
        .tech-radio:checked + label { 
            border-color: var(--accent); 
            background: linear-gradient(135deg, rgba(58, 130, 246, 0.15), rgba(96, 165, 250, 0.1)); 
            color: white; font-weight: 800; 
            transform: scale(1.08) translateY(-2px);
            box-shadow: 0 10px 30px rgba(58, 130, 246, 0.3), inset 0 0 20px rgba(58, 130, 246, 0.1);
        }
        .radio-label { 
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .radio-label::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, rgba(58, 130, 246, 0.3), transparent);
            transition: all 0.6s ease;
            transform: translate(-50%, -50%);
            border-radius: 50%;
        }
        .tech-radio:checked + label::before {
            width: 100%;
            height: 100%;
        }

        /* Enhanced Input Focus - Better Contrast */
        .tech-input:focus { 
            border-color: var(--accent); 
            background: linear-gradient(135deg, #0f0f0f, rgba(58, 130, 246, 0.08)); 
            outline: none; 
            box-shadow: 0 0 25px rgba(58, 130, 246, 0.4), inset 0 0 15px rgba(58, 130, 246, 0.08);
            transform: translateY(-2px);
        }
        .tech-input::placeholder { color: #666; }
        .tech-input:focus::placeholder { color: #888; }
        .tech-input.invalid { 
            border-color: var(--error); 
            background: rgba(239, 68, 68, 0.08);
            animation: inputShake 0.5s ease-in-out;
        }

        /* Enhanced Shake Animation */
        .animate-shake { animation: shake 0.6s cubic-bezier(0.36, 0.07, 0.19, 0.97) }
        @keyframes shake { 
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
            20%, 40%, 60%, 80% { transform: translateX(10px); }
        }
        @keyframes inputShake {
            0%, 100% { transform: translateX(0) scale(1); }
            25% { transform: translateX(-8px) scale(0.98); }
            75% { transform: translateX(8px) scale(0.98); }
        }

        /* Floating Particles Background */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--accent);
            border-radius: 50%;
            opacity: 0.3;
            animation: float 15s infinite ease-in-out;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); opacity: 0.3; }
            25% { transform: translateY(-100px) translateX(50px); opacity: 0.6; }
            50% { transform: translateY(-50px) translateX(-30px); opacity: 0.4; }
            75% { transform: translateY(-150px) translateX(-50px); opacity: 0.7; }
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            body {
                overflow-y: auto;
            }
            
            nav {
                padding: 1rem !important;
            }
            
            main {
                padding: 1rem !important;
            }
            
            h2 {
                font-size: 2rem !important;
            }
            
            .tech-input {
                padding: 0.75rem !important;
                font-size: 0.9rem;
            }
            
            .radio-label {
                padding: 0.75rem !important;
                font-size: 0.85rem;
            }
            
            #nextBtn, #prevBtn {
                padding: 0.75rem 1.5rem !important;
            }
            
            footer {
                padding: 1rem !important;
            }
            
            .grid-cols-3 {
                grid-template-columns: 1fr !important;
            }
            
            .grid-cols-2 {
                grid-template-columns: 1fr !important;
            }
            
            .grid-cols-4 {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        @media (max-width: 480px) {
            h2 {
                font-size: 1.5rem !important;
            }
            
            .tech-input {
                padding: 0.6rem !important;
                font-size: 0.8rem;
            }
            
            .radio-label {
                padding: 0.5rem !important;
                font-size: 0.75rem;
            }
            
            #nextBtn {
                padding: 0.6rem 1rem !important;
                font-size: 9px !important;
            }
            
            .grid-cols-4 {
                grid-template-columns: 1fr !important;
            }
            
            .p-12 {
                padding: 1.5rem !important;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-black">

    <!-- Floating Particles Background -->
    <div class="particles" id="particles"></div>

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
        <form id="surveyForm" class="w-full max-w-5xl" novalidate>
            
            <div class="survey-section active" id="section1">
                <div class="mb-10">
                    <span class="text-accent font-mono text-[10px] tracking-[0.4em] uppercase">Module // 01</span>
                    <h2 class="text-5xl text-white font-black tracking-tighter mt-1">Personal Identity</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="animate-item delay-1 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">First Name</label>
                        <input type="text" name="first_name" required placeholder="e.g., Juan" class="tech-input w-full p-5 rounded-xl">
                    </div>
                    <div class="animate-item delay-2 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Middle Name</label>
                        <input type="text" name="middle_name" placeholder="e.g., Santos (Optional)" class="tech-input w-full p-5 rounded-xl">
                    </div>
                    <div class="animate-item delay-3 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Last Name</label>
                        <input type="text" name="last_name" required placeholder="e.g., Cruz" class="tech-input w-full p-5 rounded-xl">
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
                        <select name="gender" required class="tech-input w-full p-5 rounded-xl">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="animate-item delay-2 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Date of Birth</label>
                        <input type="date" name="date_of_birth" required placeholder="Select your birthdate" class="tech-input w-full p-5 rounded-xl">
                    </div>
                    <div class="animate-item delay-3 md:col-span-2 space-y-4">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Civil Status</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <input type="radio" name="civil_status" id="single" value="Single" checked class="hidden tech-radio">
                            <label for="single" class="radio-label border border-color-7 p-4 text-center rounded-xl cursor-pointer">Single</label>
                            <input type="radio" name="civil_status" id="married" value="Married" class="hidden tech-radio">
                            <label for="married" class="radio-label border border-color-7 p-4 text-center rounded-xl cursor-pointer">Married</label>
                            <input type="radio" name="civil_status" id="widowed" value="Widowed" class="hidden tech-radio">
                            <label for="widowed" class="radio-label border border-color-7 p-4 text-center rounded-xl cursor-pointer">Widowed</label>
                            <input type="radio" name="civil_status" id="separated" value="Separated" class="hidden tech-radio">
                            <label for="separated" class="radio-label border border-color-7 p-4 text-center rounded-xl cursor-pointer">Separated</label>
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
                        <input type="text" name="permanent_address" required placeholder="e.g., Block 5 Lot 12, Sunshine Village" class="tech-input w-full p-5 rounded-xl">
                    </div>
                    <div class="animate-item delay-2 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Barangay</label>
                        <input type="text" name="barangay" required placeholder="e.g., San Vicente" class="tech-input w-full p-5 rounded-xl">
                    </div>
                    <div class="animate-item delay-2 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">City / Municipality</label>
                        <input type="text" name="city_municipality" required placeholder="e.g., Ilagan City" class="tech-input w-full p-5 rounded-xl">
                    </div>
                    <div class="animate-item delay-3 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Province</label>
                        <input type="text" name="province" required placeholder="e.g., Isabela" class="tech-input w-full p-5 rounded-xl">
                    </div>
                    <div class="animate-item delay-3 space-y-2">
                        <label class="text-[10px] uppercase font-bold text-gray-600">Contact Number</label>
                        <input type="tel" name="contact_number" required placeholder="09123456789" class="tech-input w-full p-5 rounded-xl">
                    </div>
                </div>
            </div>

            <div class="survey-section" id="section4">
                <div class="mb-10 text-center animate-item">
                    <span class="text-accent font-mono text-[10px] tracking-[0.4em] uppercase">Module // Final</span>
                    <h2 class="text-5xl text-white font-black tracking-tighter mt-1">Employment Track</h2>
                </div>
                <div class="animate-item delay-1 bg-[#050505] border border-gray-900 p-12 rounded-3xl max-w-2xl mx-auto shadow-2xl">
                    <h3 class="text-xl text-center text-white mb-8 font-light">Were you employed within <span class="text-accent font-bold">6 months</span> after graduation?</h3>
                    <div class="flex flex-col gap-5">
                        <input type="radio" name="employed_within_6_months" id="emp_yes" value="1" checked class="hidden tech-radio">
                        <label for="emp_yes" onclick="celebrate()" class="radio-label border border-color-7 p-6 rounded-2xl flex items-center justify-between cursor-pointer group">
                            <span class="text-lg">Yes, I was employed immediately.</span>
                            <i class="fas fa-crown text-accent opacity-0 group-hover:opacity-100 transition-all"></i>
                        </label>
                        <input type="radio" name="employed_within_6_months" id="emp_no" value="0" class="hidden tech-radio">
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

    <footer class="p-8 flex justify-between text-[9px] font-mono text-gray-800 uppercase tracking-[0.2em]">
        <div id="step-counter">STEP 01 // 04</div>
        <div class="flex items-center gap-2">
            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
            SYSTEM_READY
        </div>
    </footer>

    <script>
        const sections = document.querySelectorAll('.survey-section');
        const progressContainer = document.getElementById('progress-container');
        let currentSection = 0;

        sections.forEach((_, i) => {
            const seg = document.createElement('div');
            seg.className = `progress-segment w-12 ${i === 0 ? 'completed' : ''}`;
            progressContainer.appendChild(seg);
        });
        const segments = document.querySelectorAll('.progress-segment');

        function celebrate() { confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 }, colors: ['#3a82f6', '#ffffff'] }); }

        function validate() {
            const fields = sections[currentSection].querySelectorAll('input[required], select[required]');
            let valid = true;
            fields.forEach(f => {
                if(!f.value.trim()) {
                    valid = false;
                    f.classList.add('invalid', 'animate-shake');
                    setTimeout(() => f.classList.remove('animate-shake'), 400);
                } else { f.classList.remove('invalid'); }
            });
            return valid;
        }

        function save() {
            const formData = new FormData(document.getElementById('surveyForm'));
            const data = Object.fromEntries(formData.entries());
            
            // Auto-compute Age before saving
            if(data.date_of_birth) {
                const dob = new Date(data.date_of_birth);
                const diff = Date.now() - dob.getTime();
                data.age = Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25));
            }
            
            localStorage.setItem('tracer_payload', JSON.stringify(data));
        }

        document.getElementById('nextBtn').addEventListener('click', function() {
            if(!validate()) return;
            save();
            if(currentSection < sections.length - 1) {
                currentSection++;
                update();
            } else {
                this.innerHTML = 'SYNCING... <i class="fas fa-spinner fa-spin"></i>';
                setTimeout(() => window.location.href = 'status_selection.php', 1000);
            }
        });

        document.getElementById('prevBtn').addEventListener('click', () => {
            if(currentSection > 0) { currentSection--; update(); }
        });

        function update() {
            sections.forEach((s, i) => {
                s.classList.toggle('active', i === currentSection);
                segments[i].classList.toggle('completed', i <= currentSection);
            });
            document.getElementById('prevBtn').style.opacity = currentSection === 0 ? '0' : '1';
            document.getElementById('prevBtn').style.pointerEvents = currentSection === 0 ? 'none' : 'auto';
            document.getElementById('step-counter').innerText = `STEP 0${currentSection + 1} // 04`;
            document.getElementById('nextBtn').innerHTML = currentSection === sections.length - 1 ? 'FINISH <i class="fas fa-check ml-2"></i>' : 'CONTINUE <i class="fas fa-chevron-right ml-2"></i>';
        }

        // Initialize floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            for (let i = 0; i < 20; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (15 + Math.random() * 10) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        // Initialize particles on load
        createParticles();
    </script>

    <?php include 'includes/theme_toggle.php'; ?>
</body>
</html>