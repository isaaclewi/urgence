<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion du personnel — CongoAssist</title>
    <link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:Inter,system-ui,sans-serif;background:#0B1E3D;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
        .box{background:#fff;border-radius:16px;padding:2rem;width:100%;max-width:400px}
        h1{font-size:1.4rem;color:#0F1923;margin-bottom:.25rem}
        p.sub{color:#64748B;font-size:.9rem;margin-bottom:1.25rem}
        label{display:block;font-size:.85rem;font-weight:600;margin:.9rem 0 .3rem;color:#0F1923}
        input{width:100%;padding:.7rem .8rem;border:1px solid #E2E8F0;border-radius:10px;font-size:1rem}
        input:focus{outline:2px solid #1DB87A;border-color:#1DB87A}
        button{width:100%;margin-top:1.2rem;padding:.8rem;border:0;border-radius:10px;background:#1DB87A;color:#fff;font-size:1rem;font-weight:600;cursor:pointer}
        .err{background:#FEE2E2;color:#991B1B;border-radius:10px;padding:.7rem .9rem;font-size:.88rem;margin-bottom:.5rem}
        .ok{background:#DCFCE7;color:#166534;border-radius:10px;padding:.7rem .9rem;font-size:.88rem;margin-bottom:.5rem}
        a{color:#1A3E6E;font-size:.85rem;display:block;text-align:center;margin-top:1rem}
    </style>
</head>
<body>
<div class="box">
    <h1>Espace du personnel</h1>
    <p class="sub">Connectez-vous avec votre matricule et le mot de passe remis par votre service.</p>
    @if(session('success'))<div class="ok">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="err">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="err">@foreach($errors->all() as $e){{ $e }}<br>@endforeach</div>@endif
    <form method="POST" action="{{ route('agent.login.process') }}">
        @csrf
        <label for="matricule">Matricule</label>
        <input id="matricule" name="matricule" value="{{ old('matricule') }}" placeholder="Ex : POL3-26-0001" autocomplete="username" required autofocus>
        <label for="password">Mot de passe</label>
        <input id="password" type="password" name="password" autocomplete="current-password" required>
        <button type="submit">Se connecter</button>
    </form>
    <a href="{{ route('services.login') }}">Connexion service / équipe →</a>
</div>
</body>
</html>
