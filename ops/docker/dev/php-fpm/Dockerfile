ARG PHP_VERSION=8.1.4-fpm

FROM php:${PHP_VERSION}
COPY --from=php:8.1.4-cli /usr/local/bin/phpdbg /usr/local/bin/

RUN apt update \
    && apt upgrade -y \
    && apt install zip unzip git -y

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
