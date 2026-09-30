FROM php:8.4-cli
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo_pgsql pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist

COPY . .

RUN chmod -R 777 storage bootstrap/cache