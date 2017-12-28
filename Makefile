#
#
# Create the phpvms database if needed:
# docker exec phpvms /usr/bin/mysql -uroot -e 'CREATE DATABASE phpvms'
SHELL := /bin/bash

PKG_NAME := "/tmp"
CURR_PATH=$(shell pwd)

.PHONY: all
all: install

.PHONY: clean
clean:
	@find bootstrap/cache -type f -not -name '.gitignore' -print0 -delete
	@find storage/app/public -type f -not -name '.gitignore' -print0 -delete
	@find storage/app -type f -not -name '.gitignore' -not -name public -print0 -delete
	@find storage/framework/cache -type f -not -name '.gitignore' -print0 -delete
	@find storage/framework/sessions -type f -not -name '.gitignore' -print0 -delete
	@find storage/framework/views -type f -not -name '.gitignore' -print0 -delete
	@find storage/logs -type f -not -name '.gitignore' -print0 -delete
	@php artisan cache:clear
	@php artisan route:clear
	@php artisan config:clear
	@php artisan view:clear

.PHONY: clean-routes
clean-routes:
	@php artisan route:clear

.PHONY:  build
build:
	@php composer.phar install --no-interaction

# This is to build all the stylesheets, etc
.PHONY: build-assets
build-assets:
	npm update
	npm run dev

.PHONY: install
install: build
	@php artisan database:create
	@php artisan migrate --seed
	@echo "Done!"

.PHONY: update
update: build
	@php composer.phar dump-autoload
	@php composer.phar update --no-interaction
	@php artisan migrate --force
	@echo "Done!"

.PHONY: reset
reset: clean
	@php composer.phar dump-autoload
	@make reload-db

.PHONY: reload-db
reload-db:
	@php artisan database:create --reset
	@php artisan migrate:refresh --seed
	@php artisan phpvms:navdata

.PHONY: tests
tests: test

.PHONY: test
test:
	#php artisan database:create --reset
	vendor/bin/phpunit --debug --verbose

.PHONY: replay-acars
replay-acars:
	@php artisan phpvms:replay AAL10,AAL3113,BAW172,DAL988,FIN6,MSR986 --manual

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
	@cp .env.dev.example .env
	@make clean
	mysql -uroot -e "DROP DATABASE IF EXISTS phpvms"
	mysql -uroot -e "CREATE DATABASE phpvms"

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
