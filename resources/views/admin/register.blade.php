<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Inscription Administrateur - CongoAssist</title>
<link rel="icon" href="{{ asset('medias/Clogo.jpg') }}" type="image/png">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@700&family=Lora&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* ===== RESET ===== */
* {margin:0;padding:0;box-sizing:border-box;font-family:'Montserrat',sans-serif;}
body {
    background: linear-gradient(120deg,#E0F7FA,#E8F5E9);
    color:#1B263B;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* ===== CONTAINER ===== */
.register-container {
    width:100%;
    max-width:900px;
    padding:30px;
    display:flex;
    justify-content:center;
}

/* ===== FORM CARD ===== */
.form-card {
    background:#FFFFFF;
    width:100%;
    border-radius:20px;
    padding:40px;
    box-shadow:0 15px 30px rgba(0,0,0,0.1);
    transition:0.3s;
}
.form-card:hover {transform:translateY(-5px);box-shadow:0 20px 40px rgba(0,0,0,0.15);}
.form-card .logo {display:flex;align-items:center;gap:15px;margin-bottom:25px;justify-content:center;}
.form-card .logo img {width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid #1B263B;}
.form-card .logo h1 {font-family:'Playfair Display', serif; font-size:28px;color:#0D3B66;}
.form-card .logo h1 span {color:#FF6F61;}
.form-card h2 {text-align:center;margin-bottom:30px;color:#0D3B66;font-weight:700;}

/* ===== FORM ===== */
form .row {display:flex;gap:20px;margin-bottom:20px;flex-wrap:wrap;}
.form-group {flex:1 1 45%;display:flex;flex-direction:column;}
.form-group label {margin-bottom:5px;font-weight:600;color:#0D3B66;}
.form-group input, .form-group select {
    padding:12px 15px;
    border-radius:10px;
    border:1px solid #ccc;
    font-size:15px;
    transition:0.3s;
}
.form-group input:focus, .form-group select:focus {border-color:#FF6F61;outline:none;}
.btn-submit {
    width:100%;
    padding:15px;
    background:#FF6F61;
    color:white;
    font-size:16px;
    font-weight:600;
    border:none;
    border-radius:12px;
    cursor:pointer;
    transition:0.3s;
}
.btn-submit:hover {background:linear-gradient(135deg,#FFAB91,#FF6F61);}

/* ===== RESPONSIVE ===== */
@media(max-width:768px){
    .form-group {flex:1 1 100%;}
}
</style>
</head>
<body>

<div class="register-container">
    <div class="form-card">
        <div class="logo">
            <img src="{{ asset('medias/Clogo.jpg') }}" alt="CongoAssist">
            <h1>Congo<span>Assist</span></h1>
        </div>

        <h2>Inscription Administrateur</h2>

        <form method="POST" action="{{ route('admin.register.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="form-group">
                    <label><i class="fas fa-id-badge"></i> Matricule</label>
                    <input type="text" name="matricule" value="{{ old('matricule') }}" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nom</label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Prénom</label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-venus-mars"></i> Sexe</label>
                    <select name="sexe" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="H" {{ old('sexe')=='H'?'selected':'' }}>Homme</option>
                        <option value="F" {{ old('sexe')=='F'?'selected':'' }}>Femme</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Adresse</label>
                    <input type="text" name="adresse" value="{{ old('adresse') }}" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone') }}" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" name="mot_passe" required>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <label><i class="fas fa-user-shield"></i> Rôle</label>
                    <input type="text" name="role" value="{{ old('role','Administrateur') }}" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Photo de profil</label>
                    <input type="file" name="photo" accept="image/*">
                </div>
            </div>

            <button type="submit" class="btn-submit">Créer le compte</button>
        </form>
    </div>
</div>

</body>
</html>
