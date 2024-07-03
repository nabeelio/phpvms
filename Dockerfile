FROM dunglas/frankenphp

# Be sure to replace "demo.phpvms.net" by your domain name
ENV SERVER_NAME=:80

# Enable PHP production settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN install-php-extensions \
	pdo_mysql \
	gd \
	intl \
	zip \
	opcache \
    curl \
    mbstring \
    json \
    bcmath \
    gmp \
    zip \
    redis

# Copy the PHP files of your project in the container
COPY . /app
