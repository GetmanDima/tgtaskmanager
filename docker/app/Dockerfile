FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    supervisor \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN mkdir -p /etc/supervisor/conf.d
COPY ./docker/app/supervisord.conf /etc/supervisor/supervisord.conf

WORKDIR /var/www

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
