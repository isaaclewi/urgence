<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Administrateur') - CongoAssist</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ===== RESET & BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #1a202c;
            overflow-x: hidden;
        }

        /* ===== CONTAINER ===== */
        .container {
            display: flex;
            min-height: 100vh;
        }

        /* ===== MOBILE OVERLAY ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.08);
            padding: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 999;
        }

        /* Logo Section */
        .logo {
            display: flex;
            align-items: center;
            padding: 32px 24px;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            position: relative;
        }

        .mobile-close {
            display: none;
            position: absolute;
            top: 32px;
            right: 24px;
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .mobile-close:hover {
            color: #667eea;
            transform: rotate(90deg);
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 20px;
            margin-right: 14px;
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
            transition: transform 0.3s ease;
        }

        .logo-icon:hover {
            transform: rotate(5deg) scale(1.05);
        }

        .logo-text {
            font-size: 18px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Menu Navigation */
        .menu-nav {
            flex: 1;
            padding: 20px 12px;
            overflow-y: auto;
        }

        .menu-item {
            padding: 14px 20px;
            display: flex;
            align-items: center;
            color: #64748b;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 15px;
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .menu-item:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            color: #667eea;
            transform: translateX(4px);
        }

        .menu-item:hover::before {
            transform: scaleY(1);
        }

        .menu-item.active {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
            color: #667eea;
            font-weight: 600;
        }

        .menu-item.active::before {
            transform: scaleY(1);
        }

        .menu-item i {
            width: 22px;
            margin-right: 14px;
            font-size: 18px;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
            background: #f8fafc;
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
            z-index: 997;
            transition: all 0.3s ease;
        }

        .mobile-menu-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.5);
        }

        /* ===== HEADER ===== */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            background: white;
            padding: 20px 28px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }

        .search-bar {
            display: flex;
            align-items: center;
            background: #f1f5f9;
            padding: 12px 20px;
            border-radius: 12px;
            width: 380px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .search-bar:focus-within {
            background: white;
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .search-bar i {
            color: #94a3b8;
            margin-right: 12px;
            font-size: 16px;
        }

        .search-bar input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 15px;
            color: #1e293b;
            font-weight: 500;
        }

        .search-bar input::placeholder {
            color: #94a3b8;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .notification-icon {
            position: relative;
            cursor: pointer;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .notification-icon:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            transform: translateY(-2px);
        }

        .notification-icon i {
            color: #64748b;
            font-size: 19px;
        }

        .badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
            color: white;
            border-radius: 10px;
            padding: 3px 7px;
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(244, 63, 94, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 16px 8px 8px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .user-profile:hover {
            background: #f8fafc;
        }

        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #fff;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-profile span {
            font-weight: 600;
            color: #1e293b;
            font-size: 15px;
        }

        /* ===== STAT CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(102, 126, 234, 0.15);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-header {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .chart-bars {
            display: flex;
            align-items: flex-end;
            height: 60px;
            gap: 6px;
            margin-top: 20px;
        }

        .chart-bar {
            flex: 1;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            border-radius: 6px 6px 0 0;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .chart-bar:hover {
            opacity: 1;
            transform: scaleY(1.1);
        }

        .chart-bar.green {
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
        }

        .chart-bar.red {
            background: linear-gradient(180deg, #f43f5e 0%, #e11d48 100%);
        }

        .chart-bar.blue {
            background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
        }

        .stat-value {
            color: #10b981;
            font-size: 15px;
            font-weight: 700;
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .stat-value.negative {
            color: #f43f5e;
        }

        .stat-value i {
            font-size: 14px;
        }

        /* ===== ACTION CARDS ===== */
        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title::before {
            content: '';
            width: 4px;
            height: 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }

        .action-illustrations {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
            margin-top: 20px;
        }

        .action-illustrations div {
            text-align: center;
            cursor: pointer;
            padding: 32px 20px;
            border-radius: 20px;
            background: white;
            border: 2px solid #e2e8f0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .action-illustrations div::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .action-illustrations div:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: #667eea;
            box-shadow: 0 16px 48px rgba(102, 126, 234, 0.25);
        }

        .action-illustrations div:hover::before {
            opacity: 1;
        }

        .action-illustrations div:hover img {
            filter: brightness(0) invert(1);
        }

        .action-illustrations div:hover span {
            color: white;
        }

        .action-illustrations img {
            width: 80px;
            height: 80px;
            margin-bottom: 16px;
            object-fit: contain;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .action-illustrations span {
            display: block;
            font-weight: 600;
            color: #1e293b;
            font-size: 15px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        /* ===== FOOTER LINKS ===== */
        .footer-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 32px;
        }

        .footer-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 18px;
            border-radius: 16px;
            background: white;
            transition: all 0.3s ease;
            color: #1e293b;
            font-weight: 600;
            text-decoration: none;
            border: 2px solid #e2e8f0;
            font-size: 15px;
        }

        .footer-links a:hover {
            transform: translateY(-4px);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.3);
        }

        .footer-links a i {
            font-size: 18px;
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .sidebar {
                width: 240px;
            }

            .search-bar {
                width: 300px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .action-illustrations {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                left: -280px;
                height: 100vh;
                z-index: 999;
            }

            .sidebar.open {
                left: 0;
            }

            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .mobile-close {
                display: block;
            }

            .main-content {
                padding: 16px;
            }

            .header {
                flex-direction: column;
                gap: 16px;
                padding: 16px;
            }

            .search-bar {
                width: 100%;
            }

            .header-right {
                width: 100%;
                justify-content: space-between;
            }

            .user-profile span {
                display: none;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-number {
                font-size: 28px;
            }
        }

        @media (max-width: 640px) {
            .main-content {
                padding: 12px;
            }

            .header {
                padding: 12px;
            }

            .header-right {
                gap: 12px;
            }

            .notification-icon {
                width: 40px;
                height: 40px;
            }

            .user-avatar {
                width: 40px;
                height: 40px;
            }

            .section-title {
                font-size: 20px;
            }

            .action-illustrations {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .action-illustrations div {
                padding: 20px 12px;
            }

            .action-illustrations img {
                width: 60px;
                height: 60px;
            }

            .action-illustrations span {
                font-size: 13px;
            }

            .footer-links {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .mobile-menu-btn {
                width: 50px;
                height: 50px;
                font-size: 20px;
                bottom: 20px;
                right: 20px;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="container">

        <!-- OVERLAY -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- SIDEBAR -->
        <div class="sidebar" id="sidebar">
            <div class="logo">
                <button class="mobile-close" onclick="toggleSidebar()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="logo-icon">A</div>
                <div class="logo-text">Administrator</div>
            </div>
            <nav class="menu-nav">
                <a href="{{ route('admin.compte') }}" class="menu-item @yield('dashboardActive')">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('admin.users') }}" class="menu-item @yield('usersActive')">
                    <i class="fas fa-users"></i> Citoyens
                </a>
                <a href="{{ route('admin.valider') }}" class="menu-item @yield('validerActive')">
                    <i class="fas fa-check-circle"></i> Vérifications
                </a>
                <a href="{{ route('admin.actualite') }}" class="menu-item @yield('actualiteActive')">
                    <i class="fas fa-newspaper"></i> Actualités
                </a>
                <a href="{{ route('admin.alertes') }}" class="menu-item @yield('alertesActive')">
                    <i class="fas fa-bell"></i> Urgences
                </a>
                <a href="{{ route('admin.discussion.spaces') }}" class="menu-item @yield('discussionSpacesActive')">
                    <i class="fas fa-concierge-bell"></i> Espaces Discussion
                </a>
                <a href="{{ route('forum.index') }}" class="menu-item">
                    <i class="fas fa-comments"></i> Forum
                </a>
                <a href="{{ route('admin.login') }}" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <!-- MOBILE MENU BUTTON -->
            <button class="mobile-menu-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>

            <!-- HEADER -->
            <div class="header">
                {{-- <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Recherche...">
                </div> --}}
                {{-- <div class="header-right">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                    <div class="notification-icon">
                        <i class="fas fa-envelope"></i>
                        <span class="badge">5</span>
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar">
                            <img src="{{ asset($admin->photo_profil ?? 'medias/default.jpg') }}" alt="Profil">
                        </div>
                        <span>{{ $admin->nom ?? 'Admin' }}</span>
                    </div>
                </div> --}}
            </div>

            <!-- CONTENT SPECIFIQUE -->
            @yield('content')

        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }

        // Fermer la sidebar lors du redimensionnement sur desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            }
        });
    </script>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>
