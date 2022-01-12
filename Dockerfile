FROM php:8.0-fpm-alpine

WORKDIR /var/www/

# Setup composer
COPY --from=composer:2.1.5 /usr/bin/composer /usr/local/bin/composer

RUN apk add gmp-dev icu-dev zlib-dev libpng-dev libzip-dev zip
RUN curl --silent --show-error https://getcomposer.org/installer | php

# Copy any config files in
COPY resources/docker/php/ext-opcache.ini $PHP_INI_DIR/conf.d/
COPY resources/docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
RUN ln -sf /dev/stderr /var/log/fpm-error.log

RUN docker-php-ext-install \
  calendar \
  intl \
  pdo_mysql \
  gd \
  gmp \
  bcmath \
  opcache \
  zip && \
  docker-php-ext-enable pdo_mysql opcache bcmath zip

RUN chown -R www-data:www-data /var/www

USER www-data:www-data

RUN mkdir /var/www/public \
    /var/www/tests \
    /var/www/modules \
    /var/www/config \
    /var/www/bootstrap \
    /var/www/app \
    /var/www/resources

COPY --chown=www-data:www-data \
    .htaccess \
    swagger.yml \
    composer.json \
    artisan \
    /var/www/

COPY --chown=www-data:www-data public /var/www/public
COPY --chown=www-data:www-data tests /var/www/test
COPY --chown=www-data:www-data modules /var/www/modules
COPY --chown=www-data:www-data config /var/www/config
COPY --chown=www-data:www-data bootstrap /var/www/bootstrap
COPY --chown=www-data:www-data app /var/www/app
COPY --chown=www-data:www-data resources /var/www/resources

RUN mkdir -p storage/app/public/avatars \
    storage/app/public/uploads \
    storage/app/import \
    storage/docker/mysql \
    storage/docker/redis \
    storage/debugbar \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/navdata \
    storage/replay

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

EXPOSE 9000
