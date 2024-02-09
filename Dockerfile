FROM php:8.1 as php

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libcurl4-gnutls-dev \
    libzip-dev \
    zlib1g-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install zip curl xml pdo pdo_mysql mbstring exif pcntl bcmath gd

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

ENV PORT=8000
ENTRYPOINT [ "docker/entrypoint.sh" ]
