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
    } > /usr/local/etc/php/conf.d/prod-opcache.ini

COPY ./ops/docker/prod/php-fpm/php.ini /usr/local/etc/php/php.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

COPY ./ops/docker/prod/php-fpm/newrelic-php5-9.21.0.311-linux.tar.gz /usr/src

RUN tar -xvf /usr/src/newrelic-php5-9.21.0.311-linux.tar.gz -C /usr/src && \
    mkdir -p /etc/init.d && \
    export NR_INSTALL_SILENT=true && \
    cd /usr/src/newrelic-php5-9.21.0.311-linux && \
    ./newrelic-install install && \
    cp /etc/init.d/newrelic-daemon /usr/local/bin && \
    rm -rf /usr/src/newrelic-php5-9.21.0.311-linux.tar.gz

COPY . /var/www/html

WORKDIR /var/www/html

RUN composer install --no-interaction --no-dev --prefer-dist

EXPOSE 9000

CMD ["php-fpm"]
