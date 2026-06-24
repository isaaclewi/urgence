@extends('admin.master')

@section('title', 'Alertes en Temps Réel')
@section('alertesActive', 'active')

@push('styles')
<style>
/* ===== PAGE HEADER ===== */
.alerts-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    background: white;
    padding: 24px 28px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    position: relative;
    overflow: hidden;
}

.alerts-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
}

.page-title-section {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-title i {
    color: #667eea;
    animation: ringBell 2s infinite;
}

@keyframes ringBell {
    0%, 100% { transform: rotate(0deg); }
    10%, 30% { transform: rotate(-10deg); }
    20%, 40% { transform: rotate(10deg); }
    50% { transform: rotate(0deg); }
}

.page-subtitle {
    color: #64748b;
    font-size: 15px;
}

.refresh-btn {
    padding: 12px 24px;
    border-radius: 12px;
    border: none;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.refresh-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.refresh-btn i {
    animation: rotate 0.6s ease;
}

.refresh-btn:active i {
    animation: rotate 0.6s ease;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* ===== STATS CARDS ===== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    transition: width 0.3s ease;
}

.stat-card.pending::before {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-card.progress::before {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.stat-card.resolved::before {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-card.total::before {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.15);
}

.stat-card:hover::before {
    width: 100%;
    opacity: 0.05;
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.stat-card.pending .stat-icon {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.15) 100%);
    color: #f59e0b;
}

.stat-card.progress .stat-icon {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.15) 100%);
    color: #3b82f6;
}

.stat-card.resolved .stat-icon {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.15) 100%);
    color: #10b981;
}

.stat-card.total .stat-icon {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
    color: #667eea;
}

.stat-label {
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #1e293b;
    margin-top: 8px;
}

/* ===== AUTO REFRESH INDICATOR ===== */
.auto-refresh-indicator {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
    border-radius: 10px;
    font-size: 13px;
    color: #059669;
    font-weight: 600;
    margin-bottom: 24px;
}

.pulse-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.2);
    }
}

/* ===== TABLE CONTAINER ===== */
.table-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    transition: box-shadow 0.3s ease;
}

.table-container:hover {
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.1);
}

/* ===== TABLE ===== */
table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

th {
    color: white;
    padding: 18px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

td {
    padding: 16px;
    border-bottom: 1px solid #e2e8f0;
    color: #475569;
    font-size: 14px;
}

tbody tr {
    transition: all 0.3s ease;
}

tbody tr:hover {
    background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    transform: translateX(4px);
}

tbody tr:last-child td {
    border-bottom: none;
}

/* ===== ALERT TYPE BADGE ===== */
.type-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.type-incendie {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

.type-accident {
    background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
    color: #9a3412;
}

.type-securite {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #78350f;
}

.type-sante {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e3a8a;
}

.type-default {
    background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
    color: #374151;
}

/* ===== STATUS BADGE ===== */
.status-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
    text-transform: capitalize;
}

.status-pending {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #78350f;
}

.status-progress {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e3a8a;
}

.status-resolved {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
}

/* ===== LOCATION BADGE ===== */
.location-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.location-info i {
    color: #667eea;
}

/* ===== CITIZEN INFO ===== */
.citizen-info {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}

.citizen-info i {
    color: #64748b;
}

/* ===== DATE INFO ===== */
.date-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.date-text {
    font-weight: 600;
    color: #1e293b;
}

.time-text {
    font-size: 12px;
    color: #64748b;
}

/* ===== LOADING ANIMATION ===== */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-overlay.active {
    display: flex;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f4f6;
    border-top: 5px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ===== RESPONSIVE ===== */
@media (max-width: 1024px) {
    .alerts-header {
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    table {
        font-size: 13px;
    }

    th, td {
        padding: 12px 10px;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        min-width: 1000px;
    }
}
</style>
@endpush

@section('content')

<!-- PAGE HEADER -->
<div class="alerts-header">
    <div class="page-title-section">
        <h1 class="page-title">
            <i class="fas fa-bell"></i>
            Alertes en Temps Réel
        </h1>
        <p class="page-subtitle">Surveillance et gestion des alertes citoyennes</p>
    </div>
    <button class="refresh-btn" onclick="refreshAlertes()">
        <i class="fas fa-sync-alt"></i>
        Rafraîchir
    </button>
</div>

<!-- STATS CARDS -->
<div class="stats-grid">
    <div class="stat-card total">
        <div class="stat-header">
            <div class="stat-label">Total Alertes</div>
            <div class="stat-icon">
                <i class="fas fa-bell"></i>
            </div>
        </div>
        <div class="stat-value">{{ $alertes->count() }}</div>
    </div>

    <div class="stat-card pending">
        <div class="stat-header">
            <div class="stat-label">En Attente</div>
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-value">{{ $alertes->where('statut', 'En attente')->count() }}</div>
    </div>

    <div class="stat-card progress">
        <div class="stat-header">
            <div class="stat-label">En Cours</div>
            <div class="stat-icon">
                <i class="fas fa-spinner"></i>
            </div>
        </div>
        <div class="stat-value">{{ $alertes->where('statut', 'En cours')->count() }}</div>
    </div>

    <div class="stat-card resolved">
        <div class="stat-header">
            <div class="stat-label">Résolues</div>
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value">{{ $alertes->where('statut', 'Résolue')->count() }}</div>
    </div>
</div>

<!-- AUTO REFRESH INDICATOR -->
<div class="auto-refresh-indicator">
    <span class="pulse-dot"></span>
    <span>Rafraîchissement automatique toutes les 20 secondes</span>
</div>

<!-- TABLE CONTAINER -->
<div class="table-container">
    <table id="tableAlertes">
        <thead>
            <tr>
                <th><i class="fas fa-heading"></i> Titre</th>
                <th><i class="fas fa-tag"></i> Type</th>
                <th><i class="fas fa-map-marker-alt"></i> Localisation</th>
                <th><i class="fas fa-user"></i> Citoyen</th>
                <th><i class="fas fa-calendar-alt"></i> Date / Heure</th>
                <th><i class="fas fa-info-circle"></i> Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alertes as $alerte)
            <tr>
                <td>
                    <strong style="color: #1e293b;">{{ $alerte->titre }}</strong>
                </td>
                <td>
                    @php
                        $typeClass = 'type-default';
                        $type = strtolower($alerte->type_alerte);
                        if (str_contains($type, 'incendie')) $typeClass = 'type-incendie';
                        elseif (str_contains($type, 'accident')) $typeClass = 'type-accident';
                        elseif (str_contains($type, 'sécurité') || str_contains($type, 'securite')) $typeClass = 'type-securite';
                        elseif (str_contains($type, 'santé') || str_contains($type, 'sante')) $typeClass = 'type-sante';
                    @endphp
                    <span class="type-badge {{ $typeClass }}">
                        {{ $alerte->type_alerte }}
                    </span>
                </td>
                <td>
                    <div class="location-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $alerte->localisation }}</span>
                    </div>
                </td>
                <td>
                    <div class="citizen-info">
                        <i class="fas fa-user-circle"></i>
                        <span>
                            {{ optional($alerte->citoyen)->nom
                                ? optional($alerte->citoyen)->nom . ' ' . optional($alerte->citoyen)->prenom
                                : 'Anonyme' }}
                        </span>
                    </div>
                </td>
                <td>
                    <div class="date-info">
                        <span class="date-text">
                            {{ $alerte->created_at ? $alerte->created_at->format('d/m/Y') : 'N/A' }}
                        </span>
                        <span class="time-text">
                            {{ $alerte->created_at ? $alerte->created_at->format('H:i') : '' }}
                        </span>
                    </div>
                </td>
                <td>
                    @php
                        $statut = $alerte->statut;
                        $statusClass = 'status-pending';
                        if ($statut === 'En cours') $statusClass = 'status-progress';
                        elseif ($statut === 'Résolue') $statusClass = 'status-resolved';
                    @endphp
                    <span class="status-badge {{ $statusClass }}">
                        {{ $statut ?? 'Non défini' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- LOADING OVERLAY -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

@endsection

@push('scripts')
<script>
// Rafraîchissement manuel des alertes
function refreshAlertes() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    const refreshBtn = document.querySelector('.refresh-btn i');

    // Afficher le loading
    loadingOverlay.classList.add('active');
    refreshBtn.style.animation = 'rotate 0.6s ease';

   fetch('{{ route("admin.alertes") }}')
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTbody = doc.querySelector('#tableAlertes tbody');

            if (newTbody) {
                document.querySelector('#tableAlertes tbody').innerHTML = newTbody.innerHTML;

                // Mise à jour des statistiques
                updateStats(doc);
            }

            // Masquer le loading
            setTimeout(() => {
                loadingOverlay.classList.remove('active');
            }, 300);
        })
        .catch(err => {
            console.error("Erreur lors du rafraîchissement :", err);
            loadingOverlay.classList.remove('active');
            alert('Erreur lors du rafraîchissement des alertes');
        });
}

// Mise à jour des statistiques
function updateStats(doc) {
    const statsCards = doc.querySelectorAll('.stat-value');
    const currentStatsCards = document.querySelectorAll('.stat-value');

    statsCards.forEach((stat, index) => {
        if (currentStatsCards[index]) {
            currentStatsCards[index].textContent = stat.textContent;
        }
    });
}

// Rafraîchissement automatique toutes les 20 secondes
setInterval(refreshAlertes, 20000);

// Notification de nouvelle alerte (optionnel)
let previousCount = {{ $alertes->count() }};

function checkNewAlerts() {
    fetch('{{ route("admin.alertes") }}')
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const totalValue = doc.querySelector('.stat-card.total .stat-value');

            if (totalValue) {
                const currentCount = parseInt(totalValue.textContent);
                if (currentCount > previousCount) {
                    // Nouvelle alerte détectée
                    showNotification('Nouvelle alerte reçue !');
                    previousCount = currentCount;
                }
            }
        });
}

// Afficher une notification
function showNotification(message) {
    if ("Notification" in window && Notification.permission === "granted") {
        new Notification("CongoAssist - Alertes", {
            body: message,
            icon: "{{ asset('medias/Clogo.jpg') }}"
        });
    }
}

// Demander la permission pour les notifications
if ("Notification" in window && Notification.permission === "default") {
    Notification.requestPermission();
}

// Vérifier les nouvelles alertes toutes les 20 secondes
setInterval(checkNewAlerts, 20000);
</script>
@endpush
