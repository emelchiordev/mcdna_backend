FROM composer:2.3.8 as composer_build

WORKDIR /app
COPY . /app
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs --no-interaction --no-scripts --prefer-dist \
    && composer require annotations

FROM php:8.1.8-apache
RUN apt-get update
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql
ENV APP_HOME /var/www/html
COPY --from=composer_build /app/ /var/www/html/
RUN sed -i -e "s/html/html\/public/g" /etc/apache2/sites-enabled/000-default.conf \
    && usermod -u 1000 www-data && groupmod -g 1000 www-data \
    && chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite  \
    && cp public/.htaccess /var/www/html/public/
ENTRYPOINT []
CMD docker-php-entrypoint apache2-foreground