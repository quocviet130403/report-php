# Use the official PHP 8.1 image as the base
FROM php:8.1-fpm

# Set the working directory inside the container
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    libzip-dev \
    libpq-dev \
    libpng-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the project files to the working directory
COPY code/. .

# Install project dependencies
RUN composer install --no-interaction --no-plugins --no-scripts

# Generate an application key
RUN php artisan key:generate

# Set permissions for Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port 9000
EXPOSE 9000

# Start the PHP-FPM server
CMD ["php-fpm"]
