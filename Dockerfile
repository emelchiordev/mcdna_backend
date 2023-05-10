FROM composer:2.3.8 as composer_build

WORKDIR /app
COPY . /app
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs --no-interaction --no-scripts --prefer-dist \
    && composer require annotations


FROM php:8.1-apache
RUN a2enmod rewrite

COPY env_vars.env /etc/env_vars.env


RUN apt-get update \
    && apt-get install -y libzip-dev git wget --no-install-recommends \
    && apt-get clean \
    && apt-get install -y libpq-dev \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install pdo pdo_pgsql mysqli pdo_mysql zip


#RUN wget https://getcomposer.org/download/2.0.9/composer.phar \ 
#    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer

COPY symfony.conf /etc/apache2/sites-enabled/000-default.conf
COPY ./entrypoint.sh /entrypoint.sh

COPY . /var/www/html

WORKDIR /var/www/html

COPY --from=composer_build /app/ /var/www/html/
# Génération de la paire de clés Lexik JWT
RUN php bin/console lexik:jwt:generate-keypair

RUN mkdir -p var/cache/prod/pools/system && \
    chown -R www-data var/cache/prod && \
    chmod -R 777 var/cache/prod

RUN chown -R www-data /var/www/html/public/images && \
    chmod -R 777 /var/www/html/public/images

# … cut for readability

RUN chmod +x /entrypoint.sh

CMD ["apache2-foreground"]

ENTRYPOINT ["/entrypoint.sh"]
