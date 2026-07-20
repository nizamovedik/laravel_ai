FROM php:8.4-fpm

# Устанавливаем системные зависимости для сборки Redis
RUN apt-get update && apt-get install -y \
      apt-utils \
      libpq-dev \
      libpng-dev \
      libzip-dev \
      libssl-dev \
      zlib1g-dev \
      git \
      zip unzip \
      curl \
      && docker-php-ext-install pdo_mysql \
      && docker-php-ext-install bcmath \
      && docker-php-ext-install gd \
      && docker-php-ext-install zip \
      && apt-get clean \
      && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN pecl install redis && docker-php-ext-enable redis

COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

WORKDIR /var/www