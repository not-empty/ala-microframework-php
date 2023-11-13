ARG PHP_VERSION=7.4.33-fpm

FROM php:${PHP_VERSION}
COPY --from=php:7.4.33-cli /usr/local/bin/phpdbg /usr/local/bin/

RUN apt update \
    && apt upgrade -y \
    && apt install zip unzip git -y

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
