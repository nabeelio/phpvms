FROM php:7.3-fpm

RUN docker-php-ext-install \
    mysqli \
    pdo \
    openssl \
    mbstring \
    tokenizer \
    curl \
    json \
    gmp

RUN ln -sf /dev/stderr /var/log/fpm-error.log
