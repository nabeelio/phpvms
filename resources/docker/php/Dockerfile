FROM php:7.3-fpm-alpine

#RUN apt-get update
#RUN apt-get install -y libgmp-dev
RUN apk add gmp-dev

# Copy any config files in
COPY ext-opcache.ini $PHP_INI_DIR/conf.d/

RUN ln -sf /dev/stderr /var/log/fpm-error.log

RUN docker-php-ext-install \
  calendar \
  pdo_mysql \
  gmp \
  opcache
