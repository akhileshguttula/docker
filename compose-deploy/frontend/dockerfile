# Use PHP with Apache so the frontend can process the login form.
FROM php:8.2-apache

# Install the PHP MySQL extension required to connect to MySQL.
RUN docker-php-ext-install mysqli

# Copy the login application into Apache's web root.
COPY index.php /var/www/html/index.php

# Apache listens on port 80 inside the container.
EXPOSE 80

# The base image automatically starts Apache.
