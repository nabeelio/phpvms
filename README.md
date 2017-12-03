# phpvms <sup>7</sup>

[![Build Status](https://travis-ci.org/nabeelio/phpvms.svg)](https://travis-ci.org/nabeelio/phpvms) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/d668bebb0a3c46bda381af16ce3d9450)](https://www.codacy.com/app/nabeelio/phpvms?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=nabeelio/phpvms&amp;utm_campaign=Badge_Grade) [![Total Downloads](https://poser.pugx.org/nabeel/phpvms/downloads)](https://packagist.org/packages/nabeel/phpvms) [![Latest Stable Version](https://poser.pugx.org/nabeel/phpvms/v/stable)](https://packagist.org/packages/nabeel/phpvms) [![Latest Unstable Version](https://poser.pugx.org/nabeel/phpvms/v/unstable)](https://packagist.org/packages/nabeel/phpvms) [![License](https://poser.pugx.org/nabeel/phpvms/license)](https://packagist.org/packages/nabeel/phpvms)

The next phpvms version built on the laravel framework. work in progress. If you're looking for 
the old, phpVMS classic, it's [available here](https://github.com/nabeelio/phpvms_v2).

# installation

run the following commands. for right now, we're running on sqlite. for mysql, set 
`DB_CONNECTION` to `mysql` in the `.env` file.

```bash
cp .env.dev.example .env
composer install --no-interaction
php artisan database:create
php artisan migrate:refresh --seed
```

then point your webserver to the `/public` folder. for example, in nginx:

```nginx
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

## development environment

For development, copy the included `.env.example` to `.env` file. By default, it uses sqlite
instead of mysql. This makes it much easier to be able to clear the database and new fixtures.

The default username and password are "admin@phpvms.net" and "admin". To see the available users in the development environment, [see this file](https://github.com/nabeelio/phpvms/blob/master/database/seeds/dev.yml#L10) 

### makefile commands

I use Makefiles to be able to quickly setup the environment.

```bash
# to do an initial setup of the composer deps and install the DB
make
```

Then to reset the database/clear cache, use:

```bash
make reset
```

### database seeding

There is a `database/seeds/dev.yml` which contains the initial seed data that can be used
for testing. For production use, there is a `prod.yml` file. The `make reset` handles seeding
the database with the data from the `dev.yml`.

### virtual machine

Using [Laravel Homestead](https://laravel.com/docs/5.4/homestead) is probably the easiest 
way to get this working. Follow their instructions for install. A `Vagrantfile` and `Homestead.yaml`
is included here. Add this to your `/etc/hosts`:

```bash
127.0.0.1       phpvms.app
```

And then to launch:

```bash
vagrant up
```

And then accessing it via `http://phpvms.app` should just work.

(TODO: redis information, etc)

# updating

extract files and run the migrations:

```bash
php artisan migrate
```

![](http://i.imgur.com/bMh1xn6.png)
