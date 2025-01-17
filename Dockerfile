FROM oven/bun:alpine AS front-end
COPY ./front-end /front-end
WORKDIR /front-end
RUN bun install
RUN bun run build

FROM joseluisq/php-fpm:8.1
WORKDIR /app

# RUN apk add --no-cache --update nodejs npm
RUN apk add --update --no-cache bash mariadb mariadb-client \
    && rm -rf /var/cache/apk/*

COPY . .

RUN composer install --optimize-autoloader --no-dev --prefer-dist --no-suggest

COPY --from=front-end /front-end/dist/ /app/public/
COPY --from=front-end /front-end/dist/assets /app/public/

EXPOSE 80
VOLUME [ "/var/lib/mysql" ]

CMD ["/app/start.sh"]
