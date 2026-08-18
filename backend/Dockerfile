FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libpq-dev libzip-dev libonig-dev libicu-dev \
    && docker-php-ext-install pdo_pgsql mbstring bcmath intl zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY composer.json ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

COPY . .
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && php artisan package:discover --ansi

EXPOSE 10000
CMD sh -c 'php artisan migrate --force && php artisan db:seed --class=Database\\Seeders\\NacoSeeder --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}'
