#
#
# Create the phpvms database if needed:
# docker exec phpvms /usr/bin/mysql -uroot -e 'CREATE DATABASE phpvms'

CURR_PATH=$(shell pwd)

all: build


build:
	composer install --no-interaction
	@make db

install:
	echo ""

db:
	sqlite3 database/testing.sqlite ""
	php artisan migrate

reset-db:
	rm database/testing.sqlite
	make db

schema:
	#php artisan infyom:scaffold Airlines --fieldsFile=database/schema/airlines.json
	php artisan infyom:scaffold Aircraft --fieldsFile=database/schema/aircraft.json
	echo ""

docker:
	@mkdir -p $(CURR_PATH)/tmp/mysql

	-docker rm -f phpvms
	docker build -t phpvms .
	docker run --name=phpvms \
       -v $(CURR_PATH):/var/www/ \
       -v $(CURR_PATH)/tmp/mysql:/var/lib/mysql \
       -p 8080:80 \
       phpvms

docker-clean:
	-docker stop phpvms
	-docker rm -rf phpvms
	-rm core/local.config.php
	-rm -rf tmp/mysql

.PHONY: all build install db reset-db schema docker docker-clean
