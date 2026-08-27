FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libpq-dev libzip-dev libonig-dev libicu-dev \
    && docker-php-ext-install pdo_pgsql mbstring bcmath intl zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy the Laravel backend manifest first so Docker can cache dependency installation.
COPY composer.json ./
COPY composer.lock* ./

# Generate composer.lock when it is not committed, then install production dependencies.
RUN if [ ! -f composer.lock ]; then composer update --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts; else composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts; fi

COPY . .

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && php artisan package:discover --ansi

COPY docker/entrypoint.sh /usr/local/bin/naco-entrypoint
RUN chmod +x /usr/local/bin/naco-entrypoint

EXPOSE 8080

ENTRYPOINT ["naco-entrypoint"]
