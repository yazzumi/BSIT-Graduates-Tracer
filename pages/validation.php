<?php
session_start();
require_once "../config/db_conn.php";

$alert_msg = "";
$alert_type = "";
$user_name = "";

if (isset($_SESSION['alert'])) {
    $alert_msg = $_SESSION['alert']['msg'];
    $alert_type = $_SESSION['alert']['type'];
    $user_name = $_SESSION['alert']['name'] ?? "";
    unset($_SESSION['alert']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['full_name'])) {
    $full_name = trim($_POST['full_name']);
    $student_id = trim($_POST['student_id']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM validated_graduates WHERE student_id = :sid AND full_name = :fname LIMIT 1");
        $stmt->execute([':sid' => $student_id, ':fname' => $full_name]);
        $user = $stmt->fetch();

        if ($user) {
            // SUCCESS LOGIC - Removed one-time submission check for testing
            $_SESSION['temp_full_name'] = $full_name;
            $_SESSION['temp_student_id'] = $student_id;
            $_SESSION['is_validated'] = true;

            $_SESSION['alert'] = [
                'type' => 'success',
                'msg' => "You are verified as a legitimate graduate. You may now proceed to the survey.",
                'name' => $full_name
            ];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // FAIL LOGIC
            $_SESSION['alert'] = [
                'type' => 'error',
                'msg' => "Identity not found. Please check your Student ID and Name.",
                'name' => ""
            ];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['alert'] = ['type' => 'error', 'msg' => "Database Error.", 'name' => ""];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSIT Tracer | Identity Validation</title>
    <link rel="stylesheet" href="../output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #000; color: #808080; font-family: 'Inter', sans-serif; overflow: hidden; }
        .glass-input { background: #0a0a0a; border: 1px solid #1a1a1a; color: white; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .glass-input option { background: #0a0a0a; color: white; }
        select.glass-input { background: #0a0a0a; color: white; }
        .glass-input:focus { border-color: var(--accent); background: linear-gradient(135deg, #0f0f0f, rgba(58, 130, 246, 0.08)); outline: none; transform: scale(1.02); box-shadow: 0 0 25px rgba(58, 130, 246, 0.4), inset 0 0 15px rgba(58, 130, 246, 0.08); }
        .glass-input::placeholder { color: #666; }
        .glass-input:focus::placeholder { color: #888; }
        .step-content { display: none; opacity: 0; transform: translateX(15px) scale(0.98); }
        .step-content.active { display: block; animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes slideIn { 
            0% { opacity: 0; transform: translateX(20px) scale(0.98); filter: blur(2px); }
            100% { opacity: 1; transform: translateX(0) scale(1); filter: blur(0); } 
        }
        .scan-line { width: 100%; height: 2px; background: linear-gradient(90deg, transparent, var(--accent), transparent); position: absolute; top: 0; left: 0; animation: scanning 2s linear infinite; box-shadow: 0 0 10px var(--accent); }
        @keyframes scanning { 0% { top: 0%; } 100% { top: 100%; } }
        .year-btn.selected { border-color: var(--accent); background: linear-gradient(135deg, rgba(58, 130, 246, 0.2), rgba(96, 165, 250, 0.1)); color: white; transform: scale(1.05) translateY(-2px); font-weight: 800; box-shadow: 0 10px 30px rgba(58, 130, 246, 0.3); }
        .year-btn { transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
        .year-btn:hover { transform: translateY(-2px) scale(1.02); border-color: rgba(58, 130, 246, 0.5); }
        .summary-box { background: linear-gradient(135deg, rgba(255,255,255,0.03), rgba(58, 130, 246, 0.05)); border-left: 2px solid var(--accent); padding: 1.5rem; border-radius: 12px; }

        /* Responsive Styles */
        @media (max-width: 768px) {
            body {
                overflow-y: auto;
            }
            
            .w-full.md\:w-1\/3 {
                padding: 1.5rem !important;
            }
            
            h1 {
                font-size: 2rem !important;
            }
            
            h3 {
                font-size: 1.75rem !important;
            }
            
            .glass-input {
                padding: 1rem !important;
                font-size: 1rem !important;
            }
            
            .year-btn {
                padding: 0.75rem !important;
            }
            
            #nextBtn {
                padding: 0.75rem 1.5rem !important;
            }
            
            .p-8.md\:p-20 {
                padding: 1.5rem !important;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem !important;
            }
            
            h3 {
                font-size: 1.25rem !important;
            }
            
            .glass-input {
                padding: 0.75rem !important;
                font-size: 0.9rem !important;
            }
            
            .grid-cols-2 {
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 0.5rem !important;
            }
            
            .year-btn {
                padding: 0.5rem !important;
                font-size: 0.85rem;
            }
            
            .summary-box {
                padding: 1rem !important;
            }
        }

        /* Light Theme Overrides */
        [data-theme="light"] body {
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }

        [data-theme="light"] .bg-\[\#050505\],
        [data-theme="light"] .bg-\[\#0a0a0a\] {
            background: #ffffff !important;
        }

        [data-theme="light"] .border-gray-900 {
            border-color: #e2e8f0 !important;
        }

        [data-theme="light"] .glass-input {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        [data-theme="light"] .glass-input option {
            background: #ffffff !important;
            color: #0f172a !important;
        }

        [data-theme="light"] select.glass-input {
            background: #ffffff !important;
            color: #0f172a !important;
        }

        [data-theme="light"] .glass-input::placeholder {
            color: #94a3b8 !important;
        }

        [data-theme="light"] h1,
        [data-theme="light"] h2,
        [data-theme="light"] h3 {
            color: #0f172a !important;
        }

        [data-theme="light"] .text-white {
            color: #0f172a !important;
        }

        [data-theme="light"] .text-gray-500,
        [data-theme="light"] .text-gray-600 {
            color: #64748b !important;
        }

        [data-theme="light"] .year-btn {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        [data-theme="light"] .year-btn.selected {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(59, 130, 246, 0.15)) !important;
            border-color: #3b82f6 !important;
        }

        [data-theme="light"] .summary-box {
            background: linear-gradient(135deg, rgba(0,0,0,0.02), rgba(37, 99, 235, 0.05)) !important;
        }

        [data-theme="light"] #review-name,
        [data-theme="light"] #review-id,
        [data-theme="light"] #review-year,
        [data-theme="light"] #review-school {
            color: #0f172a !important;
        }

        [data-theme="light"] .scan-line {
            background: linear-gradient(90deg, transparent, #3b82f6, transparent);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row bg-black">

    <?php if ($alert_type === 'success'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Hi, <?php echo htmlspecialchars($user_name); ?>!',
            text: '<?php echo $alert_msg; ?>',
            background: '#0a0a0a',
            color: '#fff',
            confirmButtonColor: '#3a82f6',
            confirmButtonText: 'Start Survey'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'survey_form.php';
            }
        });
    </script>
    <?php elseif ($alert_type === 'error'): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Access Denied',
            text: '<?php echo $alert_msg; ?>',
            background: '#0a0a0a',
            color: '#fff',
            confirmButtonColor: '#ef4444'
        });
    </script>
    <?php endif; ?>

    <div class="w-full md:w-1/3 bg-[#050505] border-r border-gray-900 p-12 flex flex-col justify-between relative overflow-hidden">
        <div class="scan-line"></div>
        <div class="z-10">
            <h2 class="text-accent font-mono text-[10px] tracking-[0.5em] uppercase mb-4">Security Module</h2>
            <h1 class="text-white text-4xl font-black leading-tight tracking-tighter">Identity<br>Verification</h1>
            <p class="mt-6 text-sm leading-relaxed text-gray-500 font-light">Verifying your records...</p>
        </div>
    </div>

    <div class="flex-1 p-8 md:p-20 flex flex-col justify-center items-center">
        <form id="multiStepForm" action="" method="POST" class="w-full max-w-xl">
            <div class="step-content active" id="step1">
                <div class="mb-12"><span class="text-accent font-mono text-xs">01 // SOURCE</span><h3 class="text-4xl text-white font-black tracking-tighter mt-2">Campus Location</h3></div>
                <select id="schoolInput" name="school_graduated" class="glass-input w-full p-6 rounded-2xl text-lg appearance-none cursor-pointer">
                    <option value="">Select University Campus...</option>
                    <option value="ISU - Echague">ISU - Echague Campus</option>
                    <option value="ISU - Ilagan">ISU - Ilagan Campus</option>
                    <option value="ISU - Cauayan">ISU - Cauayan Campus</option>
                </select>
            </div>
            
            <div class="step-content" id="step2">
                <div class="mb-12"><span class="text-accent font-mono text-xs">02 // IDENTITY</span><h3 class="text-4xl text-white font-black tracking-tighter mt-2">Graduate Name</h3></div>
                <input type="text" id="nameInput" name="full_name" placeholder="e.g., CRUZ, JUAN SANTOS" class="glass-input w-full p-6 rounded-2xl text-xl uppercase tracking-widest">
            </div>

            <div class="step-content" id="step3">
                <div class="mb-12"><span class="text-accent font-mono text-xs">03 // CREDENTIALS</span><h3 class="text-4xl text-white font-black tracking-tighter mt-2">Student ID</h3></div>
                <input type="text" id="idInput" name="student_id" placeholder="21-12345" maxlength="8" class="glass-input w-full p-6 rounded-2xl text-xl font-mono tracking-widest">
            </div>

            <div class="step-content" id="step4">
                <div class="mb-12"><span class="text-accent font-mono text-xs">04 // TIMELINE</span><h3 class="text-4xl text-white font-black tracking-tighter mt-2">Batch Year</h3></div>
                <div class="grid grid-cols-2 gap-4">
                    <button type="button" class="year-btn glass-input p-5 rounded-xl">2024</button>
                    <button type="button" class="year-btn glass-input p-5 rounded-xl">2023</button>
                    <button type="button" class="year-btn glass-input p-5 rounded-xl">2022</button>
                    <button type="button" class="year-btn glass-input p-5 rounded-xl">2021</button>
                    <button type="button" class="year-btn glass-input p-5 rounded-xl">2020</button>
                    <button type="button" class="year-btn glass-input p-5 rounded-xl">2019</button>
                </div>
                <input type="hidden" name="year_graduated" id="gradYearHidden">
            </div>

            <div class="step-content" id="step5">
                    <div class="mb-12">
                        <span class="text-accent font-mono text-xs">05 // CONFIRMATION</span>
                        <h3 class="text-4xl text-white font-black tracking-tighter mt-2">Final Review</h3>
                    </div>
                    <div class="summary-box space-y-4">
                        <div>
                            <label class="text-[9px] uppercase tracking-widest text-gray-600 block">Full Name</label>
                            <span id="review-name" class="text-white font-bold text-lg">---</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[9px] uppercase tracking-widest text-gray-600 block">Student ID</label>
                                <span id="review-id" class="text-white font-mono">---</span>
                            </div>
                            <div>
                                <label class="text-[9px] uppercase tracking-widest text-gray-600 block">Batch Year</label>
                                <span id="review-year" class="text-white">---</span>
                            </div>
                        </div>

                        <div>
                            <label class="text-[9px] uppercase tracking-widest text-gray-600 block">University Campus</label>
                            <span id="review-school" class="text-white text-sm">---</span>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-600 mt-6 italic">Please ensure all details match your official university records before proceeding.</p>
                </div>

            <div class="mt-20 flex items-center justify-between">
                <button type="button" id="prevBtn" class="invisible text-gray-600 uppercase font-black text-[10px]">Back</button>
                <button type="button" id="nextBtn" disabled class="bg-white text-black px-12 py-5 rounded-full font-black text-[10px] uppercase shadow-xl">
                    <span id="btnText">Continue</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        // JS Step Logic (Same as your working version)
        const steps = document.querySelectorAll('.step-content');
        const nextBtn = document.getElementById('nextBtn');
        const prevBtn = document.getElementById('prevBtn');
        const gradYearHidden = document.getElementById('gradYearHidden');
        let currentStep = 0;

        const validate = () => {
            let isValid = false;
            if (currentStep === 0) isValid = document.getElementById('schoolInput').value !== "";
            if (currentStep === 1) isValid = document.getElementById('nameInput').value.trim().length > 4;
            if (currentStep === 2) isValid = /^\d{2}-\d{5}$/.test(document.getElementById('idInput').value);
            if (currentStep === 3) isValid = gradYearHidden.value !== "";
            if (currentStep === 4) isValid = true;
            nextBtn.disabled = !isValid;
        };

        document.getElementById('idInput').addEventListener('input', (e) => {
            let val = e.target.value.replace(/\D/g, '');
            if (val.length > 2) val = val.substring(0, 2) + '-' + val.substring(2, 7);
            e.target.value = val;
            validate();
        });

        nextBtn.addEventListener('click', () => {
            if (currentStep < steps.length - 1) {
                currentStep++;
                if(currentStep === 4) {
                    document.getElementById('review-name').innerText = document.getElementById('nameInput').value.toUpperCase();
                    document.getElementById('review-id').innerText = document.getElementById('idInput').value;
                    document.getElementById('review-school').innerText = document.getElementById('schoolInput').value;
                    document.getElementById('review-year').innerText = document.getElementById('gradYearHidden').value;
                }
                updateUI();
                validate();
            } else {
                document.getElementById('multiStepForm').submit();
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentStep > 0) { currentStep--; updateUI(); validate(); }
        });

        function updateUI() {
            steps.forEach((s, i) => s.classList.toggle('active', i === currentStep));
            prevBtn.classList.toggle('invisible', currentStep === 0);
            document.getElementById('btnText').innerText = currentStep === steps.length - 1 ? "VERIFY & PROCEED" : "CONTINUE";
        }

        document.querySelectorAll('.year-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.year-btn').forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
                gradYearHidden.value = this.innerText;
                validate();
            });
        });

        document.querySelectorAll('input, select').forEach(el => {
            el.addEventListener('input', validate);
            el.addEventListener('change', validate);
        });
    </script>

    <?php include 'includes/theme_toggle.php'; ?>
</body>
</html>