FROM php:8.2-cli

# Installer les paquets systeme necessaires
RUN apt-get update && apt-get install -y unzip libzip-dev git \
    && docker-php-ext-install pdo pdo_mysql zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app

# Installer les dependances
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-interaction

# Demarrer le serveur
CMD ["sh", "-c", "php -S 0.0.0.0:$PORT -t public"]
