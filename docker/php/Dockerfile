FROM php:8.2-fpm-alpine

RUN apk add --no-cache bash curl libzip-dev oniguruma-dev icu-dev git \
    && docker-php-ext-install pdo pdo_mysql intl zip

WORKDIR /var/www

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]



