<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Update | BSIT Tracer</title>
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

        .step-container {
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            max-width: 1000px;
            width: 100%;
            margin: 0 auto;
            animation: moveUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes moveUp {
            from { opacity: 0; transform: translateY(40px); filter: blur(10px); }
            to { opacity: 1; transform: translateY(0); filter: blur(0); }
        }

        /* Using the global accent purple from your root */
        .btn-primary {
            background-color: var(--accent-purple);
            box-shadow: 0 20px 40px -10px rgba(168, 85, 247, 0.4);
        }

        .btn-outline {
            border: 2px solid #1a1a1a;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: #fff;
            color: #000;
            border-color: #fff;
        }

        .progress-bar { 
            height: 4px; 
            background: #1a1a1a; 
            position: fixed; 
            top: 0; 
            left: 0; 
            z-index: 100; 
            width: 100%;
        }
        .progress-fill { 
            height: 100%; 
            background: var(--accent-purple); 
            box-shadow: 0 0 15px var(--accent-purple); 
            width: 50%; /* Halfway through the journey */
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            body {
                overflow-y: auto;
            }
            
            .step-container {
                height: auto;
                min-height: 100vh;
                padding: 2rem 0;
            }
            
            h1 {
                font-size: 2rem !important;
            }
            
            h2 {
                font-size: 1.25rem !important;
            }
            
            p {
                font-size: 0.9rem !important;
            }
            
            .bg-[#0a0a0a] {
                padding: 1.5rem !important;
            }
            
            .btn-primary, .btn-outline {
                padding: 1rem !important;
                font-size: 1.25rem !important;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem !important;
            }
            
            h2 {
                font-size: 1rem !important;
            }
            
            .flex-col.md\:flex-row {
                flex-direction: column !important;
            }
        }
    </style>
</head>
<body>

    <div class="progress-bar">
        <div class="progress-fill"></div>
    </div>

    <main class="px-6">
        <div class="step-container">
            <span class="text-purple-500 font-mono text-xs tracking-[0.5em] mb-4 uppercase">Status: Unemployed</span>
            
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4 text-center tracking-tighter">
                Refining your profile.
            </h1>
            <p class="text-gray-500 text-lg mb-12 text-center max-w-lg">
                Even if you are currently in-between roles, your previous professional journey matters.
            </p>

            <div class="w-full max-w-2xl bg-[#0a0a0a] border border-[#1a1a1a] p-10 rounded-[2rem] text-center">
                <h2 class="text-2xl font-semibold mb-10">Do you have previous work experiences?</h2>
                
                <div class="flex flex-col md:flex-row gap-6 justify-center">
                    <button type="button" onclick="handleChoice('YES')" 
                        class="btn-primary w-full md:w-64 py-6 rounded-2xl font-black text-2xl hover:scale-105 transition-transform">
                        YES
                    </button>
                    
                    <button type="button" onclick="handleChoice('NO')" 
                        class="btn-outline w-full md:w-64 py-6 rounded-2xl font-black text-2xl hover:scale-105 transition-transform">
                        NO
                    </button>
                </div>
            </div>

            <div class="mt-12">
                <a href="javascript:history.back()" class="text-gray-600 hover:text-white transition-colors font-bold tracking-widest text-xs">
                    <i class="fas fa-arrow-left mr-2"></i> GO BACK
                </a>
            </div>
        </div>
    </main>

    <script>
        function handleChoice(val) {
            // Save unemployed status to localStorage
            let tracerData = JSON.parse(localStorage.getItem('tracer_payload')) || {};
            tracerData.employment_status = 'Unemployed';
            tracerData.has_previous_exp = val;
            localStorage.setItem('tracer_payload', JSON.stringify(tracerData));
            
            // If they have experience, take them to previous experience wizard
            // If not, proceed to final submission/review page
            const target = (val === 'YES') ? 'previous_jobs.php' : 'final_submit.php';
            
            // Adding a small exit animation before redirecting
            document.querySelector('.step-container').style.opacity = '0';
            document.querySelector('.step-container').style.transform = 'translateY(-20px)';
            document.querySelector('.step-container').style.transition = 'all 0.4s ease';

            setTimeout(() => {
                window.location.href = target;
            }, 400);
        }
    </script>

    <?php include 'includes/theme_toggle.php'; ?>
</body>
</html>