FROM php:8.1-apache
WORKDIR /var/www/html

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY . .

RUN chown -R www-data:www-data /var/www/html