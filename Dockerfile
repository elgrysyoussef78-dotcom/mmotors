FROM php:8.2-cli

# Installer les extensions PHP nécessaires (dont pdo_mysql)
RUN docker-php-ext-install pdo pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app

# Installer les dépendances
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-interaction

# Démarrer le serveurz
CMD php -S 0.0.0.0:$PORT -t public
