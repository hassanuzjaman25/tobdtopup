FROM php:8.1-apache

RUN a2enmod rewrite

# packages
RUN apt-get update && apt-get install -y \
    unzip git curl libzip-dev zip \
    && docker-php-ext-install zip

# composer install
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ionCube
RUN curl -fsSL https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz | tar -xz \
    && cp ioncube/ioncube_loader_lin_8.1.so /usr/local/lib/php/extensions/ \
    && echo "zend_extension=/usr/local/lib/php/extensions/ioncube_loader_lin_8.1.so" > /usr/local/etc/php/conf.d/00-ioncube.ini

# Apache port 8080
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:8080/g' /etc/apache2/sites-available/000-default.conf

# copy project
COPY . /var/www/html/

# 🔥 Laravel install
WORKDIR /var/www/html/core
RUN composer install --no-dev --optimize-autoloader

# permission
RUN chown -R www-data:www-data /var/www/html
