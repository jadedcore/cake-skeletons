FROM composer:1.9.3 as vendor
FROM php:8.4-apache

LABEL org.opencontainers.image.source https://github.com/jadedcore/cake4-skeleton

# Update the server
RUN apt-get update

# Install intl
RUN apt-get install -y libicu-dev \
	&& docker-php-ext-configure intl \
	&& docker-php-ext-install intl

# Install zip
RUN apt-get install -y \
	libzip-dev libpng-dev \
	&& docker-php-ext-install zip

# Install php mysql
RUN docker-php-ext-install mysqli pdo pdo_mysql gd && docker-php-ext-enable pdo_mysql gd

# Install PECL extensions
RUN pecl install apcu \
&& docker-php-ext-enable apcu

# Install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Add app
COPY --chown=33:33 app /var/www/html

# Set permissions for cache
RUN chmod 1777 /tmp
RUN find /tmp -mindepth 1 -name '.*-unix' -exec chmod 1777 {} + -prune -o -exec chmod go-rwx {} +

# Composer install not interactive
RUN composer install -n

ENV APACHE_RUN_DIR /var/www/html
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2

# Create logging directories
RUN mkdir /var/log/sans-log/ \
	&& chown -R www-data:www-data /var/log/sans-log/ \
	&& chmod -R 770 /var/log/sans-log

# Enable modrewrite
Run a2enmod rewrite

# Create virtual host
COPY docker/000-default.conf /etc/apache2/sites-available/00-default.conf
COPY docker/apache2.conf /etc/apache2/apache2.conf
RUN a2ensite 00-default

# Start Apache
CMD ["/usr/sbin/apache2","-D","FOREGROUND"]
