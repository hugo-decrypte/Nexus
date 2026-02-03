# Étape 1 : installer les dépendances Composer
FROM composer:2 AS composer
WORKDIR /app

COPY src/composer.json ./
RUN composer install --no-dev --no-scripts --prefer-dist

COPY src/ .
RUN composer dump-autoload --optimize

# Étape 2 : image finale avec Apache
FROM php:8.2-apache

# Installer les dépendances système pour PostgreSQL + curl pour healthcheck
RUN apt-get update && apt-get install -y \
    libpq-dev \
    curl \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Configurer Apache pour permettre .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copier le code source
COPY src/ .

# Copier vendor depuis l'étape Composer (sans composer.json du stage pour éviter écrasement)
COPY --from=composer /app/vendor ./vendor

# S'assurer que les permissions sont correctes
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]