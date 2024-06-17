# Use the official PHP image with Apache
FROM php:8.2-apache

# Install additional PHP extensions if needed
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mods
RUN a2enmod rewrite

# Copy the current directory contents into the container at /var/www/html
COPY . /var/www/html

# Copy custom mime.conf to Apache configuration directory
COPY mime.conf /etc/apache2/mods-available/mime.conf

# Enable mime mod and restart Apache
RUN a2enmod mime

# Copy wohnungen.sql to Docker container
COPY wohnungen.sql /docker-entrypoint-initdb.d/

# Set permissions for the copied files
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80 for Apache
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]