<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSIT Tracer | Identity Validation</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: var(--color-1);
            color: #808080;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* High-End Input Styling */
        .glass-input {
            background: #0a0a0a;
            border: 1px solid var(--color-7);
            color: white;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-input:focus {
            border-color: var(--accent);
            background: #0f0f0f;
            box-shadow: 0 0 20px var(--accent-glow);
            outline: none;
            transform: scale(1.02);
        }

        /* Step Animations */
        .step-content {
            display: none;
            opacity: 0;
            transform: translateX(20px);
        }

        .step-content.active {
            display: block;
            animation: slideIn 0.5s ease-out forwards;
        }

        @keyframes slideIn {
            to { opacity: 1; transform: translateX(0); }
        }

        /* Cyber Security Scan Line */
        .scan-line {
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            position: absolute;
            top: 0; left: 0;
            box-shadow: 0 0 10px var(--accent);
            animation: scanning 4s linear infinite;
            opacity: 0.5;
        }

        @keyframes scanning {
            0% { top: 0%; }
            100% { top: 100%; }
        }

        /* Year Button Selection */
        .year-btn {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .year-btn.selected {
            border-color: var(--accent);
            background: rgba(58, 130, 246, 0.1);
            color: white;
            font-weight: 800;
            transform: scale(1.05);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row bg-black">

    <div class="w-full md:w-1/3 bg-[#050505] border-r border-gray-900 p-12 flex flex-col justify-between relative overflow-hidden">
        <div class="scan-line"></div>
        
        <div class="z-10">
            <h2 class="text-accent font-mono text-[10px] tracking-[0.5em] uppercase mb-4">Security Module</h2>
            <h1 class="text-white text-4xl font-black leading-tight tracking-tighter">Identity<br>Verification</h1>
            <p class="mt-6 text-sm leading-relaxed text-gray-500 font-light">
                Maintaining data integrity. Please provide your university credentials to unlock the tracer survey.
            </p>
        </div>

        <div class="space-y-8 z-10 border-l border-gray-800 pl-8 py-4">
            <div>
                <span class="block text-[9px] uppercase tracking-[0.3em] text-gray-600 mb-1">Current Task</span>
                <span id="status-text" class="text-white font-mono text-xs uppercase tracking-widest">Waiting for Campus Selection</span>
            </div>
            <div>
                <span class="block text-[9px] uppercase tracking-[0.3em] text-gray-600 mb-1">Encrypted Link</span>
                <span class="text-accent font-mono text-xs uppercase">AES_256_ACTIVE</span>
            </div>
        </div>

        <div class="text-[9px] text-gray-800 font-mono tracking-widest uppercase">
            System.Auth // Session.2025
        </div>
    </div>

    <div class="flex-1 p-8 md:p-20 flex flex-col justify-center items-center">
        <form id="multiStepForm" action="survey_form.php" method="POST" class="w-full max-w-xl" onsubmit="return false;">
            
            <div class="step-content active" id="step1">
                <div class="mb-12">
                    <span class="text-accent font-mono text-xs">01 // SOURCE</span>
                    <h3 class="text-4xl text-white font-black tracking-tighter mt-2">Where did you graduate?</h3>
                </div>
                
                <div class="space-y-6">
                    <div class="relative">
                        <label class="block text-[10px] uppercase font-bold text-gray-600 tracking-[0.2em] mb-3">Campus Location</label>
                        <select id="schoolInput" name="campus" class="glass-input w-full p-6 rounded-2xl text-lg appearance-none cursor-pointer">
                            <option value="">Select University Campus...</option>
                            <option value="Echague">ISU - Echague Campus</option>
                            <option value="Ilagan">ISU - Ilagan Campus</option>
                            <option value="Cauayan">ISU - Cauayan Campus</option>
                        </select>
                        <div class="absolute right-6 bottom-6 pointer-events-none text-gray-600">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="step-content" id="step2">
                <div class="mb-12">
                    <span class="text-accent font-mono text-xs">02 // CREDENTIALS</span>
                    <h3 class="text-4xl text-white font-black tracking-tighter mt-2">Student Identification</h3>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-gray-600 tracking-[0.2em] mb-3">Student ID Number</label>
                        <input type="text" id="idInput" name="student_id" placeholder="21-xxxxx" maxlength="8"
                               class="glass-input w-full p-6 rounded-2xl text-xl font-mono tracking-widest uppercase">
                        <p class="text-[10px] mt-4 text-gray-700 font-mono italic">PATTERN REQUIRED: YY-XXXXX</p>
                    </div>
                </div>
            </div>

            <div class="step-content" id="step3">
                <div class="mb-12">
                    <span class="text-accent font-mono text-xs">03 // TIMELINE</span>
                    <h3 class="text-4xl text-white font-black tracking-tighter mt-2">Final Confirmation</h3>
                </div>
                
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-600 tracking-[0.2em] mb-4">Graduation Year</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <button type="button" class="year-btn glass-input p-5 rounded-xl hover:border-accent">2024</button>
                        <button type="button" class="year-btn glass-input p-5 rounded-xl hover:border-accent">2023</button>
                        <button type="button" class="year-btn glass-input p-5 rounded-xl hover:border-accent">2022</button>
                        <button type="button" class="year-btn glass-input p-5 rounded-xl hover:border-accent">2021</button>
                        <button type="button" class="year-btn glass-input p-5 rounded-xl hover:border-accent">2020</button>
                        <button type="button" class="year-btn glass-input p-5 rounded-xl hover:border-accent">Earlier</button>
                    </div>
                    <input type="hidden" name="grad_year" id="gradYearHidden">
                </div>
            </div>

            <div class="mt-20 flex items-center justify-between">
                <button type="button" id="prevBtn" class="invisible text-gray-600 hover:text-white transition-all font-black text-[10px] tracking-widest uppercase">
                    <i class="fas fa-arrow-left mr-2"></i> Previous
                </button>
                
                <button type="button" id="nextBtn" disabled class="bg-white text-black px-12 py-5 rounded-full font-black text-[10px] tracking-[0.2em] uppercase transition-all shadow-xl hover:scale-105 disabled:opacity-30 disabled:hover:scale-100">
                    <span id="btnText">Continue</span>
                    <i class="fas fa-chevron-right ml-3"></i>
                </button>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById('multiStepForm');
        const steps = document.querySelectorAll('.step-content');
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const btnText = document.getElementById('btnText');
        const statusText = document.getElementById('status-text');
        const idInput = document.getElementById('idInput');
        const schoolInput = document.getElementById('schoolInput');
        const gradYearHidden = document.getElementById('gradYearHidden');
        
        let currentStep = 0;

        // --- BUG FIX: INTERCEPT ENTER KEY ---
        form.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault(); 
                if (!nextBtn.disabled) {
                    nextBtn.click();
                }
            }
        });

        // --- UPDATED: AUTO-DASH FORMATTING (YY-XXXXX) ---
        idInput.addEventListener('input', (e) => {
            let val = e.target.value.replace(/\D/g, ''); // Digits only
            if (val.length > 2) {
                // Inserts dash after the first 2 digits (Year)
                val = val.substring(0, 2) + '-' + val.substring(2, 7);
            }
            e.target.value = val;
            validate();
        });

        const validate = () => {
            let isValid = false;
            
            if (currentStep === 0) {
                isValid = schoolInput.value !== "";
            }
            
            if (currentStep === 1) {
                // Regex for 2 digits, a dash, and 5 digits (Total 8 chars)
                const idRegex = /^\d{2}-\d{5}$/;
                isValid = idRegex.test(idInput.value);
            }
            
            if (currentStep === 2) {
                isValid = gradYearHidden.value !== "";
            }
            
            nextBtn.disabled = !isValid;
        };

        schoolInput.addEventListener('change', validate);

        nextBtn.addEventListener('click', () => {
            if (currentStep < steps.length - 1) {
                currentStep++;
                updateUI();
                validate();
            } else {
                // Final verify and real submit
                btnText.innerText = "VERIFYING NODE...";
                statusText.innerText = "AUTHENTICATING...";
                statusText.style.color = "#3a82f6";
                nextBtn.disabled = true;
                
                setTimeout(() => {
                    form.onsubmit = null; // Clear the return false
                    form.submit();
                }, 1500);
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                updateUI();
                validate();
            }
        });

        function updateUI() {
            steps.forEach((step, idx) => step.classList.toggle('active', idx === currentStep));
            prevBtn.classList.toggle('invisible', currentStep === 0);
            btnText.innerText = currentStep === steps.length - 1 ? "FINALIZE SYNC" : "CONTINUE";
            
            const messages = [
                "Locating Campus Node...",
                "Awaiting Credential Input...",
                "Ready for Validation..."
            ];
            statusText.innerText = messages[currentStep];
        }

        // Year Button Logic
        document.querySelectorAll('.year-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.year-btn').forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
                gradYearHidden.value = this.innerText;
                validate();
            });
        });
    </script>
</body>
</html>