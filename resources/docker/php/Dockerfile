FROM php:7.4-fpm-alpine

RUN apk add make gmp-dev

# Copy any config files in
COPY ext-opcache.ini $PHP_INI_DIR/conf.d/

RUN ln -sf /dev/stderr /var/log/fpm-error.log

RUN docker-php-ext-install \
  calendar \
  pdo_mysql \
  gmp \
  opcache
