# ==============================
# 1. IMAGE BASE
# ==============================
FROM php:8.2-apache

# ==============================
# 2. INSTALL SYSTEM DEPENDENCIES
# ==============================
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    zip \
    libonig-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql pgsql mbstring zip exif pcntl bcmath gd

# ==============================
# 3. ENABLE APACHE MOD REWRITE
# ==============================
RUN a2enmod rewrite

# ==============================
# 4. SET WORKDIR
# ==============================
WORKDIR /var/www/html

# ==============================
# 5. COPY PROJECT FILES
# ==============================
COPY . .

# ==============================
# 6. INSTALL COMPOSER
# ==============================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ==============================
# 7. INSTALL PHP DEPENDENCIES
# ==============================
RUN composer install --no-dev --optimize-autoloader

# ==============================
# 8. FIX LARAVEL STORAGE & CACHE PATHS
# ==============================
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

# ==============================
# 9. PERMISSIONS
# ==============================
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# ==============================
# 11. APACHE CONFIG (POINT TO PUBLIC FOLDER)
# ==============================
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN echo "LimitRequestBody 57671680" >> /etc/apache2/apache2.conf

# ==============================
# 12. PHP UPLOAD LIMITS
# ==============================
RUN echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=55M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit=128M" >> /usr/local/etc/php/conf.d/uploads.ini

# ==============================
# 13. EXPOSE PORT
# ==============================
EXPOSE 10000

# ==============================
# 14. ENTRYPOINT
# ==============================
COPY docker-entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

CMD ["/usr/local/bin/entrypoint.sh"]
