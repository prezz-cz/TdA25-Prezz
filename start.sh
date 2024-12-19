#!/bin/sh

service mariadb start
echo "CREATE DATABASE IF NOT EXISTS laravel" | mysql
cd /app && composer install
/app/artisan migrate
/app/artisan serve --port=80 --host=0.0.0.0
