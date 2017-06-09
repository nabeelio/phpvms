#
#
# Create the phpvms database if needed:
# docker exec phpvms /usr/bin/mysql -uroot -e 'CREATE DATABASE phpvms'

CURR_PATH=$(shell pwd)

all: build


build:
	composer install

install:
	echo ""

db:
	sqlite3 tmp/database.sqlite ""
	php artisan migrate

reset-db:
	rm tmp/database.sqlite
	make db

schema:
	#php artisan infyom:scaffold Airlines --fieldsFile=database/schema/airlines.json
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
