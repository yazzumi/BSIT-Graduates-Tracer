<?php
// 1. Kunin ang choice mula sa URL (Dating logic mo)
$hasPreviousExperience = isset($_GET['prev']) ? $_GET['prev'] : 'NO';

if ($hasPreviousExperience === 'YES') {
    // 2. Kapag YES, diretso agad sa previous_jobs.php (Dating logic mo)
    header("Location: previous_jobs.php"); 
    exit();
} else {
    // 3. Kapag NO, dito natin ilalagay ang "New Logic" para makuha ang data
    // Gagamit tayo ng HTML/JS bridge dahil ang data ay nasa LocalStorage
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Finalizing...</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body { background: #000; color: #fff; font-family: sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        </style>
    </head>
    <body>
        <div style="text-align: center;">
            <i class="fas fa-circle-notch fa-spin" style="font-size: 2rem; color: #3a82f6;"></i>
            <p style="margin-top: 15px; font-size: 0.8rem; letter-spacing: 2px;">SAVING PROFILE...</p>
        </div>

        <script>
            async function saveDataAndRedirect() {
                // Kunin ang data mula sa LocalStorage
                const payload = JSON.parse(localStorage.getItem('tracer_payload'));

                if (payload) {
                    try {
                        // Ipadala sa save_to_db.php (ito yung backend processor)
                        const response = await fetch('save_to_db.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        });

                        const result = await response.json();
                        
                        if (result.success) {
                            console.log("Data saved successfully");
                            // Clear localStorage after successful save
                            localStorage.removeItem('tracer_payload');
                        } else {
                            console.error("Save failed:", result.message);
                        }
                    } catch (err) {
                        console.error("Save failed:", err);
                    }
                }

                // Final redirect to success page
                window.location.href = "success_page.php";
            }

            // Patakbuhin ang function pagka-load
            window.onload = saveDataAndRedirect;
        </script>
    </body>
    </html>
    <?php
    exit();
}
?>