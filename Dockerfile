FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip git curl libpng-dev libonig-dev libxml2-dev zip nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy project files
COPY . .

# Install backend dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend (if using Vite or Mix)
RUN npm install && npm run build

# Expose Railway's dynamic port
EXPOSE $PORT

# Run Laravel
CMD php artisan serve --host=0.0.0.0 --port=$PORT
