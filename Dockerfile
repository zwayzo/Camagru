FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev zip unzip git libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j$(nproc) pdo_mysql mysqli gd mbstring zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists