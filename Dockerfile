FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    curl zip unzip git libicu-dev libzip-dev libxml2-dev \
    && docker-php-ext-install intl zip pdo pdo_mysql mbstring xml \
    && apt-get clean

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

EXPOSE 8000

CMD php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}