FROM composer:2.3.8 as composer_build

WORKDIR /app
COPY . /app
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs --no-interaction --no-scripts --prefer-dist \
    && composer require annotations

RUN composer require symfony/requirements-checker

RUN php bin/console cache:clear


# Utilisez une version récente de PHP avec Apache
FROM php:8.1.8-apache

# Installez les dépendances nécessaires pour installer les extensions PHP
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev


# Installez les extensions PHP nécessaires pour Symfony
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) intl pdo pdo_pgsql gd zip

# Copiez votre fichier de configuration Apache dans le conteneur
COPY apache2.conf /etc/apache2/sites-available/000-default.conf


# Définissez l'emplacement de l'application dans le conteneur Docker
ENV APP_HOME /var/www/html
COPY --from=composer_build /app/ /var/www/html/

# Copiez l'application dans le conteneur Docker
COPY . $APP_HOME

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data \
    && chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite 

ENTRYPOINT []
CMD docker-php-entrypoint apache2-foreground