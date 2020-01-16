FROM php:7.4-fpm-alpine

WORKDIR /var/www/

RUN apk add gmp-dev
RUN curl --silent --show-error https://getcomposer.org/installer | php

# Copy any config files in
COPY resources/docker/php/ext-opcache.ini $PHP_INI_DIR/conf.d/
COPY resources/docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
RUN ln -sf /dev/stderr /var/log/fpm-error.log

RUN docker-php-ext-install \
  calendar \
  pdo_mysql \
  gmp \
  opcache && \
  docker-php-ext-enable pdo_mysql opcache

COPY . /var/www/
RUN php composer.phar install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

EXPOSE 9000
