# phpvms <sup>7</sup>

[![Build Status](https://travis-ci.org/nabeelio/phpvms.svg)](https://travis-ci.org/nabeelio/phpvms) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/d668bebb0a3c46bda381af16ce3d9450)](https://www.codacy.com/app/nabeelio/phpvms?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=nabeelio/phpvms&amp;utm_campaign=Badge_Grade) [![Total Downloads](https://poser.pugx.org/nabeel/phpvms/downloads)](https://packagist.org/packages/nabeel/phpvms) [![Latest Stable Version](https://poser.pugx.org/nabeel/phpvms/v/stable)](https://packagist.org/packages/nabeel/phpvms) [![Latest Unstable Version](https://poser.pugx.org/nabeel/phpvms/v/unstable)](https://packagist.org/packages/nabeel/phpvms) [![License](https://poser.pugx.org/nabeel/phpvms/license)](https://packagist.org/packages/nabeel/phpvms)

The next phpvms version built on the laravel framework. work in progress. If you're looking for 
the old, phpVMS classic, it's [available here](https://github.com/nabeelio/phpvms_v2).

# installation

A full distribution, with all of the composer dependencies, is available at this 
[tarball link](http://phpvms.net/downloads/phpvms-7.0.0-master.tar.gz). It's currently 
updated with every commit

### Composer Access

run the following commands. for right now, we're running on sqlite. for mysql, set 
`DB_CONNECTION` to `mysql` in the `env.php` file.

```bash
cp env.php.example env.php
composer install --no-interaction
php artisan database:create
php artisan migrate:refresh --seed
```

then point your webserver to the `/public` folder.

## development environment

For development, copy the included `env.php.example` to `env.php` file. By default, it uses sqlite
instead of mysql. This makes it much easier to be able to clear the database and new fixtures.

The easiest way to load locally is to install [Laravel Valet](https://laravel.com/docs/5.5/valet) 
(if you're running a Mac). Once you install it, go to your phpvms directory, and run:

```bash
cp env.php.example env.php
php artisan key:generate
make install   # this will install everything
valet link phpvms
```

Now going to [http://phpvms.dev](http://phpvms.dev) should work. If you want to use mysql,
follow the valet directions on installing mysql (`brew install mysql`) and then update the
`env.php` file to point to the mysql.

The default username and password are "admin@phpvms.net" and "admin". 
To see the available users in the development environment, [see this file](https://github.com/nabeelio/phpvms/blob/master/database/seeds/dev.yml#L10) 

### creating/resetting the environment

I use Makefiles to be able to quickly setup the environment.

```bash
# to do an initial setup of the composer deps and install the DB
make install
```

Then to reset the database/clear cache, use:

```bash
make reset
```

### database seeding

There is a `database/seeds/dev.yml` which contains the initial seed data that can be used
for testing. For production use, there is a `prod.yml` file. The `make reset` handles seeding
the database with the data from the `dev.yml`.

# updating

extract files and run the migrations:

```bash
php artisan migrate
```
