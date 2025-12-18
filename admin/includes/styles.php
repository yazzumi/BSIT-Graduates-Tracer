<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    :root {
        --sidebar-width: 260px;
        --bg-dark: #09090b;
        --card-bg: #18181b;
        --border: #27272a;
        --accent: #3b82f6;
        --text-muted: #a1a1aa;
    }

    body { 
        background-color: var(--bg-dark); 
        color: #fafafa; 
        font-family: 'Inter', sans-serif;
        margin: 0;
        display: flex;
    }

    /* --- Sidebar --- */
    .sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        background: #000;
        border-right: 1px solid var(--border);
        position: fixed;
        padding: 2rem 1rem;
        display: flex;
        flex-direction: column;
    }

    .brand {
        font-weight: 700;
        font-size: 1.1rem;
        padding: 0 1rem 2rem;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.75rem 1rem;
        color: var(--text-muted);
        text-decoration: none;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: 0.2s;
        margin-bottom: 4px;
    }

    .nav-item:hover, .nav-item.active {
        background: var(--card-bg);
        color: #fff;
    }

    .nav-item.active { border: 1px solid var(--border); }

    .nav-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #52525b;
        font-weight: 700;
        margin: 1.5rem 0 0.5rem 1rem;
    }

    /* --- Main Content --- */
    .content {
        margin-left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
        padding: 3rem;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 3rem;
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 1.5rem;
    }

    .card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
    }

    .col-3 { grid-column: span 3; }
    .col-4 { grid-column: span 4; }
    .col-6 { grid-column: span 6; }
    .col-8 { grid-column: span 8; }
    .col-12 { grid-column: span 12; }

    .stat-val { font-size: 2rem; font-weight: 700; margin-top: 0.5rem; }
    .stat-lab { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; }

    /* --- Chart Styling --- */
    .chart-box { height: 300px; width: 100%; position: relative; }

    /* --- Tables --- */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .data-table th {
        text-align: left;
        padding: 1rem;
        color: #71717a;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border);
        font-weight: 600;
    }

    .data-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        color: #d4d4d8;
    }

    .data-table tr:hover td {
        background: rgba(255, 255, 255, 0.02);
    }

    /* --- Badges --- */
    .badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-employed { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .badge-ofw { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .badge-self-employed { background: rgba(234, 179, 8, 0.15); color: #eab308; }
    .badge-unemployed { background: rgba(161, 161, 170, 0.15); color: #a1a1aa; }

    /* --- Activity List --- */
    .activity-list { list-style: none; padding: 0; margin: 0; }
    .activity-item {
        padding: 1rem 0;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .activity-item:last-child { border: none; }

    .logout-link { margin-top: auto; color: #ef4444; }

    /* Theme Toggle Switch */
    .theme-switch-container {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 1002;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 50px;
        padding: 0.5rem 1rem;
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
        background-color: var(--accent);
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
        background: #ffffff;
        border-color: #e2e8f0;
    }

    /* Light theme overrides for admin */
    [data-theme="light"] body {
        background-color: #f8fafc;
        color: #0f172a;
    }

    [data-theme="light"] .sidebar {
        background: #ffffff;
        border-color: #e2e8f0;
    }

    [data-theme="light"] .nav-item {
        color: #64748b;
    }

    [data-theme="light"] .nav-item:hover,
    [data-theme="light"] .nav-item.active {
        background: #f1f5f9;
        color: #0f172a;
    }

    [data-theme="light"] .card {
        background: #ffffff;
        border-color: #e2e8f0;
    }

    [data-theme="light"] .data-table th {
        color: #64748b;
        border-color: #e2e8f0;
    }

    [data-theme="light"] .data-table td {
        color: #334155;
        border-color: #e2e8f0;
    }

    [data-theme="light"] .data-table tr:hover td {
        background: #f8fafc;
    }

    [data-theme="light"] .search-input,
    [data-theme="light"] .form-input {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #0f172a;
    }

    [data-theme="light"] .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border-color: #e2e8f0;
    }

    [data-theme="light"] .mobile-menu-btn {
        background: #ffffff;
        border-color: #e2e8f0;
        color: #0f172a;
    }

    [data-theme="light"] .modal-content {
        background: #ffffff;
        border-color: #e2e8f0;
    }

    [data-theme="light"] .modal-content .modal-title,
    [data-theme="light"] .modal-content h2,
    [data-theme="light"] .modal-content h3 {
        color: #0f172a;
    }

    [data-theme="light"] .modal-content .form-label {
        color: #64748b;
    }

    [data-theme="light"] .modal-content .form-input,
    [data-theme="light"] .modal-content select {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #0f172a;
    }

    [data-theme="light"] .modal-content .close-btn {
        color: #64748b;
    }

    [data-theme="light"] .modal-content .close-btn:hover {
        color: #0f172a;
    }

    [data-theme="light"] .modal {
        background: rgba(0, 0, 0, 0.4);
    }

    [data-theme="light"] .brand {
        color: #0f172a;
    }

    /* Mobile Menu Toggle */
    .mobile-menu-btn {
        display: none;
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 1001;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 0.75rem;
        color: #fff;
        cursor: pointer;
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 999;
    }

    /* Responsive Styles */
    @media (max-width: 1024px) {
        .col-3 { grid-column: span 6; }
        .col-4 { grid-column: span 6; }
        .col-8 { grid-column: span 12; }
    }

    @media (max-width: 768px) {
        .mobile-menu-btn {
            display: block;
        }

        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar-overlay.open {
            display: block;
        }

        .content {
            margin-left: 0;
            width: 100%;
            padding: 1.5rem;
            padding-top: 4rem;
        }

        .header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .col-3, .col-4, .col-6, .col-8, .col-12 {
            grid-column: span 12;
        }

        .grid {
            gap: 1rem;
        }

        .stat-val {
            font-size: 1.5rem;
        }

        .chart-box {
            height: 250px;
        }

        .data-table {
            font-size: 0.75rem;
        }

        .data-table th, .data-table td {
            padding: 0.75rem 0.5rem;
        }

        .search-bar {
            flex-direction: column;
        }

        .search-bar form {
            flex-direction: column !important;
        }
    }

    @media (max-width: 480px) {
        .content {
            padding: 1rem;
            padding-top: 4rem;
        }

        h1 {
            font-size: 1.4rem !important;
        }

        .card {
            padding: 1rem;
        }

        .stat-val {
            font-size: 1.25rem;
        }

        .chart-box {
            height: 200px;
        }

        .data-table th, .data-table td {
            padding: 0.5rem 0.25rem;
            font-size: 0.7rem;
        }

        .btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }

        .btn-sm {
            padding: 0.35rem 0.5rem;
            font-size: 0.65rem;
        }

        .actions {
            flex-direction: column;
            gap: 0.25rem;
        }

        .modal-content {
            padding: 1.5rem;
        }

        .form-actions {
            flex-direction: column;
        }
    }
</style>
