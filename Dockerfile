FROM php:8.3-cli-alpine

# PDO MySQL + gerekli eklentiler
RUN apk add --no-cache \
        mariadb-dev \
        oniguruma-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        intl \
        opcache

# Çalışma dizini
WORKDIR /var/www/html/public/php-modernized

EXPOSE 8000

# Built-in server + router
CMD ["php", "-S", "0.0.0.0:8000", "router.php"]
