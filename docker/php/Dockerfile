# ./docker/php/Dockerfile
FROM php:8.4-fpm

RUN apt update && apt install -y \
  unzip curl git \
  zip libzip-dev \
  libpq-dev \
  && docker-php-ext-install zip pdo_pgsql pgsql \
  && curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer
