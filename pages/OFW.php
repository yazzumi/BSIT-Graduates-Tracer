<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OFW Profile | BSIT Tracer</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    

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
            border-color: var(--accent-purple); 
            color: var(--accent-purple); 
            font-weight: 600;
        }

        /* Choice Card Styling */
        .choice-card {
            background: #0a0a0a;
            border: 1px solid #1a1a1a;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1.5rem;
            border-radius: 16px;
            font-size: 0.95rem;
            color: #666;
            height: 100%;
        }

        .choice-card:hover {
            border-color: #333;
            color: #bbb;
        }

        /* The "Pop" effect when selected */
        .hidden-radio:checked + .choice-card {
            border-color: var(--accent-purple);
            background: rgba(168, 85, 247, 0.15);
            color: #fff;
            transform: scale(1.05);
            font-weight: 800;
            font-size: 1.05rem;
            box-shadow: 0 15px 40px rgba(168, 85, 247, 0.25);
            z-index: 10;
        }

        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }

        .progress-bar { 
            height: 4px; 
            background: #1a1a1a; 
            position: fixed; 
            top: 0; 
            left: 0; 
            z-index: 100; 
            width: 100%;
        }
        .progress-fill { height: 100%; background: var(--accent-purple); box-shadow: 0 0 15px var(--accent-purple); width: 0%; transition: width 0.6s ease; }

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
            
            .hidden-radio:checked + .choice-card {
                font-size: 0.8rem;
            }
            
            .grid {
                gap: 0.75rem !important;
            }
            
            #next, #prev {
                padding: 0.75rem 1.5rem;
                font-size: 10px;
            }
            
            .fixed.bottom-12 {
                bottom: 1rem;
                padding: 0 1rem;
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
            
            .flex.gap-10 {
                flex-direction: column;
                gap: 1rem;
            }
            
            .flex.gap-10 button {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="progress-bar">
        <div class="progress-fill" id="progressFill"></div>
    </div>

    <form id="ofwForm" class="px-6 h-screen flex items-center">
        
        <div class="step-container active">
            <span class="text-purple-500 font-mono text-xs tracking-[0.5em] mb-4">01 // WORKPLACE</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center">Company Name</h1>
            <input type="text" name="company_name" placeholder="Type international company..." class="input-focus" autocomplete="off">
        </div>

        <div class="step-container" data-required="company_address">
            <span class="text-purple-500 font-mono text-xs tracking-[0.5em] mb-4">02 // LOCATION</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center">Company Address</h1>
            <input type="text" name="company_address" placeholder="City, Country..." class="input-focus !text-2xl" autocomplete="off">
        </div>

        <div class="step-container" data-required="country">
            <span class="text-purple-500 font-mono text-xs tracking-[0.5em] mb-4">03 // COUNTRY</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center">Country</h1>
            <input type="text" name="country" placeholder="e.g., United Arab Emirates" class="input-focus" autocomplete="off">
        </div>

        <div class="step-container">
            <span class="text-purple-500 font-mono text-xs tracking-[0.5em] mb-4">04 // INDUSTRY</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center">Company Type</h1>
            <input type="text" name="company_type" placeholder="e.g. Technology, Logistics..." class="input-focus !text-2xl" autocomplete="off">
        </div>

        <div class="step-container">
            <span class="text-purple-500 font-mono text-xs tracking-[0.5em] mb-4">05 // DESIGNATION</span>
            <h1 class="text-3xl md:text-4xl font-extrabold mb-8 text-center">Overseas Position</h1>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-5 w-full custom-scroll overflow-y-auto max-h-[55vh] p-4">
                <?php
                $positions = [
                    "IT Academician / Teacher", "Programmer / Developer", "Network Administrator",
                    "IT Support Specialist / VA", "IT Engineer", "IT Analyst",
                    "IT Quality Control", "IT Researcher", "IT Technician",
                    "IT Consultant", "Computer Forensic / Investigator"
                ];
                foreach($positions as $pos) {
                    echo "
                    <label class='block h-24'>
                        <input type='radio' name='job_position' value='$pos' class='hidden hidden-radio pos-radio'>
                        <div class='choice-card'>$pos</div>
                    </label>";
                }
                ?>
                <div class="col-span-full mt-4">
                    <input type="text" id="other_pos_input" name="other_position" placeholder="Specify Other Position..." class="bg-[#0a0a0a] border border-[#1a1a1a] w-full p-5 rounded-2xl text-center focus:border-purple-500 outline-none text-xl transition-all">
                </div>
            </div>
        </div>

        <div class="step-container">
            <span class="text-purple-500 font-mono text-xs tracking-[0.5em] mb-4">06 // LEGAL</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center">Employment Status</h1>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 w-full px-4">
                <?php
                $status_list = ["Permanent", "Temporary", "Casual", "Contractual", "Job Order"];
                foreach($status_list as $status) {
                    echo "
                    <label class='h-40'>
                        <input type='radio' name='ofw_legal_status' value='$status' class='hidden hidden-radio'>
                        <div class='choice-card text-xl'>$status</div>
                    </label>";
                }
                ?>
            </div>
        </div>

        <div class="step-container">
            <span class="text-purple-500 font-mono text-xs tracking-[0.5em] mb-4">07 // TIMELINE</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-10 text-center">Inclusive Years</h1>
            <div class="flex flex-col md:flex-row gap-10 justify-center items-center w-full">
                <div class="w-full md:w-1/3 text-center">
                    <label class="block text-xs text-purple-500 mb-4 uppercase tracking-[0.3em] font-bold">Start Date</label>
                    <input type="date" name="start_year" class="w-full bg-transparent border-b-2 border-gray-800 p-4 focus:border-purple-500 outline-none text-white text-center text-xl">
                </div>
                <div class="w-full md:w-1/3 text-center">
                    <label class="block text-xs text-purple-500 mb-4 uppercase tracking-[0.3em] font-bold">End Date</label>
                    <input type="date" name="end_year" class="w-full bg-transparent border-b-2 border-gray-800 p-4 focus:border-purple-500 outline-none text-white text-center text-xl">
                </div>
            </div>
        </div>

        <div class="step-container">
            <span class="text-purple-500 font-mono text-xs tracking-[0.5em] mb-4">08 // HISTORY</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-12 text-center">Previous Experiences?</h1>
            <div class="flex gap-10">
                <button type="button" onclick="finalize('YES')" class="w-56 py-6 bg-purple-600 rounded-3xl font-black text-2xl hover:scale-110 transition-transform shadow-2xl shadow-purple-900/40">YES</button>
                <button type="button" onclick="finalize('NO')" class="w-56 py-6 border-2 border-gray-800 rounded-3xl font-black text-2xl hover:bg-white hover:text-black transition-all">NO</button>
            </div>
        </div>

    </form>

    <div class="fixed bottom-12 left-0 w-full px-12 flex justify-between items-center z-50">
        <button id="prev" class="text-gray-500 hover:text-white transition-colors opacity-0 pointer-events-none font-bold tracking-widest text-xs">
            <i class="fas fa-arrow-left mr-2"></i> PREVIOUS
        </button>
        <button id="next" class="px-12 py-5 rounded-full bg-white text-black font-black text-xs tracking-widest hover:scale-105 transition-all shadow-xl">
            CONTINUE <i class="fas fa-chevron-right ml-2"></i>
        </button>
    </div>

    <script>
        const steps = document.querySelectorAll('.step-container');
        const nextBtn = document.getElementById('next');
        const prevBtn = document.getElementById('prev');
        const progressFill = document.getElementById('progressFill');
        
        const otherPosInput = document.getElementById('other_pos_input');
        const posRadios = document.querySelectorAll('.pos-radio');
        let current = 0;

        function updateUI() {
            steps.forEach((s, i) => s.classList.toggle('active', i === current));
            const percent = ((current + 1) / steps.length) * 100;
            progressFill.style.width = percent + "%";

            prevBtn.style.opacity = current === 0 ? "0" : "1";
            prevBtn.style.pointerEvents = current === 0 ? "none" : "auto";
            nextBtn.style.display = current === steps.length - 1 ? "none" : "block";
            
            // Default Continue button style
            nextBtn.classList.add('bg-white', 'text-black');
            nextBtn.classList.remove('bg-purple-600', 'text-white', 'shadow-[0_0_20px_rgba(168,85,247,0.5)]');
        }

        // Logic to highlight "Continue" when a selection is made
        document.querySelectorAll('.hidden-radio').forEach(radio => {
            radio.addEventListener('change', () => {
                nextBtn.classList.remove('bg-white', 'text-black');
                nextBtn.classList.add('bg-purple-600', 'text-white', 'shadow-[0_0_20px_rgba(168,85,247,0.5)]');
            });
        });

        otherPosInput?.addEventListener('input', function() {
            if (this.value.trim() !== "") {
                posRadios.forEach(radio => radio.checked = false);
                nextBtn.classList.remove('bg-white', 'text-black');
                nextBtn.classList.add('bg-purple-600', 'text-white', 'shadow-[0_0_20px_rgba(168,85,247,0.5)]');
            }
        });

        // Clear other input when selecting a radio
        posRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked && otherPosInput) {
                    otherPosInput.value = "";
                }
            });
        });

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

        window.addEventListener('keydown', (e) => {
            if(e.key === "Enter" && current < steps.length - 1) {
                e.preventDefault();
                nextBtn.click();
            }
        });

        function finalize(choice) {
            // Save all OFW form data to localStorage
            const formData = new FormData(document.getElementById('ofwForm'));
            const data = Object.fromEntries(formData.entries());
            
            // Get existing data from localStorage or create new object
            let tracerData = JSON.parse(localStorage.getItem('tracer_payload')) || {};
            
            // Merge OFW data with existing data
            Object.assign(tracerData, data, {
                has_previous_exp: choice,
                employment_status: 'OFW'
            });
            
            // Save to localStorage
            localStorage.setItem('tracer_payload', JSON.stringify(tracerData));
            
            // Redirect based on previous experience choice
            if (choice === 'YES') {
                window.location.href = "previous_jobs.php";
            } else {
                window.location.href = "final_submit.php";
            }
        }

        updateUI();
    </script>

    <?php include 'includes/theme_toggle.php'; ?>
</body>
</html>