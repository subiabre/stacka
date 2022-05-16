FROM php:8.1-cli

# System dependencies
RUN apt update && apt upgrade -y 
RUN apt install -y \
    zip unzip git curl bash \
    libicu-dev

# PHP extensions
RUN docker-php-ext-configure intl
RUN docker-php-ext-install \
    intl \
    pdo pdo_mysql

# Set app dir
ADD . /app
WORKDIR /app

# Get composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
