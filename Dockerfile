# Utilise l’image officielle PHP avec Apache
FROM php:8.2-apache

# Installe les extensions nécessaires à Laravel
RUN apt-get update && apt-get install -y \
    git zip unzip libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie tout le code dans le conteneur
COPY . /var/www/html

# Utilise le fichier de config Apache
COPY ./vhost.conf /etc/apache2/sites-available/000-default.conf

# Active le module Apache rewrite (utile pour les routes Laravel)
RUN a2enmod rewrite

# Donne les bons droits à Laravel
RUN chown -R www-data:www-data /var/www/html/storage

# Positionne le répertoire de travail
WORKDIR /var/www/html

# Installe les dépendances Laravel sans les dev
RUN composer install --no-dev --optimize-autoloader

# Génère une clé (ignore les erreurs si déjà générée)
RUN php artisan key:generate || true

# Expose le port 80 pour que Render sache où écouter
EXPOSE 80
