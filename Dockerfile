# Gunakan image resmi PHP 8.2-FPM
FROM php:8.2-fpm

# Set environment variables
ENV TZ=Asia/Jakarta
ENV APP_ENV=production

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    supervisor \
    libzip-dev \
    nginx

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . .

# Copy .env.example to .env
RUN cp .env.example .env

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copy NGINX configuration
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy Supervisor configuration
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Expose port 80
EXPOSE 80

# Start supervisord to run both PHP-FPM and NGINX
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
