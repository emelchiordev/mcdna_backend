FROM composer:2.3.8 as composer_build

WORKDIR /app
COPY . /app
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs --no-interaction --no-scripts --prefer-dist \
    && composer require annotations

FROM php:8.1.8-apache
RUN docker-php-ext-install bcmath && docker-php-ext-enable bcmath && \
    docker-php-ext-install calendar && docker-php-ext-enable calendar && \
    docker-php-ext-install gd && docker-php-ext-enable gd && \
    docker-php-ext-install mcrypt && docker-php-ext-enable mcrypt && \
    docker-php-ext-install pdo_mysql && docker-php-ext-enable pdo_mysql && \
    docker-php-ext-install mysqli && docker-php-ext-enable mysqli && \
    docker-php-ext-install soap && docker-php-ext-enable soap && \
    docker-php-ext-install sockets && docker-php-ext-enable sockets && \
    docker-php-ext-install exif && docker-php-ext-enable exif && \
    docker-php-ext-install wddx && docker-php-ext-enable wddx && \
    docker-php-ext-install wmlrpc && docker-php-ext-enable wmlr
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