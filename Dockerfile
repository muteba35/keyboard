# Étape 1 : Builder les assets avec Node (Vite)
FROM node:18 AS node_builder
WORKDIR /app
COPY package*.json vite.config.* ./
RUN npm install
COPY . .
RUN npm run build

# Étape 2 : Image PHP avec Composer + Nginx
FROM php:8.2-fpm

# Installer dépendances système et Nginx
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip unzip git curl nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail Laravel
WORKDIR /var/www/html

# Copier le projet Laravel
COPY . .

# Copier les assets buildés par Vite
COPY --from=node_builder /app/public/build ./public/build

# Installer dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Donner les permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Supprimer les configs Nginx par défaut
RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf

# Créer une config Nginx directement dans le Dockerfile
RUN echo 'server { \
    listen 80; \
    index index.php index.html; \
    server_name localhost; \
    root /var/www/html/public; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$$ { \
        include snippets/fastcgi-php.conf; \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
    location ~ /\.ht { \
        deny all; \
    } \
}' > /etc/nginx/conf.d/default.conf

# Exposer le port
EXPOSE 80

# Lancer PHP-FPM et Nginx
CMD service nginx start && php-fpm
