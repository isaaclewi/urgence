/* CongoAssist — Fil d'actualités : texte repliable, visionneuse, partage,
   chargement progressif, aperçu avant publication. Aucune dépendance. */
(function () {
    'use strict';

    var $ = function (s, r) { return (r || document).querySelector(s); };
    var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

    /* ── Notification ─────────────────────────────────────────── */
    var toastEl, toastTimer;
    function toast(msg) {
        if (!toastEl) {
            toastEl = document.createElement('div');
            toastEl.className = 'fa-toast';
            toastEl.setAttribute('role', 'status');
            document.body.appendChild(toastEl);
        }
        toastEl.textContent = msg;
        toastEl.classList.add('is-on');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { toastEl.classList.remove('is-on'); }, 2200);
    }

    /* ── Texte : repli à 5 lignes + « Voir plus » ─────────────── */
    function initText(post) {
        var txt = $('[data-fa-text]', post), btn = $('[data-fa-toggle]', post);
        if (!txt || !btn || txt.dataset.faInit) { return; }
        txt.dataset.faInit = '1';
        txt.classList.add('is-clamped');
        if (txt.scrollHeight <= txt.clientHeight + 2) {
            txt.classList.remove('is-clamped');
            return;
        }
        btn.hidden = false;
        btn.addEventListener('click', function () {
            var ouvert = txt.classList.toggle('is-clamped') === false;
            btn.textContent = ouvert ? 'Voir moins' : 'Voir plus';
            btn.setAttribute('aria-expanded', ouvert ? 'true' : 'false');
        });
    }

    /* ── Média : fond flouté + image cassée ───────────────────── */
    function initMedia(post) {
        $$('.fa-media.is-image', post).forEach(function (box) {
            var img = $('img', box);
            if (!img || box.dataset.faInit) { return; }
            box.dataset.faInit = '1';
            var fond = function () {
                var src = img.currentSrc || img.src;
                if (src) { box.style.setProperty('--fa-bg', 'url("' + src.replace(/"/g, '%22') + '")'); }
            };
            if (img.complete && img.naturalWidth) { fond(); } else { img.addEventListener('load', fond); }
            img.addEventListener('error', function () { box.classList.add('is-broken'); });
            if (img.complete && !img.naturalWidth && img.getAttribute('src')) { box.classList.add('is-broken'); }
        });
        $$('.fa-avatar img', post).forEach(function (a) {
            a.addEventListener('error', function () { a.remove(); });
        });
    }

    function remesurer() {
        $$('.fa-post').forEach(function (post) {
            var txt = $('[data-fa-text]', post), btn = $('[data-fa-toggle]', post);
            if (!txt || !btn || !txt.classList.contains('is-clamped')) { return; }
            btn.hidden = !(txt.scrollHeight > txt.clientHeight + 2);
        });
    }
    window.addEventListener('load', remesurer);
    window.addEventListener('resize', remesurer);

    function initPost(post) { initText(post); initMedia(post); }
    function initAll(root) { $$('.fa-post', root || document).forEach(initPost); }

    /* Une seule vidéo à la fois */
    document.addEventListener('play', function (e) {
        if (e.target && e.target.tagName === 'VIDEO') {
            $$('.fa-page video').forEach(function (v) { if (v !== e.target) { v.pause(); } });
        }
    }, true);

    /* ── Visionneuse plein écran ──────────────────────────────── */
    var box, boxImg;
    function ouvrir(src, alt) {
        if (!box) {
            box = document.createElement('div');
            box.className = 'fa-lightbox';
            box.setAttribute('role', 'dialog');
            box.setAttribute('aria-modal', 'true');
            box.innerHTML = '<button type="button" class="fa-lightbox-close" aria-label="Fermer">&times;</button><img alt="">';
            document.body.appendChild(box);
            boxImg = $('img', box);
            box.addEventListener('click', function (e) { if (e.target !== boxImg) { fermer(); } });
        }
        boxImg.src = src;
        boxImg.alt = alt || '';
        box.classList.add('is-open');
        document.documentElement.style.overflow = 'hidden';
    }
    function fermer() {
        if (!box) { return; }
        box.classList.remove('is-open');
        document.documentElement.style.overflow = '';
    }
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { fermer(); } });

    /* ── Clics délégués : zoom, partage ───────────────────────── */
    document.addEventListener('click', function (e) {
        var img = e.target.closest ? e.target.closest('[data-fa-zoom]') : null;
        if (img) { ouvrir(img.currentSrc || img.src, img.alt); return; }

        var share = e.target.closest ? e.target.closest('[data-fa-share]') : null;
        if (share) {
            var url = location.origin + location.pathname + '#post-' + share.dataset.faShare;
            var data = { title: share.dataset.faTitle || 'CongoAssist', text: share.dataset.faText || '', url: url };
            if (navigator.share) {
                navigator.share(data).catch(function () {});
            } else if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function () { toast('Lien copié'); }, function () { toast(url); });
            } else {
                toast(url);
            }
        }
    });

    /* ── Chargement progressif (bouton + défilement) ──────────── */
    var chargement = false, observer;
    function observerBouton() {
        var btn = $('[data-fa-more]');
        if (!btn || !('IntersectionObserver' in window)) { return; }
        if (!observer) {
            observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (en) { if (en.isIntersecting) { charger(en.target); } });
            }, { rootMargin: '400px 0px' });
        }
        observer.observe(btn);
    }
    function charger(btn) {
        if (chargement || !btn || !btn.href) { return; }
        chargement = true;
        btn.classList.add('is-loading');
        var label = btn.textContent;
        btn.textContent = 'Chargement…';

        fetch(btn.href, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { if (!r.ok) { throw new Error(r.status); } return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var feed = $('#fa-feed'), wrap = $('.fa-more-wrap', feed);
                var nouveaux = $$('#fa-feed .fa-post', doc);
                var suite = $('#fa-feed .fa-more-wrap', doc);
                nouveaux.forEach(function (p) {
                    if (!document.getElementById(p.id)) { feed.insertBefore(document.importNode(p, true), wrap); }
                });
                if (wrap) { wrap.remove(); }
                if (suite) { feed.appendChild(document.importNode(suite, true)); }
                initAll(feed);
                observerBouton();
            })
            .catch(function () {
                btn.classList.remove('is-loading');
                btn.textContent = label;
                toast('Connexion indisponible. Réessayez.');
            })
            .then(function () { chargement = false; });
    }
    document.addEventListener('click', function (e) {
        var btn = e.target.closest ? e.target.closest('[data-fa-more]') : null;
        if (btn) { e.preventDefault(); charger(btn); }
    });

    /* ── Compositeur : aperçu, compteur, anti double-envoi ────── */
    function initComposer(form) {
        var area = $('textarea', form), file = $('input[type=file]', form), prev = $('.fa-preview', form);
        var count = $('.fa-count', form), submit = $('.fa-publish', form);
        var MAX = 50 * 1024 * 1024, url = null;

        function taille() {
            if (!area) { return; }
            area.style.height = 'auto';
            area.style.height = Math.min(area.scrollHeight + 2, 360) + 'px';
            if (count) { count.textContent = area.value.length + ' / ' + (area.maxLength > 0 ? area.maxLength : 5000); }
        }
        if (area) { area.addEventListener('input', taille); taille(); }

        function vider() {
            if (url) { URL.revokeObjectURL(url); url = null; }
            if (file) { file.value = ''; }
            if (prev) { prev.classList.remove('is-on'); prev.innerHTML = ''; }
        }
        if (file && prev) {
            file.addEventListener('change', function () {
                var f = file.files && file.files[0];
                if (!f) { vider(); return; }
                if (f.size > MAX) { toast('Fichier trop lourd (50 Mo maximum).'); vider(); return; }
                if (url) { URL.revokeObjectURL(url); }
                url = URL.createObjectURL(f);
                var el = f.type.indexOf('video') === 0
                    ? '<video src="' + url + '" controls playsinline preload="metadata"></video>'
                    : '<img src="' + url + '" alt="Aperçu">';
                prev.innerHTML = el + '<button type="button" class="fa-preview-remove" aria-label="Retirer le média">&times;</button>';
                prev.classList.add('is-on');
                $('.fa-preview-remove', prev).addEventListener('click', vider);
            });
        }
        form.addEventListener('submit', function () {
            if (submit) { submit.disabled = true; submit.textContent = file && file.files && file.files[0] ? 'Envoi du média…' : 'Publication…'; }
        });
    }

    function start() {
        initAll();
        observerBouton();
        $$('[data-fa-composer]').forEach(initComposer);
        if (location.hash) {
            var cible = document.getElementById(location.hash.slice(1));
            if (cible) { cible.scrollIntoView(); }
        }
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', start); } else { start(); }
})();
