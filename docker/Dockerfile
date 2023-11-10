FROM php:8.1.6-apache


# Cài đặt các extensions cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && docker-php-ext-install zip


# Cấu hình Apache
RUN a2enmod rewrite
WORKDIR /var/www/html

# Sao chép ứng dụng vào image
COPY . .
# COPY package*.json ./
# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Cài đặt các dependencies của project và biên dịch assets
# FROM node:16-alpine
# WORKDIR /var/www/html
# COPY package*.json /var/www/html
# RUN npm run dev
# Cài đặt Node.js
RUN apt-get update && apt-get install -y curl software-properties-common gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_14.x | bash - \
    && apt-get install -y nodejs
# RUN npm install
# RUN npm run dev
# Expose port và khởi động Apache
EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
# CMD ["apache2-foreground"]



