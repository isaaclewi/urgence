@extends('services.master')

@section('title', $space->title)

@section('page-title', $space->title)
@section('page-subtitle', $space->description ?? 'Discussion de groupe')


@section('sidebar')
    <div class="space-y-1">
        <a href="{{ route('services.compte') }}"
            class="sidebar-link active flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
            <i data-feather="home" class="w-5 h-5"></i>
            <span>Tableau de bord</span>
        </a>
        <a href="{{ route('services.urgenceSignalee') }}"
            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
            <i data-feather="bell" class="w-5 h-5"></i>
            <span>Urgences signalées</span>
            @if (($stats['urgences_en_cours'] ?? 0) > 0)
                <span
                    class="ml-auto px-2 py-1 text-xs font-bold text-white accent-bg rounded-full">{{ $stats['urgences_en_cours'] }}</span>
            @endif
        </a>
        <a href="{{ route('services.citoyens') }}"
            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
            <i data-feather="users" class="w-5 h-5"></i>
            <span>Citoyens</span>
        </a>
        <a href="{{ route('services.forum.index') }}"
            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
            <i data-feather="message-square" class="w-5 h-5"></i>
            <span>Forum</span>
        </a>
        <a href="{{ route('services.actualite') }}"
            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
            <i data-feather="newspaper" class="w-5 h-5"></i>
            <span>Actualités</span>
        </a>
        <a href="{{ route('services.profil') }}"
            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
            <i data-feather="settings" class="w-5 h-5"></i>
            <span>Gestion interne</span>
        </a>

        @if (($service->role ?? '') === 'hopital')
            <div class="pt-4 mt-4 border-t border-gray-200">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Services Médicaux</p>
                <a href="{{ route('services.vaccinationIndex') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
                    <i data-feather="shield" class="w-5 h-5"></i>
                    <span>Vaccinations</span>
                </a>
                <a href="{{ route('services.citoyensBilan') }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 font-semibold">
                    <i data-feather="activity" class="w-5 h-5"></i>
                    <span>Bilan Santé</span>
                </a>
            </div>
        @endif

        <div class="pt-4 mt-4 border-t border-gray-200">
            <a href="{{ route('services.logout') }}"
                class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 font-semibold hover:bg-red-50">
                <i data-feather="log-out" class="w-5 h-5"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </div>
@endsection
@section('content')
    <div class="space-y-6">
        {{-- Header du groupe --}}
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 accent-border animate-fade-in">
            <div class="flex items-center gap-4">
                {{-- Avatar --}}
                <div
                    class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl {{ $space->type === 'public' ? 'bg-gradient-to-br from-green-400 to-green-600' : 'bg-gradient-to-br from-blue-400 to-blue-600' }} shadow-lg flex-shrink-0">
                    {{ $space->type === 'public' ? '🌐' : '🔒' }}
                </div>

                {{-- Info groupe --}}
                <div class="flex-1 min-w-0">
                    <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $space->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $space->description ?? 'Discussion de groupe' }}</p>

                    <div class="flex items-center gap-2 mt-2">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-semibold">
                            <i data-feather="{{ $space->type === 'public' ? 'globe' : 'lock' }}" class="w-3 h-3"></i>
                            {{ strtoupper($space->type) }}
                        </span>

                        @if ($space->service)
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold">
                                <i data-feather="briefcase" class="w-3 h-3"></i>
                                {{ $space->service->nom }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Bouton retour --}}
                <a href="{{ route('services.forum.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition flex items-center gap-2">
                    <i data-feather="arrow-left" class="w-4 h-4"></i>
                    Retour
                </a>
            </div>
        </div>

        {{-- Zone de messages --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden animate-slide-in">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i data-feather="message-circle" class="w-5 h-5 accent-text"></i>
                    Messages
                </h3>
            </div>

            <div id="messagesContainer" class="p-6 space-y-4 overflow-y-auto"
                style="max-height: 500px; background: linear-gradient(to bottom, #f9fafb 0%, #ffffff 100%);">
                @forelse($messages as $msg)
                    <div
                        class="flex {{ $msg->sender_type === 'service' ? 'justify-start' : 'justify-end' }} animate-fade-in">
                        <div class="max-w-[70%] {{ $msg->sender_type === 'service' ? 'mr-auto' : 'ml-auto' }}">
                            {{-- Nom de l'expéditeur --}}
                            <div
                                class="text-xs font-semibold mb-1 {{ $msg->sender_type === 'service' ? 'text-orange-600' : 'text-green-600' }} flex items-center gap-1">
                                <i data-feather="{{ $msg->sender_type === 'service' ? 'briefcase' : 'user' }}"
                                    class="w-3 h-3"></i>
                                {{ ucfirst($msg->sender_type) }}
                            </div>

                            {{-- Bulle de message --}}
                            <div
                                class="rounded-2xl {{ $msg->sender_type === 'service' ? 'rounded-tl-none bg-white border border-gray-200' : 'rounded-tr-none bg-gradient-to-br from-green-400 to-green-500 text-white' }} p-4 shadow-md">
                                @if ($msg->message_type === 'texte')
                                    <p
                                        class="text-sm leading-relaxed {{ $msg->sender_type === 'service' ? 'text-gray-900' : 'text-white' }}">
                                        {{ $msg->message }}
                                    </p>
                                @else
                                    <a href="{{ asset($msg->file_path) }}" target="_blank"
                                        class="flex items-center gap-2 p-3 bg-gray-50 {{ $msg->sender_type === 'citoyen' ? 'bg-white/20' : '' }} rounded-xl hover:bg-gray-100 transition">
                                        <i data-feather="paperclip"
                                            class="w-5 h-5 {{ $msg->sender_type === 'service' ? 'text-gray-600' : 'text-white' }}"></i>
                                        <span
                                            class="text-sm font-medium {{ $msg->sender_type === 'service' ? 'text-gray-900' : 'text-white' }}">{{ $msg->file_name }}</span>
                                    </a>
                                @endif

                                {{-- Heure --}}
                                <div
                                    class="text-xs {{ $msg->sender_type === 'service' ? 'text-gray-500' : 'text-white/80' }} mt-2 text-right flex items-center justify-end gap-1">
                                    <i data-feather="clock" class="w-3 h-3"></i>
                                    {{ $msg->created_at->format('H:i') }}
                                    {{-- Suppression adaptée à la session service_id --}}
                                    @if ($msg->sender_type === 'service' && $msg->sender_id == session('service_id'))
                                        <form action="{{ route('services.forum.message.delete', $msg->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                style="margin-left: 8px; color: #ef4444; font-size: 12px; cursor: pointer;">🗑</button>
                                        </form>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-feather="message-square" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 font-semibold mb-2">Aucun message pour l'instant</p>
                        <p class="text-gray-400 text-sm">Soyez le premier à écrire dans cet espace !</p>
                    </div>
                @endforelse
            </div>

            {{-- Zone d'écriture --}}
            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <form method="POST" action="{{ route('services.forum.group.send', $space->id) }}" class="flex gap-3">
                    @csrf

                    <div class="flex-1">
                        <textarea name="message" rows="1"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-100 outline-none transition resize-none text-sm"
                            placeholder="Écrire un message..." style="max-height: 120px;"
                            onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();this.form.submit();}"
                            oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"></textarea>
                    </div>

                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-br from-green-400 to-green-500 hover:from-green-500 hover:to-green-600 text-white rounded-xl font-semibold transition shadow-lg hover:shadow-xl flex items-center gap-2">
                        <i data-feather="send" class="w-5 h-5"></i>
                        <span class="hidden sm:inline">Envoyer</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Stats rapides --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 animate-fade-in">
            <div class="bg-white rounded-2xl p-4 shadow-lg border-l-4 border-blue-500">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 p-3 rounded-xl">
                        <i data-feather="message-circle" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ count($messages) }}</p>
                        <p class="text-xs text-gray-600">Messages</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 shadow-lg border-l-4 border-purple-500">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-100 p-3 rounded-xl">
                        <i data-feather="users" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $messages->unique('sender_id')->count() }}</p>
                        <p class="text-xs text-gray-600">Participants</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 shadow-lg border-l-4 border-green-500">
                <div class="flex items-center gap-3">
                    <div class="bg-green-100 p-3 rounded-xl">
                        <i data-feather="activity" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">Actif</p>
                        <p class="text-xs text-gray-600">Statut</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Initialiser les icônes Feather
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // Auto-scroll vers le bas au chargement
            document.addEventListener('DOMContentLoaded', function() {
                const messagesContainer = document.getElementById('messagesContainer');
                if (messagesContainer) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            });

            // Focus automatique sur le textarea
            document.addEventListener('DOMContentLoaded', function() {
                const textarea = document.querySelector('textarea[name="message"]');
                if (textarea && window.innerWidth > 768) {
                    textarea.focus();
                }
            });

            // Smooth scroll pour les nouveaux messages
            const observer = new MutationObserver(function() {
                const messagesContainer = document.getElementById('messagesContainer');
                if (messagesContainer) {
                    messagesContainer.scrollTo({
                        top: messagesContainer.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            });

            // Observer les changements dans le container de messages
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                observer.observe(messagesContainer, {
                    childList: true,
                    subtree: true
                });
            }
        </script>
    @endpush
@endsection
