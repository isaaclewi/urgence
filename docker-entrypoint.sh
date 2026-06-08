#!/bin/bash

# Ne pas utiliser set -e — on gère les erreurs manuellement
# pour éviter que laravel stoppe tout le démarrage

# ==============================
# DOSSIERS & PERMISSIONS
# ==============================
mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache
mkdir -p storage/app/public/logos/institutions

chown -R www-data:www-data storage bootstrap/cache
chmod -R 777 storage bootstrap/cache

# ==============================
# LARAVEL SETUP
# ==============================
echo "=== LARAVEL SETUP ==="
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

# ==============================
# MIGRATIONS
# ==============================
echo "=== MIGRATIONS ==="
php artisan migrate --force || true

# ==============================
# SESSION CLEANUP
# ==============================
echo "=== SESSION CLEANUP ==="
rm -f storage/framework/sessions/* 2>/dev/null || true

# ==============================
# CONFIG CACHE
# ==============================
echo "=== CONFIG CACHE ==="
php artisan config:cache || true

# ==============================
# STORAGE LINK
# ==============================
echo "=== STORAGE LINK ==="
php artisan storage:link --force || true

# ==============================
# TEST CONNEXION DB
# ==============================
echo "=== TEST CONNEXION DB ==="
php -r "
try {
    \$pdo = new PDO(
        'pgsql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    echo 'DB OK\n';
} catch(Exception \$e) {
    echo 'DB ERREUR: ' . \$e->getMessage() . '\n';
}
" 2>&1

# ==============================
# VÉRIFICATION TABLE SESSIONS
# ==============================
echo "=== CHECK TABLE SESSIONS ==="
php artisan tinker --execute="
try {
    \$count = \DB::table('sessions')->count();
    echo 'Sessions en base : ' . \$count . PHP_EOL;
} catch(\Exception \$e) {
    echo 'ERREUR sessions : ' . \$e->getMessage() . PHP_EOL;
}
" 2>&1 || true

# ==============================
# DEMARRER APACHE
# ==============================
echo "=== STARTING APACHE ==="
apache2-foreground
