FROM docker.pkg.github.com/kiwfy/php73-clear-linux/php73-clear-linux:latest

RUN sed -i "s/^;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /usr/share/defaults/php/php.ini && \
    sed -i "s/^;listen.owner = www-data/listen.owner = www-data/" /usr/share/defaults/php/php-fpm.d/www.conf && \
    sed -i "s/^;listen.group = www-data/listen.group = www-data/" /usr/share/defaults/php/php-fpm.d/www.conf && \
    sed -i "s/^;listen.mode = 0660/listen.mode = 0660/" /usr/share/defaults/php/php-fpm.d/www.conf

COPY . /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
