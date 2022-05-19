FROM kiwfydev/php81-clear-linux:latest

RUN sed -i "s/^;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /usr/share/defaults/php/php.ini && \
    sed -i "s/^;listen.owner = www-data/listen.owner = www-data/" /usr/share/defaults/php/php-fpm.d/www.conf && \
    sed -i "s/^;listen.group = www-data/listen.group = www-data/" /usr/share/defaults/php/php-fpm.d/www.conf && \
    sed -i "s/^;listen.mode = 0660/listen.mode = 0660/" /usr/share/defaults/php/php-fpm.d/www.conf

COPY ./docker/prod/php-clear/newrelic-php5-9.21.0.311-linux.tar.gz /usr/src

RUN tar -xvf /usr/src/newrelic-php5-9.21.0.311-linux.tar.gz -C /usr/src && \
    mkdir /etc/init.d && \
    export NR_INSTALL_SILENT=true && \
    cd /usr/src/newrelic-php5-9.21.0.311-linux && \
    ./newrelic-install install && \
    cp /etc/init.d/newrelic-daemon /usr/local/bin && \
    rm -rf /usr/src/newrelic-php5-9.21.0.311-linux.tar.gz

COPY . /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
