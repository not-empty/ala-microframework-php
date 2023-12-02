ARG PHP_VERSION=7.3.33-fpm

FROM php:${PHP_VERSION}
COPY --from=php:7.3.33-cli /usr/local/bin/phpdbg /usr/local/bin/

RUN apt update \
    && apt upgrade -y \
    && apt install zip unzip git -y

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-enable pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
