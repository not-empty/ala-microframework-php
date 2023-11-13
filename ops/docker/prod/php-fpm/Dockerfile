ARG PHP_VERSION=7.1.33-fpm

FROM php:${PHP_VERSION}

RUN apt update \
    && apt upgrade -y \
    && apt install zip unzip git -y

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

COPY ./ops/docker/prod/php-clear/newrelic-php5-9.21.0.311-linux.tar.gz /usr/src

RUN tar -xvf /usr/src/newrelic-php5-9.21.0.311-linux.tar.gz -C /usr/src && \
    mkdir /etc/init.d && \
    export NR_INSTALL_SILENT=true && \
    cd /usr/src/newrelic-php5-9.21.0.311-linux && \
    ./newrelic-install install && \
    cp /etc/init.d/newrelic-daemon /usr/local/bin && \
    rm -rf /usr/src/newrelic-php5-9.21.0.311-linux.tar.gz

COPY . /var/www/html


WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
