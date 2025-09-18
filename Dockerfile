# Étape 1 : Builder les assets avec Node (Vite)
FROM node:18 AS node_builder
WORKDIR /app
COPY package*.json vite.config.* ./
RUN npm install
COPY . .
RUN npm run build

# Étape 2 : Image PHP avec Composer
FROM php:8.2-fpm

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip unzip git curl nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier Laravel
WORKDIR /var/www/html

# Copier projet
COPY . .

# Copier assets buildés par Vite
COPY --from=node_builder /app/public/build ./public/build

# Installer dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Donner les permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Supprimer config Nginx par défaut
RUN rm /etc/nginx/sites-enabled/default

# Ajouter config Nginx Laravel
COPY ./docker/nginx.conf /etc/nginx/conf.d/default.conf

# Lancer PHP-FPM + Nginx
CMD service nginx start && php-fpm
