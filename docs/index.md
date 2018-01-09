# phpvms <sup>7</sup>

[![Build Status](https://travis-ci.org/nabeelio/phpvms.svg)](https://travis-ci.org/nabeelio/phpvms) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/d668bebb0a3c46bda381af16ce3d9450)](https://www.codacy.com/app/nabeelio/phpvms?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=nabeelio/phpvms&amp;utm_campaign=Badge_Grade) [![Total Downloads](https://poser.pugx.org/nabeel/phpvms/downloads)](https://packagist.org/packages/nabeel/phpvms) [![Latest Stable Version](https://poser.pugx.org/nabeel/phpvms/v/stable)](https://packagist.org/packages/nabeel/phpvms) [![Latest Unstable Version](https://poser.pugx.org/nabeel/phpvms/v/unstable)](https://packagist.org/packages/nabeel/phpvms) [![License](https://poser.pugx.org/nabeel/phpvms/license)](https://packagist.org/packages/nabeel/phpvms)

The next phpvms version built on the laravel framework. This is a separate version from the old v2/v5 classic version.

# installation

## Requirements

- PHP 7.0+, extensions:
  - cURL
  - JSON
  - mbstring
  - openssl
  - pdo
  - tokenizer
- Database:
  - MySQL (or MySQL variant, including MariaDB, Percona)
  - SQLite (for testing)
  - Postgres is supported by Laravel but not enabled/tested for phpVMS, yet
- Apache or Nginx
- Redis (optional, for job queuing, various optimizations)

## Download

A full distribution, with all of the composer dependencies, is available at this 
[tarball link](http://phpvms.net/downloads/phpvms-7.0.0-master.tar.gz). It's currently 
updated with every commit

## Upload the files

If you're on shared hosting, just upload all of the files. If you have your own server, it's 
recommended to create a vhost that points to the `/public` directory. ([see laravel's installation docs](https://laravel.com/docs/5.5/installation#web-server-configuration))

## Browse to the site

Once you browse to the site, you will be given a link to the installer (`/install`) (Note: there
isn't a separate `install` folder). If the installation with the vhost and/or htaccess is working
correctly, you'll be able to see the installer. 

Follow the instructions to complete the install.
