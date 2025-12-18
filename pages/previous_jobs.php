<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previous Experience | BSIT Tracer</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap');

        body { background-color: #000; color: #fff; font-family: 'Inter', sans-serif; overflow: hidden; }

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

        /* --- CUSTOM SCROLLBAR START --- */
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 10px;
            transition: background 0.3s ease;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #3a82f6;
        }
        /* --- CUSTOM SCROLLBAR END --- */

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
        .input-focus:focus { outline: none; border-color: #3a82f6; color: #3a82f6; font-weight: 600; }

        .choice-card {
            background: #0a0a0a;
            border: 1px solid #1a1a1a;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            text-align: center; padding: 1.5rem; border-radius: 16px;
            font-size: 0.85rem; color: #666; height: 100%;
        }

        .hidden-radio:checked + .choice-card {
            border-color: #3a82f6; background: rgba(58, 130, 246, 0.15); color: #fff;
            transform: scale(1.05); font-weight: 800;
            box-shadow: 0 15px 40px rgba(58, 130, 246, 0.25);
        }

        .date-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 4rem; width: 100%; max-width: 800px; }
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); }

        .progress-bar { height: 4px; background: #1a1a1a; position: fixed; top: 0; left: 0; z-index: 100; width: 100%; }
        .progress-fill { height: 100%; background: #3a82f6; box-shadow: 0 0 15px #3a82f6; width: 16.6%; transition: width 0.6s ease; }

        /* Responsive Styles */
        @media (max-width: 768px) {
            body {
                overflow-y: auto;
            }
            
            .step-container.active {
                height: auto;
                min-height: 85vh;
                padding: 2rem 0;
            }
            
            .input-focus {
                font-size: 1.5rem;
                padding: 0.75rem;
            }
            
            h1 {
                font-size: 1.75rem !important;
                margin-bottom: 1.5rem !important;
            }
            
            .choice-card {
                padding: 1rem;
                font-size: 0.75rem;
            }
            
            .date-grid {
                grid-template-columns: 1fr !important;
                gap: 2rem !important;
            }
            
            .fixed.top-12 {
                top: 0.5rem;
            }
            
            .fixed.top-12 span {
                font-size: 8px;
                padding: 0.5rem 1rem;
            }
            
            #nextBtn, #prevBtn {
                padding: 0.75rem 1.5rem;
                font-size: 10px;
            }
            
            .fixed.bottom-12 {
                bottom: 1rem;
                padding: 0 1rem;
            }
            
            .mt-16 {
                margin-top: 2rem !important;
            }
            
            .mt-16 button {
                padding: 0.75rem 1.5rem;
                font-size: 10px;
            }
        }

        @media (max-width: 480px) {
            .input-focus {
                font-size: 1.2rem;
            }
            
            h1 {
                font-size: 1.5rem !important;
            }
            
            .choice-card {
                padding: 0.75rem;
                font-size: 0.65rem;
            }
            
            .grid-cols-2 {
                grid-template-columns: 1fr !important;
            }
            
            .mt-16 {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .mt-16 button {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>

    <div class="fixed top-12 left-0 w-full text-center z-50">
        <span class="px-6 py-2 border border-blue-500/30 bg-blue-500/10 rounded-full text-blue-400 font-mono text-[10px] uppercase tracking-[0.4em]">
            Answering: Work Experiences (Historical)
        </span>
    </div>

    <form id="previousExpForm" class="px-6 h-screen flex items-center">
        
        <div class="step-container active" id="step-0">
            <span class="text-blue-500 font-mono text-xs tracking-[0.5em] mb-4">01 // ROLE</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center">Job Description</h1>
            <input type="text" name="job_desc" placeholder="e.g. Lead System Architect" class="input-focus" autocomplete="off">
        </div>

        <div class="step-container" id="step-1">
            <span class="text-blue-500 font-mono text-xs tracking-[0.5em] mb-4">02 // ENTITY</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center">Company Name</h1>
            <input type="text" name="prev_company" placeholder="Enter company name..." class="input-focus" autocomplete="off">
        </div>

        <div class="step-container" id="step-2">
            <span class="text-blue-500 font-mono text-xs tracking-[0.5em] mb-4">03 // LOCATION</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center">Company Address</h1>
            <input type="text" name="prev_address" placeholder="Full Address..." class="input-focus !text-2xl" autocomplete="off">
        </div>

        <div class="step-container" id="step-3a">
            <span class="text-blue-500 font-mono text-xs tracking-[0.5em] mb-4">04 // INDUSTRY</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center">Type of Company</h1>
            <input type="text" name="type_of_company" placeholder="e.g., IT Services, Manufacturing..." class="input-focus !text-2xl" autocomplete="off">
        </div>

        <div class="step-container" id="step-3b">
            <span class="text-blue-500 font-mono text-xs tracking-[0.5em] mb-4">05 // BUSINESS</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center">Nature of Business</h1>
            <input type="text" name="nature_of_business" placeholder="e.g., Software Development, Consulting..." class="input-focus !text-2xl" autocomplete="off">
        </div>

        <div class="step-container" id="step-4">
            <span class="text-blue-500 font-mono text-xs tracking-[0.5em] mb-4">06 // DESIGNATION</span>
            <h1 class="text-3xl md:text-4xl font-extrabold mb-8 text-center">Professional Position</h1>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-5 w-full custom-scroll overflow-y-auto max-h-[50vh] p-4 pr-6">
                <?php
                $positions = [
                    "IT Academician / Teacher", "Programmer / Developer", "Network Administrator",
                    "IT Support Specialist / Virtual Assistant", "IT Engineer", "IT Analyst",
                    "IT Quality Control", "IT Researcher", "IT Technician",
                    "IT Consultant", "Computer Forensic / Investigator"
                ];
                foreach($positions as $pos) {
                    echo "
                    <label class='block h-24'>
                        <input type='radio' name='position' value='$pos' class='hidden hidden-radio pos-radio'>
                        <div class='choice-card'>$pos</div>
                    </label>";
                }
                ?>
                <div class="col-span-full mt-4">
                    <input type="text" id="other_pos_input" name="other_position" placeholder="Specify Other Position..." class="bg-[#0a0a0a] border border-[#1a1a1a] w-full p-5 rounded-2xl text-center focus:border-blue-500 outline-none text-xl transition-all">
                </div>
            </div>
        </div>

        <div class="step-container" id="step-5">
            <span class="text-blue-500 font-mono text-xs tracking-[0.5em] mb-4">07 // TIMELINE</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center">Inclusive Years</h1>
            <div class="date-grid">
                <div class="flex flex-col">
                    <label class="text-xs text-blue-500 mb-4 uppercase tracking-[0.3em] font-bold text-center">From</label>
                    <input type="date" name="prev_from" class="bg-transparent border-b-2 border-gray-800 p-4 focus:border-blue-500 outline-none text-white text-center text-xl">
                </div>
                <div class="flex flex-col">
                    <label class="text-xs text-blue-500 mb-4 uppercase tracking-[0.3em] font-bold text-center">To</label>
                    <input type="date" name="prev_to" class="bg-transparent border-b-2 border-gray-800 p-4 focus:border-blue-500 outline-none text-white text-center text-xl">
                </div>
            </div>
        </div>

        <div class="step-container" id="step-6">
            <span class="text-blue-500 font-mono text-xs tracking-[0.5em] mb-4">08 // STATUS</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center">Employment Status</h1>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 w-full px-4">
                <?php
                $status_list = ["Permanent", "Temporary", "Casual", "Contractual", "Job Order"];
                foreach($status_list as $status) {
                    echo "
                    <label class='h-40'>
                        <input type='radio' name='emp_status' value='$status' class='hidden hidden-radio'>
                        <div class='choice-card text-xl'>$status</div>
                    </label>";
                }
                ?>
            </div>
            <div class="mt-16 flex gap-6">
                <button type="button" onclick="saveAndAddMore()" class="px-8 py-4 border border-gray-700 rounded-full text-xs font-bold tracking-widest hover:bg-white hover:text-black transition-all">
                    + SAVE & ADD ANOTHER
                </button>
                <button type="button" onclick="finishAndSubmit()" class="px-12 py-4 bg-blue-600 rounded-full text-xs font-bold tracking-widest hover:scale-105 transition-all shadow-xl shadow-blue-500/20">
                    FINISH SUBMISSION
                </button>
            </div>
        </div>

    </form>

    <div class="fixed bottom-12 left-0 w-full px-12 flex justify-between items-center z-50">
        <button id="prevBtn" class="text-gray-500 hover:text-white transition-colors opacity-0 pointer-events-none font-bold tracking-widest text-xs"><i class="fas fa-arrow-left mr-2"></i> PREVIOUS</button>
        <button id="nextBtn" class="px-12 py-5 rounded-full bg-white text-black font-black text-xs tracking-widest hover:scale-105 transition-all">CONTINUE <i class="fas fa-chevron-right ml-2"></i></button>
    </div>

    <script>
        const steps = document.querySelectorAll('.step-container');
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const progressFill = document.getElementById('progressFill');
        const otherPosInput = document.getElementById('other_pos_input');
        const posRadios = document.querySelectorAll('.pos-radio');
        let current = 0;

        function updateUI() {
            steps.forEach((s, i) => s.classList.toggle('active', i === current));
            progressFill.style.width = ((current + 1) / steps.length) * 100 + "%";
            prevBtn.style.opacity = current === 0 ? "0" : "1";
            prevBtn.style.pointerEvents = current === 0 ? "none" : "auto";
            nextBtn.style.display = current === steps.length - 1 ? "none" : "block";
        }

        // Improved Other Position Logic
        otherPosInput.addEventListener('input', () => { 
            if (otherPosInput.value.trim() !== "") {
                // Manually uncheck all radio buttons to remove visual "active" state
                posRadios.forEach(r => r.checked = false); 
            }
        });

        posRadios.forEach(r => {
            r.addEventListener('change', () => { 
                if (r.checked) {
                    otherPosInput.value = ""; 
                }
            });
        });

        nextBtn.addEventListener('click', () => { if(current < steps.length - 1) { current++; updateUI(); } });
        prevBtn.addEventListener('click', () => { if(current > 0) { current--; updateUI(); } });

        let previousExperiences = [];

        function saveAndAddMore() {
            const formData = new FormData(document.getElementById('previousExpForm'));
            const data = Object.fromEntries(formData.entries());
            
            // Add current experience to array
            previousExperiences.push({
                employment_type: 'Previous',
                company_name: data.prev_company || '',
                nature_of_business: data.nature_of_business || '',
                position: data.position || data.other_position || '',
                job_description: data.job_desc || '',
                company_address: data.prev_address || '',
                type_of_company: data.type_of_company || '',
                date_from: data.prev_from || null,
                date_to: data.prev_to || null,
                employment_status: data.emp_status || ''
            });
            
            alert("Entry Saved. Let's add another.");
            document.getElementById('previousExpForm').reset();
            current = 0;
            updateUI();
        }

        function finishAndSubmit() {
            // Save current form if filled
            const formData = new FormData(document.getElementById('previousExpForm'));
            const data = Object.fromEntries(formData.entries());
            
            if (data.prev_company && data.prev_company.trim() !== '') {
                previousExperiences.push({
                    employment_type: 'Previous',
                    company_name: data.prev_company || '',
                    nature_of_business: data.nature_of_business || '',
                    position: data.position || data.other_position || '',
                    job_description: data.job_desc || '',
                    company_address: data.prev_address || '',
                    type_of_company: data.type_of_company || '',
                    date_from: data.prev_from || null,
                    date_to: data.prev_to || null,
                    employment_status: data.emp_status || ''
                });
            }
            
            // Save to localStorage
            let tracerData = JSON.parse(localStorage.getItem('tracer_payload')) || {};
            tracerData.previous_experiences = previousExperiences;
            localStorage.setItem('tracer_payload', JSON.stringify(tracerData));
            
            window.location.href = "final_submit.php";
        }

        updateUI();
    </script>

    <?php include 'includes/theme_toggle.php'; ?>
</body>
</html>