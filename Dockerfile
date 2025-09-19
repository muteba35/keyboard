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

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev \
    zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Copier tout le dossier storage (Firebase credentials inclus)
COPY storage ./storage

# Copier ton fichier env.local et le renommer en .env dans le conteneur
COPY env.local .env

# Copier les assets buildés
COPY --from=node_builder /app/public/build ./public/build

# Installer dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Générer clé Laravel
RUN php artisan key:generate

# Exécuter seulement les migrations déjà présentes
RUN php artisan migrate --force

# Donner permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
