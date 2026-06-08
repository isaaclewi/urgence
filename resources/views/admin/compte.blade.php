@extends('admin.master')

@section('title', 'Dashboard')
@section('dashboardActive', 'active')

@push('styles')
<style>
/* ===== DASHBOARD WELCOME ===== */
.welcome-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 40px;
    margin-bottom: 32px;
    color: white;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.welcome-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.welcome-section::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -5%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
}

.welcome-content {
    position: relative;
    z-index: 1;
}

.welcome-title {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.welcome-subtitle {
    font-size: 16px;
    opacity: 0.95;
    font-weight: 400;
}

/* ===== PROFIL CARD ===== */
.profile-card {
    background: white;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    gap: 32px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.profile-card::before {
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

.profile-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.15);
}

.profile-card:hover::before {
    transform: scaleX(1);
}

.profile-avatar {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    overflow: hidden;
    border: 5px solid #fff;
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
    position: relative;
    transition: all 0.3s ease;
}

.profile-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 32px rgba(102, 126, 234, 0.4);
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info {
    flex: 1;
}

.profile-name {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.profile-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.profile-details {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.profile-detail-item {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #64748b;
    font-size: 15px;
}

.profile-detail-item i {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-radius: 8px;
    color: #667eea;
    font-size: 14px;
}

.profile-detail-item strong {
    color: #1e293b;
    font-weight: 600;
}

/* ===== STATS OVERVIEW ===== */
.stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.stat-box {
    background: white;
    padding: 28px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.15);
}

.stat-box-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.stat-box-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.stat-box-icon.blue {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.15) 100%);
    color: #3b82f6;
}

.stat-box-icon.green {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.15) 100%);
    color: #10b981;
}

.stat-box-icon.purple {
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.15) 0%, rgba(124, 58, 237, 0.15) 100%);
    color: #8b5cf6;
}

.stat-box-icon.orange {
    background: linear-gradient(135deg, rgba(249, 115, 22, 0.15) 0%, rgba(234, 88, 12, 0.15) 100%);
    color: #f97316;
}

.stat-box-title {
    font-size: 14px;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-box-value {
    font-size: 32px;
    font-weight: 700;
    color: #1e293b;
    margin-top: 8px;
}

/* ===== SECTION TITLE ===== */
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

/* ===== ACTION CARDS ===== */
.action-illustrations {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.action-card {
    text-align: center;
    cursor: pointer;
    padding: 32px 24px;
    border-radius: 20px;
    background: white;
    border: 2px solid #e2e8f0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.action-card::before {
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

.action-card:hover {
    transform: translateY(-8px) scale(1.02);
    border-color: #667eea;
    box-shadow: 0 16px 48px rgba(102, 126, 234, 0.25);
}

.action-card:hover::before {
    opacity: 1;
}

.action-card:hover .action-image {
    filter: brightness(0) invert(1);
    transform: scale(1.1);
}

.action-card:hover .action-label {
    color: white;
}

.action-image {
    width: 90px;
    height: 90px;
    margin: 0 auto 20px;
    border-radius: 16px;
    object-fit: cover;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.action-label {
    display: block;
    font-weight: 600;
    color: #1e293b;
    font-size: 16px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

/* ===== FOOTER LINKS ===== */
.footer-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 32px;
}

.footer-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 20px;
    border-radius: 16px;
    background: white;
    transition: all 0.3s ease;
    color: #1e293b;
    font-weight: 600;
    text-decoration: none;
    border: 2px solid #e2e8f0;
    font-size: 15px;
    position: relative;
    overflow: hidden;
}

.footer-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.footer-link:hover {
    transform: translateY(-4px);
    border-color: #667eea;
    box-shadow: 0 12px 32px rgba(102, 126, 234, 0.3);
}

.footer-link:hover::before {
    transform: scaleX(1);
}

.footer-link:hover span,
.footer-link:hover i {
    position: relative;
    z-index: 1;
    color: white;
}

.footer-link i {
    font-size: 20px;
    transition: all 0.3s ease;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .welcome-section {
        padding: 28px;
    }
    
    .welcome-title {
        font-size: 24px;
    }
    
    .profile-card {
        flex-direction: column;
        text-align: center;
        padding: 28px;
    }
    
    .profile-name {
        flex-direction: column;
    }
    
    .profile-details {
        align-items: center;
    }
    
    .stats-overview {
        grid-template-columns: 1fr;
    }
    
    .action-illustrations {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .footer-links {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')

<!-- WELCOME SECTION -->
<div class="welcome-section">
    <div class="welcome-content">
        <h1 class="welcome-title">
            <i class="fas fa-hand-sparkles"></i>
            Bienvenue, {{ $admin->nom ?? 'Admin' }} !
        </h1>
        <p class="welcome-subtitle">
            Tableau de bord administrateur CongoAssist - Gérez efficacement votre plateforme
        </p>
    </div>
</div>

<!-- PROFIL CARD -->
<div class="profile-card">
    <div class="profile-avatar">
        <img src="{{ asset($admin->photo_profil ?? 'medias/default.jpg') }}" alt="Profil Administrateur">
    </div>
    <div class="profile-info">
        <h2 class="profile-name">
            {{ $admin->nom ?? 'Administrateur' }}
            <span class="profile-badge">
                <i class="fas fa-shield-alt"></i> Admin
            </span>
        </h2>
        <div class="profile-details">
            <div class="profile-detail-item">
                <i class="fas fa-envelope"></i>
                <span><strong>Email:</strong> {{ $admin->email ?? 'Non renseigné' }}</span>
            </div>
            <div class="profile-detail-item">
                <i class="fas fa-phone"></i>
                <span><strong>Téléphone:</strong> {{ $admin->telephone ?? 'Non renseigné' }}</span>
            </div>
            <div class="profile-detail-item">
                <i class="fas fa-calendar"></i>
                <span><strong>Membre depuis:</strong> {{ $admin->created_at ? $admin->created_at->format('d/m/Y') : 'N/A' }}</span>
            </div>
        </div>
    </div>
</div>

<!-- STATS OVERVIEW -->
<div class="stats-overview">
    <div class="stat-box">
        <div class="stat-box-header">
            <div class="stat-box-title">Citoyens</div>
            <div class="stat-box-icon blue">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-box-value">{{ $totalCitoyens ?? '0' }}</div>
    </div>
    
    <div class="stat-box">
        <div class="stat-box-header">
            <div class="stat-box-title">Services</div>
            <div class="stat-box-icon green">
                <i class="fas fa-concierge-bell"></i>
            </div>
        </div>
        <div class="stat-box-value">{{ $totalServices ?? '0' }}</div>
    </div>
    
    <div class="stat-box">
        <div class="stat-box-header">
            <div class="stat-box-title">Actualités</div>
            <div class="stat-box-icon purple">
                <i class="fas fa-newspaper"></i>
            </div>
        </div>
        <div class="stat-box-value">{{ $totalActualites ?? '0' }}</div>
    </div>
    
    <div class="stat-box">
        <div class="stat-box-header">
            <div class="stat-box-title">Urgences</div>
            <div class="stat-box-icon orange">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
        <div class="stat-box-value">{{ $totalUrgences ?? '0' }}</div>
    </div>
</div>

<!-- ACTIONS RAPIDES -->
<h2 class="section-title">Actions Rapides</h2>
<div class="action-illustrations">
    <div class="action-card" onclick="window.location='{{ route('admin.serviceCreate') }}'">
        <img src="{{ asset('medias/police.jpg') }}" alt="Services" class="action-image">
        <span class="action-label">
            <i class="fas fa-plus-circle"></i> Créer Service
        </span>
    </div>
    
    <div class="action-card" onclick="window.location='{{ route('admin.actualite') }}'">
        <img src="{{ asset('medias/icon4.jpg') }}" alt="Actualités" class="action-image">
        <span class="action-label">
            <i class="fas fa-newspaper"></i> Actualités
        </span>
    </div>
    
    <div class="action-card" onclick="window.location='{{ route('admin.alertes') }}'">
        <img src="{{ asset('medias/icon5.jpg') }}" alt="Urgences" class="action-image">
        <span class="action-label">
            <i class="fas fa-bell"></i> Urgences
        </span>
    </div>
    
    <div class="action-card" onclick="window.location='{{ route('admin.valider') }}'">
        <img src="{{ asset('medias/icon1.jpg') }}" alt="Validation" class="action-image">
        <span class="action-label">
            <i class="fas fa-check-circle"></i> Validation
        </span>
    </div>
</div>

<!-- FOOTER LINKS -->
<div class="footer-links">
    <a href="{{ route('admin.profil') }}" class="footer-link">
        <i class="fas fa-user-circle"></i>
        <span>Mon Profil</span>
    </a>
    <a href="{{ route('admin.services') }}" class="footer-link">
        <i class="fas fa-cogs"></i>
        <span>Nouveau Service</span>
    </a>
    <a href="{{ route('admin.login') }}" class="footer-link">
        <i class="fas fa-sign-out-alt"></i>
        <span>Déconnexion</span>
    </a>
</div>

@endsection