# -----------------------------
# Étape 1 : Builder les assets avec Node (Vite)
# -----------------------------
FROM node:18 AS node_builder
WORKDIR /app

# Copier package.json et vite.config
COPY package*.json vite.config.* ./
RUN npm install

# Copier le reste du projet et builder Vite
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
    libzip-dev \
    zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail Laravel
WORKDIR /var/www/html

# Copier le projet Laravel
COPY . .

# Copier les assets buildés par Vite
COPY --from=node_builder /app/public/build ./public/build

# Créer .env si absent
RUN cp .env.example .env || true

# Installer dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Générer la clé Laravel
RUN php artisan key:generate

# Donner les permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port du serveur Laravel
EXPOSE 8000

# Lancer le serveur intégré Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000
