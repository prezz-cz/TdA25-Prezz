FROM tourdeapp/php-8.1

WORKDIR /app

COPY . /app

EXPOSE 80

CMD ["/app/start.sh"]
