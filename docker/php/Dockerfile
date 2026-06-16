FROM php:8.3-fpm

# Install dependensi sistem & konfigurasi ekstensi PHP bawaan Anda
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

RUN docker-php-ext-install \
    pdo_mysql \
    bcmath \
    exif \
    intl \
    gd \
    zip \
    opcache

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www

# Salin seluruh file dari lokal (yang sudah di-build & di-install vendor-nya)
COPY . .

# Atur permission agar Laravel bisa menulis log/session di VPS
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

CMD ["php-fpm"]