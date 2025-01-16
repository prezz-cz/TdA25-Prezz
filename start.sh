#!/bin/sh

service mariadb start
echo "CREATE DATABASE IF NOT EXISTS laravel" | mysql
cd /app && composer install
cp -r /app/front-end/build/* /app/public
/app/artisan migrate
/app/artisan serve --port=80 --host=0.0.0.0

# cd ../front-end
# npm start & # v pozadi

# caddy run