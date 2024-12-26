FROM php:8.4.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set ServerName to suppress warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copy application files
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html
