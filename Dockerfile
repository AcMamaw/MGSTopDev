FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    curl zip unzip git libpng-dev libonig-dev libxml2-dev ca-certificates \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

COPY . .

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && chmod -R 775 storage bootstrap/cache

RUN composer install --no-dev --optimize-autoloader

EXPOSE 10000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]