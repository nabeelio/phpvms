FROM php:7.3-fpm

RUN apt-get update
RUN apt-get install -y libgmp-dev

# Copy any config files in
COPY ext-opcache.ini $PHP_INI_DIR/conf.d/

RUN ln -sf /dev/stderr /var/log/fpm-error.log

RUN docker-php-ext-install \
  mysqli \
  pdo \
  pdo_mysql \
  gmp \
  opcache
