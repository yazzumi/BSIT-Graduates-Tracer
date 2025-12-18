<?php
// Get current page name for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <div class="brand">
        <i class="fas fa-database text-blue-500"></i>
        BSIT TRACER
    </div>
    
    <nav>
        <a href="dashboard.php" class="nav-item <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        
        <div class="nav-label">Records</div>
        <a href="graduates.php" class="nav-item <?php echo $current_page == 'graduates.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-graduate"></i> All Graduates
        </a>
        
        <div class="nav-label">Analytics</div>
        <a href="employed.php" class="nav-item <?php echo $current_page == 'employed.php' ? 'active' : ''; ?>">
            <i class="fas fa-briefcase"></i> Employed
        </a>
        <a href="unemployed.php" class="nav-item <?php echo $current_page == 'unemployed.php' ? 'active' : ''; ?>">
            <i class="fas fa-clock"></i> Unemployed
        </a>
        <a href="self_employed.php" class="nav-item <?php echo $current_page == 'self_employed.php' ? 'active' : ''; ?>">
            <i class="fas fa-store"></i> Self-Employed
        </a>
        <a href="ofw.php" class="nav-item <?php echo $current_page == 'ofw.php' ? 'active' : ''; ?>">
            <i class="fas fa-globe"></i> OFW
        </a>
    </nav>

    <a href="logout.php" class="nav-item" style="color: #ef4444; margin-top: 2rem;">
        <i class="fas fa-power-off"></i> Logout
    </a>
</aside>

<!-- Theme Toggle Switch -->
<div class="theme-switch-container">
    <label class="theme-switch">
        <input type="checkbox" id="theme-checkbox" onchange="toggleTheme()">
        <span class="slider"></span>
    </label>
</div>

<script>
    // Theme Toggle
    function toggleTheme() {
        const html = document.documentElement;
        const checkbox = document.getElementById('theme-checkbox');
        
        if (checkbox.checked) {
            html.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
        } else {
            html.removeAttribute('data-theme');
            localStorage.setItem('theme', 'dark');
        }
    }

    // Load saved theme on page load
    (function() {
        const savedTheme = localStorage.getItem('theme');
        const checkbox = document.getElementById('theme-checkbox');
        if (savedTheme === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
            if (checkbox) checkbox.checked = true;
        }
    })();
</script>
