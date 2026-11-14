{{--
    Zone de publication (services et admin).
    Variables : $action (URL du POST), $auteur (nom affiché), $photo (URL ou null)
--}}
<form class="fa-composer" method="POST" action="{{ $action }}" enctype="multipart/form-data" data-fa-composer>
    @csrf
    <div class="fa-composer-top">
        <div class="fa-avatar" aria-hidden="true">
            {{ mb_strtoupper(mb_substr($auteur ?? 'C', 0, 1)) }}
            @if(!empty($photo))<img src="{{ $photo }}" alt="" loading="lazy">@endif
        </div>
        <textarea name="contenu" maxlength="5000" required rows="3" aria-label="Contenu de l'actualité"
                  placeholder="Quoi de neuf, {{ $auteur ?? '' }} ? Écrivez votre actualité ici…">{{ old('contenu') }}</textarea>
    </div>

    <div class="fa-preview" aria-live="polite"></div>

    <div class="fa-composer-bar">
        <label class="fa-attach">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            Photo / vidéo
            <input type="file" name="url_media" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4">
        </label>
        <span class="fa-count">0 / 5000</span>
        <button type="submit" class="fa-publish">Publier</button>
    </div>
    <p class="fa-hint">JPG, PNG, WebP, GIF ou MP4 — 50 Mo maximum. Les grandes photos sont automatiquement allégées.</p>
</form>
