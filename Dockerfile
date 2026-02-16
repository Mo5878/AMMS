FROM php:8.2-apache

# Install required PHP extensions only
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable rewrite only (DO NOT enable any MPM)
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html
