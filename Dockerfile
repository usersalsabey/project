FROM php:8.2-apache

# Install extension installer (pre-compiled, jauh lebih cepat)
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install PHP extensions
RUN install-php-extensions intl zip pdo pdo_mysql mbstring xml opcache bcmath ctype fileinfo tokenizer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Apache config: point DocumentRoot to /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && echo '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>' >> /etc/apache2/apache2.conf

# Expose port
EXPOSE 80

CMD ["apache2-foreground"]