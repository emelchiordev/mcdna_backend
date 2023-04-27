FROM composer:2.3.8 as composer_build

WORKDIR /app
COPY . /app
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs --no-interaction --no-scripts --prefer-dist \
    && composer require annotations


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

# Activer les modules Apache nécessaires pour Symfony
RUN a2enmod rewrite
RUN a2enmod headers

# Définissez l'emplacement de l'application dans le conteneur Docker
ENV APP_HOME /var/www/html

# Copiez l'application dans le conteneur Docker
COPY . $APP_HOME

# Définissez les autorisations correctes pour l'application
RUN chown -R www-data:www-data $APP_HOME \
    && chmod -R 755 $APP_HOME

# Exposez le port 80 pour que l'application soit accessible depuis l'hôte
EXPOSE 80