#
#
# Create the phpvms database if needed:
# docker exec phpvms /usr/bin/mysql -uroot -e 'CREATE DATABASE phpvms'
SHELL := /bin/bash
COMPOSER ?= $(shell which composer)

PKG_NAME := "/tmp"
CURR_PATH=$(shell pwd)

.PHONY: all
all: install

.PHONY: clean
clean:
	@php artisan cache:clear
	@php artisan route:clear
	@php artisan config:clear
	@php artisan view:clear
	@find bootstrap/cache -type f -not -name '.gitignore' -print0 | xargs -0 rm -rf

	@find storage/framework/cache/ -mindepth 1 -not -name '.gitignore' -print0 | xargs -0 rm -rf
	@find storage/framework/sessions/ -mindepth 1 -type f -not -name '.gitignore' -print0 | xargs -0 rm -rf
	@find storage/framework/views/ -mindepth 1 -not -name '.gitignore' -print0 | xargs -0 rm -rf

	@find storage/logs -mindepth 1 -not -name '.gitignore' -print0 | xargs -0 rm -rf

.PHONY: clean-routes
clean-routes:
	@php artisan route:clear

.PHONY:  build
build:
	@php $(COMPOSER) install --no-interaction

# This is to build all the stylesheets, etc
.PHONY: build-assets
build-assets:
	yarn run dev

.PHONY: install
install: build
	@php artisan database:create
	@php artisan migrate --seed
	@echo "Done!"

.PHONY: update
update: build
	@php $(COMPOSER) dump-autoload
	@php $(COMPOSER) update --no-interaction
	@php artisan migrate --force
	@echo "Done!"

.PHONY: reset
reset: clean
	@php $(COMPOSER) dump-autoload
	@make reload-db

.PHONY: reload-db
reload-db:
	@php artisan phpvms:dev-install --reset-db

.PHONY: tests
tests: test

.PHONY: test
test:
	#php artisan database:create --reset
	vendor/bin/phpunit --debug --verbose

.PHONY: phpcs
phpcs:
	@vendor/bin/php-cs-fixer fix --config=.php_cs -v --diff --dry-run

#.PHONY: phpstan
#phpstan:
#	vendor/bin/phpstan analyse -c phpstan.neon -v --level 2 app

.PHONY: replay-acars
replay-acars:
	#@php artisan phpvms:replay AAL10,AAL3113,BAW172,DAL988,FIN6,MSR986 --manual
	@php artisan phpvms:replay ASH6028 --manual

.PHONY: sass-watch
sass-watch:
	sass --watch public/assets/admin/sass/paper-dashboard.scss:public/assets/admin/css/paper-dashboard.css

.PHONY: schema
schema:
	#php artisan infyom:scaffold Aircraft --fieldsFile=database/schema/aircraft.json
	echo ""

.PHONY: deploy-package
deploy-package:
	./.travis/deploy_script.sh

.PHONY: reset-installer
reset-installer:
	@php artisan database:create --reset
	@php artisan migrate:refresh --seed

.PHONY: docker
docker:
	@mkdir -p $(CURR_PATH)/tmp/mysql

	-docker rm -f phpvms
	docker build -t phpvms .
	docker run --name=phpvms \
       -v $(CURR_PATH):/var/www/ \
       -v $(CURR_PATH)/tmp/mysql:/var/lib/mysql \
       -p 8080:80 \
       phpvms

.PHONY: docker-clean
docker-clean:
	-docker stop phpvms
	-docker rm -rf phpvms
	-rm core/local.config.php
	-rm -rf tmp/mysql
