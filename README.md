![](http://i.imgur.com/bMh1xn6.png)

# phpvms <sup>4</sup>

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/nabeelio/phpvms_next) [![Total Downloads](https://poser.pugx.org/nabeel/phpvms/downloads)](https://packagist.org/packages/nabeel/phpvms) [![Latest Stable Version](https://poser.pugx.org/nabeel/phpvms/v/stable)](https://packagist.org/packages/nabeel/phpvms) [![Latest Unstable Version](https://poser.pugx.org/nabeel/phpvms/v/unstable)](https://packagist.org/packages/nabeel/phpvms) [![License](https://poser.pugx.org/nabeel/phpvms/license)](https://packagist.org/packages/nabeel/phpvms)

The next phpvms version built on the laravel framework. work in progress. If you're looking for the old, phpVMS classic, it's [available here](https://github.com/nabeelio/phpvms_v2).

# installation

run the following commands. for right now, we're running on sqlite. for mysql, set `DB_CONNECTION` to `mysql` in the `.env` file, and skip the `sqlite3` step below.

```bash
cp .env.example .env
composer install --no-interaction
sqlite3 database/testing.sqlite ""
php artisan migrate:refresh --seed
```

then point your webserver to the `/public` folder. for example, in nginx:

```
server {
    listen 80 default_server;
    listen [::]:80 default_server ipv6only=on;

    root /var/www/laravel/public;
    index index.php index.html index.htm;

    server_name localhost;

    location / {
            try_files $uri $uri/ =404;
    }
}
```

see [this article](https://www.digitalocean.com/community/tutorials/how-to-install-laravel-with-an-nginx-web-server-on-ubuntu-14-04) for more detailed instructions.

(TODO: redis information, etc)

# updating

extract files and run the migrations:

```bash
php artisan migrate
```
