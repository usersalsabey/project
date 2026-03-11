FROM php:8.2-fpm

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions intl zip pdo pdo_mysql mbstring xml opcache bcmath ctype fileinfo tokenizer

RUN apt-get update && apt-get install -y nginx supervisor && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

RUN echo "clear_env = no" >> /usr/local/etc/php-fpm.d/www.conf

RUN echo 'server { \
    listen 80; \
    root /var/www/html/public; \
    index index.php; \
    location / { try_files $uri $uri/ /index.php?$query_string; } \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
}' > /etc/nginx/sites-available/default

RUN echo '[supervisord]\nnodaemon=true\n\n[program:php-fpm]\ncommand=php-fpm\n\n[program:nginx]\ncommand=nginx -g "daemon off;"' > /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

# Buat .env saat container start, baru jalankan server
CMD cp .env.example .env && \
    php artisan key:generate --force && \
    php artisan config:clear && \
    /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf