FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libnss3 \
    libxcb1 \
    wget \
    unzip \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd  \
    &&  docker-php-ext-install zip


COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html/

WORKDIR /var/www/html
