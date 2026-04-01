FROM php:8.1-apache

# Apache rewrite enable
RUN a2enmod rewrite

# Install required packages
RUN apt-get update && apt-get install -y \
    unzip git curl libzip-dev \
    && docker-php-ext-install zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install ionCube Loader
RUN curl -fsSL https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz | tar -xz \
    && cp ioncube/ioncube_loader_lin_8.1.so /usr/local/lib/php/extensions/ \
    && echo "zend_extension=/usr/local/lib/php/extensions/ioncube_loader_lin_8.1.so" > /usr/local/etc/php/conf.d/00-ioncube.ini

# Copy project
COPY . /var/www/html/

# Laravel (core folder) install
WORKDIR /var/www/html/core
RUN composer install --no-dev --optimize-autoloader

# Permission fix
RUN chown -R www-data:www-data /var/www/html

# Apache root
WORKDIR /var/www/html
