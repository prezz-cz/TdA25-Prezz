FROM debian:bookworm


RUN apt-get update && apt-get install -y \
    git \
    lsb-release \
    ca-certificates \
    curl \
    php-common \
    mariadb-server \
    nodejs \
    npm

RUN curl -sSLo /tmp/debsuryorg-archive-keyring.deb https://packages.sury.org/debsuryorg-archive-keyring.deb
RUN dpkg -i /tmp/debsuryorg-archive-keyring.deb
RUN sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'

RUN apt-get update && apt-get install -y \
    php8.1 \
    php8.1-mysql \
    php8.1-mbstring \
    php8.1-bcmath \
    php8.1-dom \
    php8.1-xml \
    php8.1-curl \
    php8.1-ctype

RUN apt clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# COPY --from=caddy:latest /usr/bin/caddy /usr/bin/caddy

COPY . /app
# COPY caddy/Caddyfile /etc/caddy/Caddyfile

WORKDIR /app/front-end
RUN npm install
RUN npm install react-router-dom axios
RUN npm run build

WORKDIR /app
RUN composer install

EXPOSE 80

CMD ["/app/start.sh"]