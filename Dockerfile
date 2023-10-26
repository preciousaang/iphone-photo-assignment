FROM composer:2.5.8 as composer
FROM  php:8.1-fpm-alpine as base

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk update && apk add --no-cache wget nodejs npm  \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    libxml2-dev \
    freetype-dev

RUN   docker-php-ext-configure gd \
    --with-jpeg \
    --with-freetype && \
    docker-php-ext-install opcache \
    pdo \
    bcmath \
    pdo_mysql \
    gd \
    simplexml \
    zip



COPY ./composer.json /app/composer.json
COPY ./composer.lock /app/composer.lock
WORKDIR /app
RUN composer install --no-interaction --no-plugins --no-scripts
COPY . .
EXPOSE 8000
EXPOSE 3000
EXPOSE 6001

CMD ["php", "artisan", "serve", "--host", "0.0.0.0"]


