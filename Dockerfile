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
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev \
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

# Copier l'env spécifique Docker et le renommer en .env
COPY .env.docker .env

# Installer dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Générer clé Laravel si elle n'existe pas
RUN php artisan key:generate

# Exécuter les migrations (uniquement celles qui n'ont pas encore été exécutées)
RUN php artisan migrate --force

# Donner permissions aux dossiers critiques
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port Laravel
EXPOSE 8000

# Lancer le serveur intégré Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
