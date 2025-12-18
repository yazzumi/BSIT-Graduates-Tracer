<!-- Theme Toggle Switch -->
<div class="theme-switch-container">
    <label class="theme-switch">
        <input type="checkbox" id="theme-checkbox" onchange="toggleTheme()">
        <span class="slider"></span>
    </label>
</div>

<style>
    /* Theme Toggle Switch */
    .theme-switch-container {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(10, 10, 10, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 50px;
        padding: 0.5rem 1rem;
        backdrop-filter: blur(10px);
    }


    .theme-switch {
        position: relative;
        width: 44px;
        height: 24px;
    }

    .theme-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #3a82f6;
        transition: 0.3s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #1e293b;
    }

    input:checked + .slider:before {
        transform: translateX(20px);
    }

    [data-theme="light"] .theme-switch-container {
        background: rgba(255, 255, 255, 0.9);
        border-color: #e2e8f0;
    }

    /* Light Theme Overrides for Public Pages */
    [data-theme="light"] body {
        background-color: #f8fafc !important;
        color: #0f172a !important;
    }

    [data-theme="light"] .bg-glow {
        background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, transparent 70%);
    }

    [data-theme="light"] .glass-panel,
    [data-theme="light"] .bg-\[#0a0a0a\] {
        background: rgba(255, 255, 255, 0.9) !important;
        border-color: #e2e8f0 !important;
    }

    [data-theme="light"] h1, 
    [data-theme="light"] h2, 
    [data-theme="light"] h3 {
        color: #0f172a !important;
    }

    [data-theme="light"] p,
    [data-theme="light"] label,
    [data-theme="light"] span {
        color: #475569 !important;
    }

    [data-theme="light"] .text-white {
        color: #0f172a !important;
    }

    [data-theme="light"] .text-gray-400,
    [data-theme="light"] .text-gray-500 {
        color: #64748b !important;
    }

    [data-theme="light"] .tech-input,
    [data-theme="light"] .glass-input,
    [data-theme="light"] input[type="text"],
    [data-theme="light"] input[type="date"],
    [data-theme="light"] select {
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
        color: #0f172a !important;
    }

    [data-theme="light"] .radio-label,
    [data-theme="light"] .status-node {
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
        color: #0f172a !important;
    }

    [data-theme="light"] .theme-toggle {
        background: rgba(255, 255, 255, 0.9);
        border-color: #e2e8f0;
        color: #64748b;
    }

    [data-theme="light"] .grid-overlay {
        background-image: linear-gradient(rgba(0, 0, 0, 0.03) 1px, transparent 1px),
                          linear-gradient(90deg, rgba(0, 0, 0, 0.03) 1px, transparent 1px);
    }

    [data-theme="light"] footer,
    [data-theme="light"] footer a,
    [data-theme="light"] footer div {
        color: #64748b !important;
    }
</style>

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
