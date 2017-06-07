#
# after launch run
# docker exec phpvms /usr/bin/mysql -uroot -e 'CREATE DATABASE phpvms'

FROM nabeelio/docker-lemp:latest

RUN mkdir -p /var/run/mysqld

ENTRYPOINT ["/entrypoint.sh"]
