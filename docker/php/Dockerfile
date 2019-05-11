FROM php:7.3-fpm

RUN apt-get update
RUN apt-get install -y libgmp-dev

RUN ln -sf /dev/stderr /var/log/fpm-error.log

RUN docker-php-ext-install \
  mysqli \
  pdo \
  pdo_mysql \
  gmp
