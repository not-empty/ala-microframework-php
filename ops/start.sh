#!/bin/sh

chmod -R 777 /var/log
echo "permissions set on xdebug logs"

php-fpm
