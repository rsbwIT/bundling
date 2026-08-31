<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise Dashboard - @yield('title')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Base CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            /* Enterprise Color Palette */
            --sidebar-bg: #0f172a; /* Deep Slate */
            --sidebar-hover: #1e293b;
            --sidebar-active: #2563eb; /* Corporate Blue */
            --sidebar-text: #94a3b8;
            --sidebar-text-light: #f8fafc;
            
            --topbar-bg: #ffffff;
            --main-bg: #f1f5f9; /* Very light slate */
            
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            
            --text-main: #0f172a;
            --text-muted: #64748b;
            
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--main-bg);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* --- Scrollbar --- */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--main-bg); }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* --- Sidebar --- */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background-color: var(--sidebar-bg);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            color: var(--sidebar-text-light);
            border-right: 1px solid #1e293b;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 1.25rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none;
            background-color: #0b1120; /* Slightly darker header */
            border-bottom: 1px solid #1e293b;
            letter-spacing: 0.5px;
        }
        
        .brand i {
            color: var(--sidebar-active);
            font-size: 1.25rem;
        }

        .nav-menu {
            list-style: none;
            padding: 1.25rem 0.75rem;
            margin: 0;
            flex-grow: 1;
        }

        .nav-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--sidebar-text);
            font-weight: 600;
            margin: 1rem 0 0.5rem 0.75rem;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.6rem 0.75rem;
            border-radius: 6px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .nav-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: var(--sidebar-text-light);
        }

        .nav-link.active {
            background-color: var(--sidebar-active);
            color: #ffffff;
        }

        /* --- Topbar --- */
        .topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            height: 60px;
            background-color: var(--topbar-bg);
            border-bottom: 1px solid var(--border-color);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02);
        }

        .topbar-search {
            position: relative;
            width: 280px;
        }

        .topbar-search input {
            width: 100%;
            background-color: var(--main-bg);
            border: 1px solid transparent;
            color: var(--text-main);
            padding: 0.4rem 1rem 0.4rem 2.2rem;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: border-color 0.2s;
        }

        .topbar-search input::placeholder { color: #94a3b8; }
        .topbar-search input:focus { 
            outline: none; 
            border-color: var(--sidebar-active);
            background-color: #fff;
        }

        .topbar-search i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--sidebar-active);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* --- Main Content --- */
        .main-content {
            margin-left: 260px;
            padding: calc(60px + 1.5rem) 1.5rem 1.5rem 1.5rem;
            min-height: 100vh;
        }
        
        .page-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 1.5rem;
        }

        /* --- Override Inner Content Cards (Enterprise Style) --- */
        main .card, main .card-endpoint, main .chart-card {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px !important; /* Professional slight curve */
            box-shadow: var(--shadow-sm) !important;
            color: var(--text-main) !important;
        }
        main .card:hover, main .card-endpoint:hover {
            box-shadow: var(--shadow-md) !important;
            border-color: #cbd5e1 !important;
        }
        main .card-header {
            border-bottom: 1px solid var(--border-color) !important;
            background-color: #f8fafc !important; /* Very subtle off-white for header */
            padding: 1rem 1.25rem !important;
            border-radius: 8px 8px 0 0 !important;
        }
        main h6, main .fw-bold, main .chart-title {
            color: var(--text-main) !important;
            font-weight: 600 !important;
        }
        main .text-muted {
            color: var(--text-muted) !important;
        }
        main .bg-light {
            background-color: #f8fafc !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
        }
        
        /* Clean Enterprise Badges */
        main .status-badge {
            font-weight: 500 !important;
            padding: 2px 8px !important;
            border-radius: 4px !important; /* Square-ish professional */
            font-size: 0.7rem !important;
        }
        main .status-online { background-color: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
        main .status-offline { background-color: #fef2f2 !important; color: #dc2626 !important; border: 1px solid #fecaca !important; }
        main .status-loading { background-color: #fffbeb !important; color: #d97706 !important; border: 1px solid #fde68a !important; }
        
        main .latency-bar-bg { background-color: #e2e8f0 !important; height: 4px !important; border-radius: 2px !important;}
        main .btn-light {
            background-color: #ffffff !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-muted) !important;
        }
        main .btn-light:hover { 
            background-color: #f8fafc !important; 
            color: var(--text-main) !important;
        }
    </style>
</head>
<body>
    
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="#" class="brand">
            <i class="fa-solid fa-hospital"></i>
            <span>RSBW Enterprise</span>
        </a>
        
        <ul class="nav-menu">
            <div class="nav-section-title">Dashboards</div>
            <li class="nav-item">
                <a href="/test-theme" class="nav-link">
                    <i class="fa-solid fa-chart-bar"></i>
                    <span>Overview</span>
                </a>
            </li>
            
            <div class="nav-section-title">System Status</div>
            <li class="nav-item">
                <a href="#" class="nav-link active">
                    <i class="fa-solid fa-network-wired"></i>
                    <span>BPJS Monitoring</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>System Logs</span>
                </a>
            </li>
            
            <div class="nav-section-title">Settings</div>
            <li class="nav-item">
                <a href="/" class="nav-link">
                    <i class="fa-solid fa-sign-out-alt"></i>
                    <span>Kembali ke Tema Lama</span>
                </a>
            </li>
        </ul>
        
        <!-- Sidebar Footer -->
        <div style="padding: 1rem; border-top: 1px solid #1e293b; font-size: 0.75rem; color: #64748b;">
            RSBW IT Division &copy; 2026
        </div>
    </aside>

    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-search">
            <i class="fa-solid fa-search"></i>
            <input type="text" placeholder="Search logs, endpoints...">
        </div>
        
        <div class="d-flex align-items-center gap-4">
            <a href="#" style="color: #64748b; font-size: 1.1rem; position: relative;">
                <i class="fa-regular fa-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger rounded-circle" style="width: 6px; height: 6px;">
                    <span class="visually-hidden">New alerts</span>
                </span>
            </a>
            
            <div class="user-profile">
                <div class="d-none d-md-block text-end">
                    <div style="font-weight: 600; font-size: 0.8rem; color: #0f172a; line-height: 1.2;">Admin RSBW</div>
                    <div style="font-size: 0.7rem; color: #64748b;">IT Administrator</div>
                </div>
                <div class="avatar">AD</div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <h1 class="page-title">@yield('title')</h1>
        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
