@extends($layout)

@section('title', $space->title)
@section('page-title', 'Messagerie')

@section('content')
@include('messagerie._styles')
@php
    $nbServices = $membres->where('status', 'active')->count();
@endphp

<div class="mg-app" id="mg"
     data-poll="{{ route($base . 'poll', $space->id) }}"
     data-send="{{ route($base . 'send', $space->id) }}"
     data-item="{{ route($base . 'update', [$space->id, '__ID__']) }}"
     data-index="{{ route($base . 'index') }}"
     data-last="{{ $dernierId }}" data-since="{{ $maintenant }}" data-more="{{ $aPlus ? 1 : 0 }}"
     data-canpost="{{ $canPost ? 1 : 0 }}"
     data-today="{{ now()->format('Y-m-d') }}" data-yesterday="{{ now()->subDay()->format('Y-m-d') }}"
     data-csrf="{{ csrf_token() }}" data-maxmb="{{ $actor->isCitoyen() ? 5 : 10 }}">

    <section class="mg-chat">
        <header class="mg-top">
            <a class="mg-btn" href="{{ route($base . 'index') }}" aria-label="Retour à la liste">←</a>
            <div class="mg-av lg tone-{{ $space->type === 'public' ? 'hopital' : 'admin' }}">{{ mb_strtoupper(mb_substr($space->title, 0, 1)) }}</div>
            <div class="mg-top-t">
                <b>{{ $space->title }}</b>
                <small>
                    {{ $space->type === 'public' ? 'Groupe public' : 'Groupe privé' }}
                    · {{ $nbServices }} service{{ $nbServices > 1 ? 's' : '' }}
                    @unless($space->is_active) · archivé @endunless
                </small>
            </div>
            @if($actor->isAdmin())
                <a class="mg-btn" href="{{ route('admin.messagerie.membres', $space->id) }}">Gérer l'accès</a>
            @endif
            <button type="button" class="mg-btn" id="mg-toggle-side">Infos</button>
        </header>

        <div class="mg-scroll" id="mg-scroll">
            @if($aPlus)<button type="button" class="mg-btn mg-older" id="mg-older">Voir les messages précédents</button>@endif
            <div id="mg-list">
                @foreach($messages as $m)
                    @include('messagerie._message', ['m' => $m])
                @endforeach
            </div>
            <div class="mg-empty" id="mg-empty" @if($messages->isNotEmpty()) hidden @endif>
                Aucun message pour le moment.@if($canPost) Écrivez le premier.@endif
            </div>
        </div>

        <button type="button" class="mg-btn primary mg-newpill" id="mg-pill" hidden>↓ Nouveaux messages</button>

        <footer class="mg-compose" id="mg-compose">
            @if($canPost)
                <div class="mg-reply" id="mg-reply" hidden>
                    <div><b id="mg-reply-name"></b><span id="mg-reply-text"></span></div>
                    <button type="button" class="mg-btn" id="mg-reply-x" aria-label="Annuler la réponse">✕</button>
                </div>
                <div class="mg-chip" id="mg-chip" hidden>
                    <span id="mg-chip-name"></span>
                    <button type="button" class="mg-btn" id="mg-chip-x" aria-label="Retirer le fichier">✕</button>
                </div>
                <form class="mg-form" id="mg-form" autocomplete="off">
                    <input type="file" id="mg-file" hidden
                           accept="{{ $actor->isCitoyen() ? 'image/*' : 'image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.mp3,.m4a,.ogg,.wav,.mp4' }}">
                    <button class="mg-btn" type="button" id="mg-attach" aria-label="Joindre un fichier">Joindre</button>
                    <textarea id="mg-input" rows="1" maxlength="2000" placeholder="Écrire un message…" aria-label="Message"></textarea>
                    <button class="mg-btn primary" type="submit" id="mg-send">Envoyer</button>
                </form>
            @else
                <div class="mg-readonly">
                    @if(! $space->is_active) Ce groupe est archivé : lecture seule.
                    @else Vous pouvez lire ce groupe mais pas y écrire. @endif
                </div>
            @endif
        </footer>
    </section>

    <aside class="mg-side" id="mg-side">
        <button type="button" class="mg-btn mg-close-side" id="mg-close-side">Fermer</button>
        <h3>À propos</h3>
        <p>{{ $space->description ?: 'Groupe de coordination.' }}</p>

        <h3>Qui peut écrire</h3>
        <p>Administration : toujours.</p>
        <p>Services membres : {{ $space->services_can_post ? 'oui' : 'modérateurs seulement' }}.</p>
        <p>Citoyens : {{ $space->type === 'public' ? ($space->citizens_can_post ? 'peuvent lire et écrire' : 'lecture seule') : 'pas d\'accès' }}.</p>

        <h3>Services autorisés ({{ $nbServices }})</h3>
        @forelse($membres as $mb)
            @php
                $sv = $services->get($mb->member_id);
                [$lib, $ton] = \App\Support\Messagerie\Presenter::roleService($sv->role ?? null);
                $nom = $sv->nom ?? 'Service';
            @endphp
            <div class="mg-who tone-{{ $ton }}">
                <div class="mg-av">{{ mb_strtoupper(mb_substr($nom, 0, 2)) }}</div>
                <div>
                    <b>{{ $nom }}</b>
                    <span class="mg-badge">{{ $lib }}</span>
                    @if($mb->role === 'moderator')<span class="mg-tag ok">Modérateur</span>@endif
                    @if($mb->status === 'muted' || ! $mb->can_post)<span class="mg-tag warn">Lecture seule</span>@endif
                </div>
            </div>
        @empty
            <p>Aucun service n'a encore été ajouté.</p>
        @endforelse
    </aside>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const root = document.getElementById('mg');
    if (!root) return;

    const $ = (id) => document.getElementById(id);
    const list = $('mg-list'), scroller = $('mg-scroll'), pill = $('mg-pill'), empty = $('mg-empty');
    const form = $('mg-form'), input = $('mg-input');
    const cfg = root.dataset;
    let lastId = parseInt(cfg.last, 10) || 0, since = parseInt(cfg.since, 10) || 0;
    let hasMore = cfg.more === '1', replyTo = null, sending = false;
    const coarse = window.matchMedia('(pointer: coarse)').matches;

    // ── utilitaires ─────────────────────────────────────────
    async function api(url, method, body) {
        const res = await fetch(url, {
            method: method || 'GET',
            headers: Object.assign(
                { 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrf, 'X-Requested-With': 'XMLHttpRequest' },
                body instanceof FormData ? {} : { 'Content-Type': 'application/json' }
            ),
            body: body ? (body instanceof FormData ? body : JSON.stringify(body)) : undefined,
            credentials: 'same-origin',
        });
        let data = {};
        try { data = await res.json(); } catch (e) {}
        if (!res.ok) {
            const msg = data.message || (data.errors && Object.values(data.errors)[0][0]) || 'Une erreur est survenue.';
            const err = new Error(msg); err.status = res.status; throw err;
        }
        return data;
    }
    function toast(text) {
        document.querySelectorAll('.mg-toast').forEach(t => t.remove());
        const t = document.createElement('div'); t.className = 'mg-toast'; t.textContent = text;
        root.querySelector('.mg-chat').appendChild(t); setTimeout(() => t.remove(), 3500);
    }
    const nearBottom = () => scroller.scrollHeight - scroller.scrollTop - scroller.clientHeight < 140;
    const toBottom = () => { scroller.scrollTop = scroller.scrollHeight; pill.hidden = true; };
    const frag = (html) => { const t = document.createElement('template'); t.innerHTML = html.trim(); return t.content; };
    const itemUrl = (id) => cfg.item.replace('__ID__', id);

    function dayLabel(d) {
        if (d === cfg.today) return "Aujourd'hui";
        if (d === cfg.yesterday) return 'Hier';
        const [y, m, j] = d.split('-').map(Number);
        return new Date(y, m - 1, j).toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long' });
    }

    // séparateurs de jour + regroupement des messages consécutifs d'un même auteur
    function decorate() {
        list.querySelectorAll('.mg-day').forEach(n => n.remove());
        let prev = null;
        list.querySelectorAll('.mg-msg').forEach(el => {
            const newDay = !prev || prev.dataset.day !== el.dataset.day;
            if (newDay) {
                const sep = document.createElement('div'); sep.className = 'mg-day';
                sep.innerHTML = '<span></span>'; sep.firstChild.textContent = dayLabel(el.dataset.day);
                list.insertBefore(sep, el);
            }
            el.classList.toggle('cont', !newDay && prev.dataset.sender === el.dataset.sender
                && (el.dataset.ts - prev.dataset.ts) < 300);
            prev = el;
        });
        empty.hidden = !!prev;
    }

    function append(html) {
        const nodes = frag(html).querySelectorAll('.mg-msg');
        let added = 0, fromOthers = false;
        nodes.forEach(n => {
            const id = n.dataset.id;
            if (list.querySelector('.mg-msg[data-id="' + id + '"]')) return;
            list.appendChild(n); added++;
            if (!n.classList.contains('mine')) fromOthers = true;
        });
        return { added, fromOthers };
    }
    function replace(id, html) {
        const old = list.querySelector('.mg-msg[data-id="' + id + '"]');
        const neu = frag(html).querySelector('.mg-msg');
        if (old && neu) old.replaceWith(neu);
    }

    // ── rafraîchissement automatique ────────────────────────
    let timer = null, polling = false;
    async function poll() {
        if (polling) return; polling = true;
        try {
            const d = await api(cfg.poll + '?after=' + lastId + '&since=' + since);
            const stick = nearBottom();
            if (d.html) {
                const r = append(d.html);
                if (r.added) {
                    if (stick) { decorate(); toBottom(); } else { decorate(); pill.hidden = false; }
                }
            }
            (d.updates || []).forEach(u => replace(u.id, u.html));
            if (d.updates && d.updates.length) decorate();
            lastId = Math.max(lastId, d.last_id || 0); since = d.now || since;
            if ((d.can_post ? '1' : '0') !== cfg.canpost || !d.active && cfg.canpost === '1') location.reload();
        } catch (e) {
            if (e.status === 403) { window.location.href = cfg.index; return; }
        } finally { polling = false; }
    }
    function schedule() {
        clearTimeout(timer);
        timer = setTimeout(async () => { await poll(); schedule(); }, document.hidden ? 15000 : 4000);
    }
    document.addEventListener('visibilitychange', () => { if (!document.hidden) { poll(); schedule(); } });

    // ── envoi ───────────────────────────────────────────────
    function clearReply() { replyTo = null; $('mg-reply').hidden = true; }
    function setReply(el) {
        replyTo = el.dataset.id;
        $('mg-reply-name').textContent = el.dataset.name;
        $('mg-reply-text').textContent = el.dataset.excerpt;
        $('mg-reply').hidden = false; input.focus();
    }
    const fileInput = $('mg-file');
    function clearFile() { if (fileInput) { fileInput.value = ''; $('mg-chip').hidden = true; } }
    if (form) {
        $('mg-attach').addEventListener('click', () => fileInput.click());
        $('mg-chip-x').addEventListener('click', clearFile);
        fileInput.addEventListener('change', () => {
            const f = fileInput.files[0]; if (!f) return clearFile();
            if (f.size > parseInt(cfg.maxmb, 10) * 1048576) { toast('Fichier trop volumineux (max ' + cfg.maxmb + ' Mo).'); return clearFile(); }
            $('mg-chip-name').textContent = f.name + ' (' + (f.size / 1048576 >= 1 ? (f.size / 1048576).toFixed(1) + ' Mo' : Math.max(1, Math.round(f.size / 1024)) + ' Ko') + ')';
            $('mg-chip').hidden = false;
        });
        const grow = () => { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 140) + 'px'; };
        input.addEventListener('input', grow);
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey && !coarse) { e.preventDefault(); form.requestSubmit(); }
        });
        $('mg-reply-x').addEventListener('click', clearReply);
        form.addEventListener('submit', async e => {
            e.preventDefault();
            const text = input.value.trim(), f = fileInput.files[0];
            if ((!text && !f) || sending) return;
            sending = true; $('mg-send').disabled = true;
            try {
                const fd = new FormData();
                fd.append('message', text);
                if (replyTo) fd.append('reply_to_id', replyTo);
                if (f) fd.append('fichier', f);
                const d = await api(cfg.send, 'POST', fd);
                append(d.html); lastId = Math.max(lastId, d.id);
                input.value = ''; grow(); clearReply(); clearFile(); decorate(); toBottom();
            } catch (err) { toast(err.message); }
            finally { sending = false; $('mg-send').disabled = false; input.focus(); }
        });
    }

    // ── actions sur un message ──────────────────────────────
    function closeMenus() { list.querySelectorAll('.mg-menu').forEach(m => m.hidden = true); }
    document.addEventListener('click', e => { if (!e.target.closest('.mg-more')) closeMenus(); });

    function startEdit(el) {
        let box = el.querySelector('.mg-text');
        if (!box) { box = document.createElement('div'); box.className = 'mg-text'; el.querySelector('.mg-meta').before(box); }
        const original = box.innerHTML;
        box.innerHTML = '<div class="mg-edit"><textarea maxlength="2000"></textarea><div>' +
            '<button type="button" class="mg-btn" data-x="cancel">Annuler</button>' +
            '<button type="button" class="mg-btn primary" data-x="save">Enregistrer</button></div></div>';
        const ta = box.querySelector('textarea'); ta.value = el.dataset.raw; ta.focus();
        box.querySelector('[data-x=cancel]').onclick = () => { if (original.trim()) box.innerHTML = original; else box.remove(); };
        box.querySelector('[data-x=save]').onclick = async () => {
            const text = ta.value.trim(); if (!text) return;
            try { const d = await api(itemUrl(el.dataset.id), 'PUT', { message: text }); replace(el.dataset.id, d.html); decorate(); }
            catch (err) { toast(err.message); }
        };
    }

    list.addEventListener('click', async e => {
        const b = e.target.closest('[data-act]'); if (!b) return;
        const el = b.closest('.mg-msg'), act = b.dataset.act;

        if (act === 'menu') {
            const menu = b.parentElement.querySelector('.mg-menu'), was = menu.hidden;
            closeMenus(); menu.hidden = !was; return;
        }
        if (act === 'goto') {
            const t = list.querySelector('.mg-msg[data-id="' + b.dataset.target + '"]');
            if (t) { t.scrollIntoView({ block: 'center' }); t.classList.add('flash'); setTimeout(() => t.classList.remove('flash'), 1400); }
            else toast('Ce message est plus ancien : chargez les messages précédents.');
            return;
        }
        closeMenus();
        if (act === 'reply') setReply(el);
        if (act === 'edit') startEdit(el);
        if (act === 'del' && confirm('Supprimer ce message pour tous les participants ?')) {
            try { const d = await api(itemUrl(el.dataset.id), 'DELETE'); replace(el.dataset.id, d.html); decorate(); }
            catch (err) { toast(err.message); }
        }
    });

    // ── anciens messages ────────────────────────────────────
    const older = $('mg-older');
    if (older) older.addEventListener('click', async () => {
        const first = list.querySelector('.mg-msg'); if (!first) return;
        try {
            const d = await api(cfg.poll + '?before=' + first.dataset.id);
            const h = scroller.scrollHeight;
            const nodes = frag(d.html).querySelectorAll('.mg-msg');
            const ref = list.firstChild;
            nodes.forEach(n => list.insertBefore(n, ref));
            decorate(); scroller.scrollTop += scroller.scrollHeight - h;
            if (!d.has_more) older.remove();
        } catch (err) { toast(err.message); }
    });

    // ── interface ───────────────────────────────────────────
    pill.addEventListener('click', toBottom);
    scroller.addEventListener('scroll', () => { if (nearBottom()) pill.hidden = true; });
    $('mg-toggle-side').addEventListener('click', () => root.classList.toggle('side-open'));
    $('mg-close-side').addEventListener('click', () => root.classList.remove('side-open'));

    decorate(); toBottom(); schedule();
})();
</script>
@endpush
