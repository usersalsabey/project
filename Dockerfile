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

RUN printf 'server {\n\
    listen 80;\n\
    root /var/www/html/public;\n\
    index index.php;\n\
    location / { try_files $uri $uri/ /index.php?$query_string; }\n\
    location ~ \\.php$ {\n\
        fastcgi_pass localhost:9000;\n\
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;\n\
        include fastcgi_params;\n\
    }\n\
}\n' > /etc/nginx/sites-available/default

RUN echo '[supervisord]\nnodaemon=true\n\n[program:php-fpm]\ncommand=php-fpm\nstdout_logfile=/dev/stdout\nstdout_logfile_maxbytes=0\nstderr_logfile=/dev/stderr\nstderr_logfile_maxbytes=0\n\n[program:nginx]\ncommand=nginx -g "daemon off;"\nstdout_logfile=/dev/stdout\nstdout_logfile_maxbytes=0\nstderr_logfile=/dev/stderr\nstderr_logfile_maxbytes=0' > /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD php artisan config:clear && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf