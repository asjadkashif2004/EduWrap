FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql intl zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Cache what is safe at build-time. APP_KEY may not be set during image build.
RUN php artisan route:cache || true
RUN php artisan view:cache || true

EXPOSE 10000
CMD php artisan serve --host 0.0.0.0 --port ${PORT:-10000}
