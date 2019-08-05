# phpvms <sup>7</sup>

[![Build Status](https://travis-ci.org/nabeelio/phpvms.svg)](https://travis-ci.org/nabeelio/phpvms) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/d668bebb0a3c46bda381af16ce3d9450)](https://www.codacy.com/app/nabeelio/phpvms?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=nabeelio/phpvms&amp;utm_campaign=Badge_Grade) [![Latest Stable Version](https://poser.pugx.org/nabeel/phpvms/v/stable)](https://packagist.org/packages/nabeel/phpvms) ![StyleCI](https://github.styleci.io/repos/93688482/shield?branch=dev) [![License](https://poser.pugx.org/nabeel/phpvms/license)](https://packagist.org/packages/nabeel/phpvms)

The next phpvms version built on the laravel framework. work in progress. The latest documentation, with installation instructions is available 
                                                                          [on the phpVMS documentation](http://docs.phpvms.net/) page.

# installation

A full distribution, with all of the composer dependencies, is available at this 
[GitHub Releases](https://github.com/nabeelio/phpvms/releases) link. 



## Requirements

- PHP 7.1+, extensions:
  - cURL
  - JSON
  - mbstring
  - openssl
  - pdo
  - tokenizer
- Database:
  - MySQL 5.5+ (or MySQL variant, including MariaDB and Percona)

[View more details on requirements](http://docs.phpvms.net/setup/requirements)

## Installer

1. Upload to your server
1. Visit the site, and follow the link to the installer

[View installation details](http://docs.phpvms.net/setup/installation)

# development environment

A full development environment can be brought up using Docker:

```bash
composer install
yarn install
docker-compose build
docker-compose up
```

Then go to `http://localhost`. If you're using dnsmasq, the `app` container is listening on `phpvms.test`, or you can add to your `/etc/hosts` file:

```
127.0.0.1   phpvms.test
```

## Building JS/CSS assets

Yarn is required, run:

```bash
make build-assets
```

This will build all of the assets according to the webpack file.
