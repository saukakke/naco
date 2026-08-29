FROM php:8.2-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libpq-dev libzip-dev libonig-dev libicu-dev \
    && docker-php-ext-install pdo_pgsql mbstring bcmath intl zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy the Laravel backend manifest first so Docker can cache dependency resolution.
COPY composer.json ./

# composer.lock is intentionally not required in the repository. Generate it during
# the image build so Railway can deploy from composer.json alone.
RUN composer update --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts

COPY . .

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && php artisan package:discover --ansi

COPY docker/entrypoint.sh /usr/local/bin/naco-entrypoint
RUN chmod +x /usr/local/bin/naco-entrypoint

EXPOSE 8000

ENTRYPOINT ["naco-entrypoint"]
