<?php
// Get the choice from the URL
$hasPreviousExperience = isset($_GET['prev']) ? $_GET['prev'] : 'NO';

// logic to save the current 'Employed' data to your database would go here

if ($hasPreviousExperience === 'YES') {
    // Redirect to a form for previous jobs
    header("Location: previous_jobs.php"); 
} else {
    // No previous experience, go straight to the shooting stars success page
    header("Location: success_page.php");
}
exit();
?>