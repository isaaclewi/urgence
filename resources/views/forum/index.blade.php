@extends('admin.master')

@section('title', 'Espaces de discussion')

@section('content')

<div class="discussion-page">
    {{-- EN-TÊTE DE PAGE --}}
    <div class="page-header-box">
        <div class="d-flex align-items-center gap-3">
            <div class="header-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="flex-1">
                <h2 class="mb-1">Espaces de discussion</h2>
                <p class="text-muted mb-0">Rejoignez la conversation et échangez avec la communauté</p>
            </div>
            <div class="count-badge-large">
                {{ count($spaces) }} <span style="font-size: 14px;">{{ count($spaces) > 1 ? 'espaces' : 'espace' }}</span>
            </div>
        </div>
    </div>

    {{-- LISTE DES ESPACES --}}
    <div class="spaces-grid">
        @forelse($spaces as $space)
            <a href="{{ route('forum.group', $space->id) }}" class="space-card">
                <div class="space-card-inner">
                    
                    {{-- Avatar --}}
                    <div class="space-avatar {{ $space->type === 'public' ? 'avatar-public' : 'avatar-private' }}">
                        {{ $space->type === 'public' ? '🌐' : '🔒' }}
                    </div>

                    {{-- Content --}}
                    <div class="space-content">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h4 class="space-title">{{ $space->title }}</h4>
                            <i class="fas fa-arrow-right space-arrow"></i>
                        </div>
                        
                        <p class="space-description">
                            {{ \Illuminate\Support\Str::limit($space->description, 100) }}
                        </p>

                        {{-- Badges --}}
                        <div class="space-badges">
                            <span class="badge badge-type">
                                <i class="fas fa-{{ $space->type === 'public' ? 'globe' : 'lock' }}"></i>
                                {{ strtoupper($space->type) }}
                            </span>

                            @if($space->service)
                                <span class="badge badge-service">
                                    <i class="fas fa-building"></i>
                                    {{ $space->service->nom }}
                                </span>
                            @endif
                        </div>
                    </div>

                </div>
            </a>
        @empty
            <div class="empty-state-box">
                <div class="empty-icon">💬</div>
                <h3>Aucun espace disponible</h3>
                <p>Les espaces de discussion seront bientôt disponibles.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    /* Page Container */
    .discussion-page {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Header Box */
    .page-header-box {
        background: white;
        padding: 28px 32px;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        margin-bottom: 32px;
        border-left: 5px solid;
        border-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%) 1;
    }

    .header-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .page-header-box h2 {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .page-header-box p {
        color: #64748b;
        font-size: 15px;
    }

    .count-badge-large {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 16px 24px;
        border-radius: 16px;
        font-size: 32px;
        font-weight: 700;
        text-align: center;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        min-width: 120px;
    }

    .flex-1 {
        flex: 1;
    }

    .d-flex {
        display: flex;
    }

    .align-items-center {
        align-items: center;
    }

    .justify-content-between {
        justify-content: space-between;
    }

    .gap-3 {
        gap: 1rem;
    }

    /* Spaces Grid */
    .spaces-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 24px;
        animation: slideUp 0.6s ease;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Space Card */
    .space-card {
        display: block;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .space-card-inner {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 2px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        gap: 20px;
        align-items: start;
    }

    .space-card:hover .space-card-inner {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(102, 126, 234, 0.15);
        border-color: #667eea;
    }

    .space-card:hover .space-arrow {
        transform: translateX(5px);
        color: #667eea;
    }

    /* Avatar */
    .space-avatar {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        flex-shrink: 0;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .avatar-public {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .avatar-private {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .space-card:hover .space-avatar {
        transform: scale(1.1) rotate(5deg);
    }

    /* Content */
    .space-content {
        flex: 1;
        min-width: 0;
    }

    .space-title {
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        transition: color 0.3s ease;
    }

    .space-card:hover .space-title {
        color: #667eea;
    }

    .space-arrow {
        color: #94a3b8;
        font-size: 18px;
        transition: all 0.3s ease;
    }

    .space-description {
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
        margin: 8px 0 16px 0;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    /* Badges */
    .space-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .badge-type {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .badge-service {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        color: #667eea;
        border: 1px solid rgba(102, 126, 234, 0.3);
    }

    .space-card:hover .badge {
        transform: translateY(-2px);
    }

    /* Empty State */
    .empty-state-box {
        grid-column: 1 / -1;
        background: white;
        border-radius: 20px;
        padding: 80px 40px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    }

    .empty-icon {
        font-size: 80px;
        margin-bottom: 24px;
        opacity: 0.3;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .empty-state-box h3 {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 12px;
    }

    .empty-state-box p {
        color: #64748b;
        font-size: 16px;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .spaces-grid {
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .spaces-grid {
            grid-template-columns: 1fr;
        }

        .page-header-box {
            padding: 20px;
        }

        .page-header-box h2 {
            font-size: 22px;
        }

        .header-icon {
            width: 50px;
            height: 50px;
            font-size: 24px;
        }

        .count-badge-large {
            font-size: 24px;
            padding: 12px 16px;
            min-width: 100px;
        }

        .space-card-inner {
            flex-direction: column;
            text-align: center;
        }

        .space-avatar {
            margin: 0 auto;
        }

        .space-badges {
            justify-content: center;
        }
    }

    /* Utilities */
    .mb-0 { margin-bottom: 0; }
    .mb-1 { margin-bottom: 0.25rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .text-muted { color: #64748b; }
</style>

@endsection