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
    RUN echo "DirectoryIndex index.php" >> /etc/apache2/mods-enabled/dir.conf \
    && echo "<IfModule mod_negotiation.c>" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "    Options -MultiViews" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "</IfModule>" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "<IfModule mod_rewrite.c>" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "    RewriteEngine On" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "    RewriteCond %{REQUEST_URI}::$0 ^(/.+)/(.*)::\2$" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "    RewriteRule .* - [E=BASE:%1]" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "    RewriteCond %{HTTP:Authorization} .+" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "    RewriteRule ^ - [E=HTTP_AUTHORIZATION:%0]" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "    RewriteCond %{ENV:REDIRECT_STATUS} =''" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "    RewriteRule ^index\.php(?:/(.*)|$) %{ENV:BASE}/$1 [R=301,L]" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "    RewriteCond %{REQUEST_FILENAME} !-f" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "    RewriteRule ^ %{ENV:BASE}/index.php [L]" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "</IfModule>" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "<IfModule !mod_rewrite.c>" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "    <IfModule mod_alias.c>" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "        RedirectMatch 307 ^/$ /index.php/" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "    </IfModule>" >> /etc/apache2/sites-enabled/000-default.conf \
    && echo "</IfModule>" >> /etc/apache2/sites-enabled/000-default.conf \
    && usermod -u 1000 www-data && groupmod -g 1000 www-data \
    && chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite 
ENTRYPOINT []
CMD docker-php-entrypoint apache2-foreground