# -----------------------------
# Étape 1 : Builder les assets avec Node (Vite)
# -----------------------------
FROM node:18 AS node_builder
WORKDIR /app

COPY package*.json vite.config.* ./
RUN npm install

COPY . .
RUN npm run build

# -----------------------------
# Étape 2 : Image PHP avec Laravel
# -----------------------------
FROM php:8.2-cli

# Installer extensions PHP et dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier projet Laravel
COPY . .

# Copier assets buildés par Vite
COPY --from=node_builder /app/public/build ./public/build

# Créer .env si absent
RUN cp .env.example .env || true

# Installer dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Générer clé Laravel
RUN php artisan key:generate

# Créer les tables pour les sessions et le cache
RUN php artisan session:table && php artisan cache:table && php artisan migrate --force

# Donner permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port
EXPOSE 8000

# Lancer le serveur Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000
