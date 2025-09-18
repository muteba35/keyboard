FROM php:8.2-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier les fichiers
WORKDIR /var/www/html
COPY . .

# Installer les dépendances Laravel (après gd installé)
RUN composer install --no-dev --optimize-autoloader

# Générer la clé Laravel (si besoin)
RUN php artisan key:generate --ansi || true

CMD ["php-fpm"]
