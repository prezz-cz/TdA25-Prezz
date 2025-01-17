#!/bin/sh

chown mysql:mysql /var/lib/mysql

mysql_install_db --user=mysql --datadir=/var/lib/mysql >/dev/null # otherwise database can't start on first run
mysqld_safe &
sleep 1

# bash; # for debug

echo "Waiting for /run/mysqld/mysqld.sock ..."
while [ ! -S /run/mysqld/mysqld.sock ]; do sleep 1; done
echo "mysqld started"

echo "CREATE DATABASE IF NOT EXISTS laravel" | mysql

/app/artisan migrate
/app/artisan serve --port=80 --host=0.0.0.0
