<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CongoAssist - Connexion Administrateur</title>
<link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* RESET ET TYPO */
* {margin:0;padding:0;box-sizing:border-box;font-family:'Montserrat',sans-serif;}
body {background:#f0f2f7; min-height:100vh; display:flex; justify-content:center; align-items:center;}

/* CARD PRINCIPALE */
.card {
    background:#fff;
    padding:40px 35px;
    border-radius:18px;
    box-shadow:0 15px 30px rgba(0,0,0,0.12);
    width:100%;
    max-width:420px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

/* LOGO */
.logo {
    text-align:center;
    margin-bottom:25px;
}
.logo img {
    width:60px; height:60px;
    border-radius:50%; border:2px solid #1a4b9b;
    object-fit:cover;
}
.logo h1 {
    font-family:'Playfair Display', serif;
    font-size:2rem;
    color:#1a4b9b;
}
.logo h1 span { color:#32CD32; }

/* TITRE FORMULAIRE */
.card h2 { text-align:center; margin-bottom:30px; font-size:1.6rem; color:#1a4b9b; font-weight:700; }

/* FORMULAIRE */
form label { display:block; margin-bottom:5px; font-weight:600; color:#1a4b9b; }
form input {
    width:100%; padding:12px 15px; margin-bottom:20px;
    border:1px solid #ccc; border-radius:10px; font-size:1rem;
    transition:0.3s;
}
form input:focus {
    border-color:#32CD32; outline:none;
    box-shadow:0 0 5px rgba(50,205,50,0.4);
}

/* BOUTON */
form button {
    width:100%; padding:14px;
    background:linear-gradient(135deg,#1a4b9b,#32CD32);
    color:white; border:none; border-radius:12px; font-size:1rem; font-weight:700;
    cursor:pointer; transition:0.3s;
}
form button:hover {
    background:linear-gradient(135deg,#32CD32,#1a4b9b);
}

/* LIENS */
form a {
    display:block; text-align:center; margin-top:15px;
    color:#1a4b9b; text-decoration:none; font-weight:600;
}
form a:hover { text-decoration:underline; }

/* RESPONSIVE */
@media(max-width:480px){
    .card {padding:30px 20px;}
    .logo h1 {font-size:1.6rem;}
    .card h2 {font-size:1.4rem;}
}
</style>
</head>
<body>

<div class="card">
    <div class="logo">
        <img src="{{ asset('medias/Clogo.jpg') }}" alt="CongoAssist">
        <h1>Congo<span>Assist</span></h1>
    </div>
    <h2>Connexion Administrateur</h2>
    <form action="{{ route('admin.login.process') }}" method="post" enctype="multipart/form-data">
        @csrf
        <label for="email">Votre Email</label>
        <input type="text" id="email" name="email" placeholder="Entrez votre email" required>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="mot_passe" placeholder="Entrez votre mot de passe" required>

        <button type="submit">Se connecter</button>
        <a href="{{ route('admin.accueil') }}"><-Retour accueil</a>
    </form>
</div>

</body>
</html>
