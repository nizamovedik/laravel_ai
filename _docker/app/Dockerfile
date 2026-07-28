FROM php:8.4-fpm

# Системные зависимости
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
      gnupg \
      libfreetype6-dev \
      libjpeg62-turbo-dev \
      && apt-get clean \
      && rm -rf /var/lib/apt/lists/*

# Расширения PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
      && docker-php-ext-install pdo_mysql bcmath gd zip

# Установка Redis через PECL
RUN pecl install redis \
      && docker-php-ext-enable redis \
      && apt-get purge -y --auto-remove autoconf g++ make \
      && rm -rf /tmp/* /var/tmp/*

# Копирование конфигурации PHP
COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

# Настройка Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN echo "listen = 9000" >> /usr/local/etc/php-fpm.d/zz-docker.conf

# Установка Node.js (исправленный скрипт NodeSource с поддержкой GPG-ключей)
RUN curl -fsSL https://nodesource.com | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# Копируем сначала файлы манифестов зависимостей (для эффективного кэширования слоев Docker)
COPY package*.json composer*.json ./

# Шаг установки зависимостей (выполняется до копирования кода, чтобы экономить время сборки)
RUN composer install --no-interaction --no-scripts --no-autoloader
RUN npm ci

# Копируем весь остальной код проекта
COPY . /var/www

# Финальная оптимизация автозагрузки классов PHP и сборка фронтенда Vite
RUN composer dump-autoload --optimize
RUN npm run build
