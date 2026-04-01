FROM php:8.1-apache

# Enable Apache rewrite
RUN a2enmod rewrite

# Install system packages + PHP extensions
RUN apt-get update && apt-get install -y \
    unzip git curl libzip-dev zip \
    libicu-dev libjpeg-dev libpng-dev \
    && docker-php-ext-install zip intl exif

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install ionCube Loader
RUN curl -fsSL https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz | tar -xz \
    && cp ioncube/ioncube_loader_lin_8.1.so /usr/local/lib/php/extensions/ \
    && echo "zend_extension=/usr/local/lib/php/extensions/ioncube_loader_lin_8.1.so" > /usr/local/etc/php/conf.d/00-ioncube.ini

# Change Apache port to 8080 (Fly.io requirement)
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:8080/g' /etc/apache2/sites-available/000-default.conf

# Copy project
COPY . /var/www/html/

# Laravel install
WORKDIR /var/www/html/core
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# Permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 8080

CMD ["apache2-foreground"]