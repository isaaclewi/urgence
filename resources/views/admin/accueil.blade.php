<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>CongoAssist — Portail Administration</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('medias/Clogo.jpg') }}">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    :root {
      --primary: #667eea;
      --primary-dark: #764ba2;
      --bg-light: #f8fafc;
      --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --card-bg: #ffffff;
      --text-dark: #1e293b;
      --text-muted: #64748b;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --info: #3b82f6;
      --radius: 16px;
      --shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 12px 40px rgba(102, 126, 234, 0.15);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: var(--text-dark);
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      min-height: 100vh;
    }

    /* ===== CONTAINER ===== */
    .wrap {
      max-width: 1200px;
      margin: 0 auto;
      padding: 24px;
    }

    /* ===== HEADER ===== */
    header.site {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 20px 32px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      margin-bottom: 32px;
      position: sticky;
      top: 24px;
      z-index: 100;
      transition: all 0.3s ease;
    }

    header.site:hover {
      box-shadow: var(--shadow-lg);
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .brand-logo {
      width: 64px;
      height: 64px;
      border-radius: 14px;
      object-fit: cover;
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
      border: 3px solid white;
      transition: transform 0.3s ease;
    }

    .brand-logo:hover {
      transform: rotate(5deg) scale(1.05);
    }

    .brand-text h1 {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem;
      margin: 0;
      font-weight: 700;
    }

    .brand-text h1 .accent {
      background: var(--bg-gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .brand-subtitle {
      font-size: 0.85rem;
      color: var(--text-muted);
      font-weight: 500;
    }

    /* ===== NAVIGATION ===== */
    nav.top {
      display: flex;
      gap: 12px;
      align-items: center;
    }

    nav.top a {
      padding: 10px 18px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      color: var(--text-muted);
      transition: all 0.3s ease;
      font-size: 15px;
    }

    nav.top a:hover {
      background: rgba(102, 126, 234, 0.1);
      color: var(--primary);
      transform: translateY(-2px);
    }

    nav.top a.cta {
      background: var(--bg-gradient);
      color: white;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    nav.top a.cta:hover {
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    /* ===== HERO SECTION ===== */
    .hero {
      display: grid;
      grid-template-columns: 1.2fr 0.8fr;
      gap: 24px;
      margin-bottom: 40px;
    }

    .hero-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: var(--radius);
      padding: 40px;
      box-shadow: var(--shadow);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .hero-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--bg-gradient);
    }

    .hero-card:hover {
      box-shadow: var(--shadow-lg);
      transform: translateY(-4px);
    }

    .hero .title {
      font-size: 2rem;
      font-weight: 800;
      margin-bottom: 16px;
      line-height: 1.3;
      color: var(--text-dark);
    }

    .hero .lead {
      color: var(--text-muted);
      font-size: 1.05rem;
      margin-bottom: 24px;
      line-height: 1.7;
    }

    /* ===== BUTTONS ===== */
    .actions {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 24px;
      border-radius: 12px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      font-size: 15px;
      transition: all 0.3s ease;
    }

    .btn.primary {
      background: var(--bg-gradient);
      color: white;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn.primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .btn.secondary {
      background: transparent;
      border: 2px solid var(--primary);
      color: var(--primary);
    }

    .btn.secondary:hover {
      background: var(--primary);
      color: white;
    }

    /* ===== QUICK STATS ===== */
    .quick-stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      margin-top: 24px;
    }

    .stat-box {
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
      padding: 20px;
      border-radius: 12px;
      border: 2px solid rgba(102, 126, 234, 0.1);
      transition: all 0.3s ease;
    }

    .stat-box:hover {
      transform: translateY(-4px);
      border-color: var(--primary);
      box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
    }

    .stat-box h3 {
      font-size: 0.9rem;
      color: var(--text-muted);
      margin-bottom: 8px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .stat-box .value {
      font-size: 1.8rem;
      font-weight: 700;
      background: var(--bg-gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    /* ===== RIGHT SIDEBAR ===== */
    .right-cards {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .card-small {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      padding: 24px;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .card-small::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: var(--bg-gradient);
    }

    .card-small:hover {
      box-shadow: var(--shadow-lg);
      transform: translateY(-4px);
    }

    .card-small h3 {
      margin-bottom: 12px;
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .card-small h3 i {
      color: var(--primary);
    }

    .card-small p {
      color: var(--text-muted);
      line-height: 1.6;
    }

    /* ===== SERVICES SECTION ===== */
    .services-section {
      margin: 40px 0;
    }

    .section-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .section-header h2 {
      font-size: 2.2rem;
      font-weight: 800;
      margin-bottom: 12px;
      color: white;
    }

    .section-header p {
      color: rgba(255, 255, 255, 0.9);
      font-size: 1.1rem;
    }

    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 24px;
    }

    .service-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      padding: 28px;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      display: flex;
      gap: 20px;
      align-items: flex-start;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .service-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--bg-gradient);
      transform: scaleX(0);
      transition: transform 0.4s ease;
    }

    .service-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-lg);
    }

    .service-card:hover::before {
      transform: scaleX(1);
    }

    .service-icon {
      width: 70px;
      height: 70px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: white;
      flex-shrink: 0;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .icon-medical { background: linear-gradient(135deg, #f87171 0%, #ef4444 100%); }
    .icon-police { background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%); }
    .icon-vaccine { background: linear-gradient(135deg, #34d399 0%, #10b981 100%); }
    .icon-urban { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }
    .icon-electric { background: linear-gradient(135deg, #a78bfa 0%, #7c3aed 100%); }
    .icon-info { background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%); }

    .service-content h4 {
      margin-bottom: 10px;
      font-size: 1.2rem;
      color: var(--text-dark);
    }

    .service-content p {
      color: var(--text-muted);
      line-height: 1.6;
    }

    /* ===== FEATURES BAR ===== */
    .features {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      margin-top: 32px;
    }

    .feature-item {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      padding: 20px;
      border-radius: 12px;
      text-align: center;
      font-weight: 600;
      box-shadow: var(--shadow);
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .feature-item i {
      color: var(--primary);
      font-size: 1.2rem;
    }

    .feature-item:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
    }

    /* ===== FOOTER ===== */
    footer.site {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      padding: 32px;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      margin-top: 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
    }

    .footer-left strong {
      display: block;
      margin-bottom: 8px;
      font-size: 1.1rem;
    }

    .footer-left a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
    }

    .footer-left a:hover {
      text-decoration: underline;
    }

    .footer-right {
      color: var(--text-muted);
      font-size: 0.9rem;
    }

    /* ===== ANIMATIONS ===== */
    .fade-in {
      animation: fadeInUp 0.6s ease forwards;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .fade-in:nth-child(1) { animation-delay: 0.1s; }
    .fade-in:nth-child(2) { animation-delay: 0.2s; }
    .fade-in:nth-child(3) { animation-delay: 0.3s; }
    .fade-in:nth-child(4) { animation-delay: 0.4s; }
    .fade-in:nth-child(5) { animation-delay: 0.5s; }
    .fade-in:nth-child(6) { animation-delay: 0.6s; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
      .hero {
        grid-template-columns: 1fr;
      }
      
      .services-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      header.site {
        flex-direction: column;
        gap: 16px;
        position: relative;
        top: 0;
      }
      
      nav.top {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      .hero .title {
        font-size: 1.5rem;
      }
      
      .services-grid {
        grid-template-columns: 1fr;
      }
      
      .quick-stats {
        grid-template-columns: 1fr;
      }
      
      .features {
        grid-template-columns: 1fr;
      }
      
      footer.site {
        flex-direction: column;
        text-align: center;
      }
    }
  </style>
</head>

<body>
  <div class="wrap">

    <!-- ===== HEADER ===== -->
    <header class="site">
      <div class="brand">
        <img src="{{ asset('medias/Clogo.jpg') }}" alt="CongoAssist Logo" class="brand-logo">
        <div class="brand-text">
          <h1>Congo<span class="accent">Assist</span></h1>
          <span class="brand-subtitle">Portail d'administration & communication</span>
        </div>
      </div>

      <nav class="top">
        <a href="#accueil"><i class="fas fa-home"></i> Accueil</a>
        <a href="#services"><i class="fas fa-th-large"></i> Services</a>
        <a href="#contact"><i class="fas fa-envelope"></i> Contact</a>
        <a href="{{ route('admin.login') }}" class="cta">
          <i class="fas fa-sign-in-alt"></i> Connexion Admin
        </a>
      </nav>
    </header>

    <!-- ===== HERO SECTION ===== -->
    <section class="hero" id="accueil">
      <div class="hero-card fade-in">
        <h2 class="title">CongoAssist — Administration & coordination des urgences nationales</h2>
        <p class="lead">
          Centralisez, coordonnez et communiquez rapidement avec les services essentiels — médical, police, vaccination, urbanisme et réseaux électriques. Une interface institutionnelle pensée pour l'administration et la confiance citoyenne.
        </p>

        <div class="actions">
          <button class="btn primary" onclick="location.href='{{ route('admin.login') }}'">
            <i class="fas fa-tachometer-alt"></i>
            Accéder au panneau admin
          </button>
          <button class="btn secondary" onclick="document.getElementById('services').scrollIntoView({behavior:'smooth'})">
            <i class="fas fa-search"></i>
            Découvrir les services
          </button>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats">
          <div class="stat-box">
            <h3><i class="fas fa-ambulance"></i> Interventions</h3>
            <div class="value" id="stat1">—</div>
          </div>
          <div class="stat-box">
            <h3><i class="fas fa-clock"></i> En attente</h3>
            <div class="value" id="stat2">—</div>
          </div>
          <div class="stat-box">
            <h3><i class="fas fa-users"></i> Agents actifs</h3>
            <div class="value" id="stat3">—</div>
          </div>
        </div>
      </div>

      <!-- Right Sidebar -->
      <aside class="right-cards">
        <div class="card-small fade-in">
          <h3>
            <i class="fas fa-exclamation-triangle"></i>
            Message important
          </h3>
          <p>Consignes d'urgence, contacts de permanence, et procédure d'escalade. Peut être mis à jour depuis le panneau admin.</p>
        </div>
        
        <div class="card-small fade-in">
          <h3>
            <i class="fas fa-bolt"></i>
            Raccourcis
          </h3>
          <p>Créer une alerte · Consulter les interventions · Gérer les équipes · Paramètres réseau</p>
        </div>
      </aside>
    </section>

    <!-- ===== SERVICES SECTION ===== -->
    <section class="services-section" id="services">
      <div class="section-header">
        <h2>Nos domaines d'intervention</h2>
        <p>Chaque service possède son workflow et ses contacts de coordination</p>
      </div>

      <div class="services-grid">
        <article class="service-card fade-in">
          <div class="service-icon icon-medical">
            <i class="fas fa-heartbeat"></i>
          </div>
          <div class="service-content">
            <h4>Urgence Médicale</h4>
            <p>Signalement des cas, géolocalisation des patients, coordination ambulances et disponibilité des lits.</p>
          </div>
        </article>

        <article class="service-card fade-in">
          <div class="service-icon icon-police">
            <i class="fas fa-shield-alt"></i>
          </div>
          <div class="service-content">
            <h4>Urgence Police & Sécurité</h4>
            <p>Réception des signalements citoyens, cartographie des incidents et liaison avec les postes de sécurité.</p>
          </div>
        </article>

        <article class="service-card fade-in">
          <div class="service-icon icon-vaccine">
            <i class="fas fa-syringe"></i>
          </div>
          <div class="service-content">
            <h4>Vaccination</h4>
            <p>Suivi des campagnes, gestion des stocks, alertes vaccination urgente et centres de vaccination.</p>
          </div>
        </article>

        <article class="service-card fade-in">
          <div class="service-icon icon-urban">
            <i class="fas fa-city"></i>
          </div>
          <div class="service-content">
            <h4>Urgence Urbaine</h4>
            <p>Signalement des routes, feux défectueux, inondations et dégagement des voies publiques.</p>
          </div>
        </article>

        <article class="service-card fade-in">
          <div class="service-icon icon-electric">
            <i class="fas fa-bolt"></i>
          </div>
          <div class="service-content">
            <h4>Électricité & Réseau</h4>
            <p>Alerte panne, planification dépannage, suivi des rétablissements et communication avec le fournisseur.</p>
          </div>
        </article>

        <article class="service-card fade-in">
          <div class="service-icon icon-info">
            <i class="fas fa-info-circle"></i>
          </div>
          <div class="service-content">
            <h4>Informations & Communication</h4>
            <p>Messages officiels, bulletins de situation et contacts de presse pour diffusion rapide.</p>
          </div>
        </article>
      </div>

      <!-- Features -->
      <div class="features">
        <div class="feature-item">
          <i class="fas fa-check-circle"></i>
          Procédures validées
        </div>
        <div class="feature-item">
          <i class="fas fa-lock"></i>
          Sécurité & accès restreint
        </div>
        <div class="feature-item">
          <i class="fas fa-bell"></i>
          Notifications temps réel
        </div>
      </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="site" id="contact">
      <div class="footer-left">
        <strong><i class="fas fa-phone"></i> Contact</strong>
        <div style="color: var(--text-muted);">
          Email: <a href="mailto:lombaisaac8@gmail.com">lombaisaac8@gmail.com</a><br>
          Téléphone: <a href="tel:+242050520146">+242 05 052 0146</a>
        </div>
      </div>
      <div class="footer-right">
        <small>© {{ date('Y') }} CongoAssist — Tous droits réservés</small>
      </div>
    </footer>

  </div>

  <!-- ===== SCRIPTS ===== -->
  <script>
    // Démonstration des statistiques
    document.getElementById('stat1').textContent = '12';
    document.getElementById('stat2').textContent = '3';
    document.getElementById('stat3').textContent = '24';

    // Animation des chiffres (compteur progressif)
    function animateValue(id, start, end, duration) {
      const obj = document.getElementById(id);
      const range = end - start;
      const increment = end > start ? 1 : -1;
      const stepTime = Math.abs(Math.floor(duration / range));
      let current = start;
      
      const timer = setInterval(function() {
        current += increment;
        obj.textContent = current;
        if (current == end) {
          clearInterval(timer);
        }
      }, stepTime);
    }

    // Animer les stats au chargement
    window.addEventListener('load', () => {
      setTimeout(() => {
        animateValue('stat1', 0, 12, 1000);
        animateValue('stat2', 0, 3, 1000);
        animateValue('stat3', 0, 24, 1000);
      }, 500);
    });

    // Smooth scroll pour les liens de navigation
    document.querySelectorAll('nav.top a[href^="#"]').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href').slice(1);
        const target = document.getElementById(targetId);
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });

    // Header sticky effect
    let lastScroll = 0;
    const header = document.querySelector('header.site');
    
    window.addEventListener('scroll', () => {
      const currentScroll = window.pageYOffset;
      
      if (currentScroll > 100) {
        header.style.boxShadow = '0 8px 32px rgba(102, 126, 234, 0.2)';
      } else {
        header.style.boxShadow = 'var(--shadow)';
      }
      
      lastScroll = currentScroll;
    });
  </script>
</body>

</html>