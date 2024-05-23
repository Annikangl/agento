FROM php:7.2-fpm-alpine

RUN apk add --update libzip-dev curl-dev &&\
    docker-php-ext-install curl && \
    apk del gcc g++ &&\
    rm -rf /var/cache/apk/*

RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install mysqli

RUN apk add --no-cache tzdata

ENV TZ=Asia/Almaty

COPY crontab /etc/crontabs/root

WORKDIR /var/www/scrappers

CMD ["crond", "-f"]
