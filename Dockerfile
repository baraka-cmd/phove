FROM php:8.2-apache

# ─────────────────────────────────────────────
# 1. Dépendances système + extensions PHP
# ─────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev \
    libjpeg-dev libfreetype6-dev libwebp-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# ─────────────────────────────────────────────
# 2. Composer
# ─────────────────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ─────────────────────────────────────────────
# 3. Apache : pointer sur /public
# ─────────────────────────────────────────────
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite

WORKDIR /var/www/html

# ─────────────────────────────────────────────
# 4. Copier le code source
# ─────────────────────────────────────────────
COPY . .

# ─────────────────────────────────────────────
# 5. ⭐ INSTALLER LES DÉPENDANCES COMPOSER
# ─────────────────────────────────────────────
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# ─────────────────────────────────────────────
# 6. Permissions
# ─────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ─────────────────────────────────────────────
# 7. Configurer le port dynamique de Render
# ─────────────────────────────────────────────
RUN sed -ri -e 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf
RUN sed -ri -e 's/:80/:${PORT}/g' /etc/apache2/sites-available/*.conf

EXPOSE 10000