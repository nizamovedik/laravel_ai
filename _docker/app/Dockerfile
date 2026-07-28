FROM php:8.4-fpm

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
      autoconf \
      g++ \
      make \
      libfreetype6-dev \
      libjpeg62-turbo-dev \
      && apt-get clean \
      && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
      && docker-php-ext-install pdo_mysql bcmath gd zip

RUN pecl install redis \
      && docker-php-ext-enable redis \
      && apt-get purge -y --auto-remove autoconf g++ make \
      && rm -rf /tmp/* /var/tmp/*

COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN echo "listen = 9000" >> /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www
COPY . /var/www

# Установка зависимостей PHP
RUN composer install --no-interaction --optimize-autoloader

# === ДОБАВЛЕННЫЙ БЛОК ДЛЯ СБОРКИ СТАТИКИ VITE ===
# 1. Устанавливаем Node.js и npm (используем стабильную 20-ю или 22-ю версию из официального репозитория Debian)
RUN curl -fsSL https://nodesource.com | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Устанавливаем npm-пакеты и собираем Vite-манифест
RUN npm install && npm run build
# ===============================================
