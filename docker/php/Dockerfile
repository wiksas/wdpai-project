FROM php:8.3-fpm-alpine

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /app