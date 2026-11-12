@php $edition = $edition ?? false; @endphp
<div class="mg-form-grid">
    <div class="mg-f">
        <label>Nom du groupe</label>
        <input type="text" name="title" required maxlength="255" value="{{ old('title', $space->title ?? '') }}" placeholder="Ex. Coordination urgences Brazzaville">
    </div>
    <div class="mg-f">
        <label>Visibilité</label>
        <select name="type">
            <option value="prive"  @selected(old('type', $space->type ?? 'prive') === 'prive')>Privé — services autorisés seulement</option>
            <option value="public" @selected(old('type', $space->type ?? '') === 'public')>Public — ouvert aux citoyens</option>
        </select>
    </div>
    <div class="mg-f">
        <label>Comment un service obtient l'accès</label>
        <select name="join_policy">
            <option value="invite"  @selected(old('join_policy', $space->join_policy ?? 'invite') === 'invite')>Sur invitation de l'administration</option>
            <option value="request" @selected(old('join_policy', $space->join_policy ?? '') === 'request')>Le service peut demander l'accès</option>
        </select>
    </div>
    <div class="mg-f">
        <label>Service responsable (modérateur)</label>
        <select name="service_id">
            <option value="">— Aucun —</option>
            @foreach($services as $sv)
                <option value="{{ $sv->id }}" @selected((int) old('service_id', $space->service_id ?? 0) === $sv->id)>{{ $sv->nom }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mg-f" style="margin-top:12px;">
    <label>Description</label>
    <textarea name="description" rows="2" maxlength="1000">{{ old('description', $space->description ?? '') }}</textarea>
</div>
<div style="display:flex; gap:22px; flex-wrap:wrap; margin-top:12px;">
    <label class="mg-check"><input type="checkbox" name="services_can_post" value="1" @checked(old('services_can_post', $space->services_can_post ?? true))> Les services membres peuvent écrire</label>
    <label class="mg-check"><input type="checkbox" name="citizens_can_post" value="1" @checked(old('citizens_can_post', $space->citizens_can_post ?? false))> Les citoyens peuvent écrire (groupe public)</label>
</div>
@unless($edition)
    <div class="mg-f" style="margin-top:14px;">
        <label>Services autorisés dès la création (vous pourrez en ajouter ensuite)</label>
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:6px; max-height:170px; overflow:auto; border:1px solid #E2E8F0; border-radius:8px; padding:10px;">
            @foreach($services as $sv)
                <label class="mg-check"><input type="checkbox" name="service_ids[]" value="{{ $sv->id }}" @checked(in_array($sv->id, (array) old('service_ids', [])))> {{ $sv->nom }}</label>
            @endforeach
        </div>
    </div>
@endunless
