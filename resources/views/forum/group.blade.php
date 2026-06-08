@extends('admin.master')

@section('title', $space->title)

@section('content')

    <div style="max-width: 1200px; margin: 0 auto;">

        {{-- Header du groupe --}}
        <div
            style="background: white; border-radius: 20px; padding: 28px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06); animation: fadeIn 0.5s ease;">
            <div style="display: flex; align-items: center; gap: 20px;">
                {{-- Bouton retour --}}
                <a href="{{ route('forum.index') }}"
                    style="display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: linear-gradient(90deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; transition: all 0.3s ease; text-decoration: none; cursor: pointer;">
                    <i class="fas fa-arrow-left" style="color: #667eea; font-size: 18px;"></i>
                </a>

                {{-- Avatar --}}
                <div
                    style="width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 32px; background: {{ $space->type === 'public' ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)' }}; box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3); flex-shrink: 0;">
                    {{ $space->type === 'public' ? '🌐' : '🔒' }}
                </div>

                {{-- Info groupe --}}
                <div style="flex: 1; min-width: 0;">
                    <h3
                        style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $space->title }}
                    </h3>
                    <p style="font-size: 14px; color: #64748b; margin: 0 0 12px 0;">
                        {{ $space->description ?? 'Discussion de groupe' }}
                    </p>

                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <span
                            style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1)); color: #059669; border-radius: 12px; font-size: 12px; font-weight: 700;">
                            <i class="fas fa-{{ $space->type === 'public' ? 'globe' : 'lock' }}"></i>
                            {{ strtoupper($space->type) }}
                        </span>

                        @if ($space->service)
                            <span
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1)); color: #667eea; border-radius: 12px; font-size: 12px; font-weight: 700;">
                                <i class="fas fa-building"></i>
                                {{ $space->service->nom }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Zone de messages --}}
        <div
            style="background: white; border-radius: 20px; overflow: hidden; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06); animation: slideIn 0.5s ease;">

            {{-- Header messages --}}
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px 28px;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <h3
                        style="margin: 0; font-size: 18px; font-weight: 700; color: white; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-comments"></i>
                        Discussion
                    </h3>
                    <span
                        style="background: rgba(255, 255, 255, 0.2); color: white; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700;">
                        {{ count($messages) }} message{{ count($messages) > 1 ? 's' : '' }}
                    </span>
                </div>
            </div>

            {{-- Container des messages --}}
            <div id="messagesContainer"
                style="padding: 28px; max-height: 500px; overflow-y: auto; background: linear-gradient(to bottom, #f8fafc 0%, #ffffff 100%);">
                @forelse($messages as $msg)
                    <div
                        style="margin-bottom: 16px; display: flex; justify-content: {{ $msg->sender_type === 'agent' ? 'flex-end' : 'flex-start' }}; animation: messageSlide 0.3s ease;">
                        <div
                            style="max-width: 70%; {{ $msg->sender_type === 'agent' ? 'margin-left: auto;' : 'margin-right: auto;' }}">

                            {{-- Nom de l'expéditeur --}}
                            <div
                                style="font-size: 12px; font-weight: 700; margin-bottom: 6px; color: {{ $msg->sender_type === 'agent' ? '#10b981' : '#f59e0b' }}; {{ $msg->sender_type === 'agent' ? 'text-align: right;' : '' }} display: flex; align-items: center; gap: 6px; {{ $msg->sender_type === 'agent' ? 'justify-content: flex-end;' : '' }}">
                                <i class="fas fa-{{ $msg->sender_type === 'agent' ? 'user-circle' : 'building' }}"></i>
                                {{ ucfirst($msg->sender_type) }}
                            </div>

                            {{-- Bulle de message --}}
                            <div
                                style="padding: 16px 20px; border-radius: 20px; {{ $msg->sender_type === 'agent' ? 'border-top-right-radius: 4px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;' : 'border-top-left-radius: 4px; background: white; border: 2px solid #e2e8f0; color: #1e293b;' }} box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); position: relative;">

                                @if ($msg->message_type === 'texte')
                                    <p style="margin: 0; font-size: 15px; line-height: 1.6; word-wrap: break-word;">
                                        {{ $msg->message }}
                                    </p>
                                @else
                                    <a href="{{ asset($msg->file_path) }}" target="_blank"
                                        style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: {{ $msg->sender_type === 'agent' ? 'rgba(255, 255, 255, 0.2)' : '#f1f5f9' }}; border-radius: 12px; text-decoration: none; transition: all 0.3s ease;">
                                        <i class="fas fa-paperclip"
                                            style="font-size: 20px; color: {{ $msg->sender_type === 'agent' ? 'white' : '#667eea' }};"></i>
                                        <span
                                            style="font-size: 14px; font-weight: 600; color: {{ $msg->sender_type === 'agent' ? 'white' : '#1e293b' }}; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $msg->file_name }}
                                        </span>
                                    </a>
                                @endif

                                {{-- Heure --}}
                                <div
                                    style="text-align: right; font-size: 11px; color: {{ $msg->sender_type === 'agent' ? 'rgba(255, 255, 255, 0.8)' : '#94a3b8' }}; margin-top: 8px; display: flex; align-items: center; justify-content: flex-end; gap: 4px;">
                                    <i class="fas fa-clock"></i>
                                    {{ $msg->created_at->format('H:i') }}
                                    @if ($msg->sender_type === 'agent' && $msg->sender_id == session('admin_id'))
                                        <form action="{{ route('services.forum.message.delete', $msg->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                style="margin-left: 8px; color: #ef4444; font-size: 12px; cursor: pointer;">🗑</button>
                                        </form>
                                    @endif
                                </div>
                                {{-- Bouton supprimer si c'est le service connecté --}}

                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 80px 20px;">
                        <div
                            style="width: 100px; height: 100px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="fas fa-comments" style="font-size: 48px; color: #cbd5e1;"></i>
                        </div>
                        <p style="color: #64748b; font-size: 18px; font-weight: 700; margin: 0 0 8px 0;">
                            Aucun message pour l'instant
                        </p>
                        <p style="color: #94a3b8; font-size: 14px; margin: 0;">
                            Soyez le premier à écrire dans cet espace !
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Zone d'écriture --}}
            <div style="padding: 24px 28px; background: #f8fafc; border-top: 2px solid #e2e8f0;">
                <form method="POST" action="{{ route('forum.group.send', $space->id) }}"
                    style="display: flex; gap: 12px;">
                    @csrf

                    <div style="flex: 1;">
                        <textarea name="message" rows="1"
                            style="width: 100%; padding: 14px 20px; border: 2px solid #e2e8f0; border-radius: 16px; font-size: 15px; font-family: inherit; resize: none; max-height: 120px; overflow-y: auto; transition: all 0.3s ease; outline: none;"
                            placeholder="Écrire un message..."
                            onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();this.form.submit();}"
                            oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"
                            onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 4px rgba(102, 126, 234, 0.1)';"
                            onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"></textarea>
                    </div>

                    <button type="submit"
                        style="width: 56px; height: 56px; border: none; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; transition: all 0.3s ease; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- Stats rapides --}}
        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; animation: fadeIn 0.5s ease 0.2s both;">
            <div
                style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06); border-left: 4px solid #3b82f6;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div
                        style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.1)); padding: 16px; border-radius: 12px;">
                        <i class="fas fa-comments" style="font-size: 24px; color: #3b82f6;"></i>
                    </div>
                    <div>
                        <p style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;">{{ count($messages) }}</p>
                        <p style="font-size: 12px; color: #64748b; margin: 4px 0 0 0; font-weight: 600;">Messages</p>
                    </div>
                </div>
            </div>

            <div
                style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06); border-left: 4px solid #8b5cf6;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div
                        style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(124, 58, 237, 0.1)); padding: 16px; border-radius: 12px;">
                        <i class="fas fa-users" style="font-size: 24px; color: #8b5cf6;"></i>
                    </div>
                    <div>
                        <p style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;">
                            {{ $messages->unique('sender_id')->count() }}</p>
                        <p style="font-size: 12px; color: #64748b; margin: 4px 0 0 0; font-weight: 600;">Participants</p>
                    </div>
                </div>
            </div>

            <div
                style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06); border-left: 4px solid #10b981;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div
                        style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1)); padding: 16px; border-radius: 12px;">
                        <i class="fas fa-check-circle" style="font-size: 24px; color: #10b981;"></i>
                    </div>
                    <div>
                        <p style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;">Actif</p>
                        <p style="font-size: 12px; color: #64748b; margin: 4px 0 0 0; font-weight: 600;">Statut du groupe
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Scrollbar personnalisée */
        #messagesContainer::-webkit-scrollbar {
            width: 8px;
        }

        #messagesContainer::-webkit-scrollbar-track {
            background: transparent;
        }

        #messagesContainer::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        #messagesContainer::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Hover effects */
        a[style*="background: linear-gradient(90deg, rgba(102, 126, 234"]:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%) !important;
            transform: translateX(-2px);
        }

        button[style*="background: linear-gradient(135deg, #10b981"]:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 28px rgba(16, 185, 129, 0.4) !important;
        }

        button[style*="background: linear-gradient(135deg, #10b981"]:active {
            transform: scale(0.95);
        }

        /* Responsive */
        @media (max-width: 768px) {
            #messagesContainer {
                max-height: 400px !important;
                padding: 20px !important;
            }

            div[style*="max-width: 70%"] {
                max-width: 85% !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-scroll vers le bas au chargement
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            // Focus automatique sur le textarea (uniquement desktop)
            const textarea = document.querySelector('textarea[name="message"]');
            if (textarea && window.innerWidth > 768) {
                textarea.focus();
            }

            // Observer pour auto-scroll lors de nouveaux messages
            const observer = new MutationObserver(function() {
                if (messagesContainer) {
                    messagesContainer.scrollTo({
                        top: messagesContainer.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            });

            if (messagesContainer) {
                observer.observe(messagesContainer, {
                    childList: true,
                    subtree: true
                });
            }
        });
    </script>
@endsection
