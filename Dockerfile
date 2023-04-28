FROM composer:2.3.8 as composer_build

WORKDIR /app
COPY . /app
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs --no-interaction --no-scripts --prefer-dist \
    && composer require annotations

RUN apt-get update && apt-get install -y \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

FROM php:8.1.8-apache
ENV APP_HOME /var/www/html
COPY --from=composer_build /app/ /var/www/html/
RUN sed -i -e "s/html/html\/public/g" /etc/apache2/sites-enabled/000-default.conf \
    && echo "LogLevel debug" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "ErrorLog /var/log/apache2/error.log" >> /etc/apache2/sites-enabled/000-default.conf \
    && usermod -u 1000 www-data && groupmod -g 1000 www-data \
    && chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite
ENTRYPOINT []
CMD docker-php-entrypoint apache2-foreground