ARG PHP_VERSION=8.3-rc-fpm

FROM php:${PHP_VERSION}

RUN apt update \
    && apt upgrade -y \
    && apt install zip unzip git -y

RUN docker-php-ext-install mysqli pdo pdo_mysql opcache

RUN { \
        echo "opcache.enable=1"; \
        echo "opcache.enable_cli=0"; \
        echo "opcache.memory_consumption=256"; \
        echo "opcache.interned_strings_buffer=32"; \
        echo "opcache.max_accelerated_files=50000"; \
        echo "opcache.validate_timestamps=0"; \
        echo "opcache.save_comments=1"; \
        echo "opcache.jit=0"; \
    } > /usr/local/etc/php/conf.d/benchmark-opcache.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

WORKDIR /var/www/html

EXPOSE 9000

CMD ["./ops/start.sh"]
