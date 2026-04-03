FROM php:8.3-apache

# SQLite support for PDO
RUN docker-php-ext-install pdo pdo_sqlite

# Enable Apache rewrite module for .htaccess rules
RUN a2enmod rewrite

# Allow .htaccess overrides under document root
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Default command from base image starts Apache in foreground