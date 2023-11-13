FROM kiwfydev/nginx-alpine:latest

COPY ./ops/docker/prod/nginx/default.conf /etc/nginx/conf.d/default.conf

COPY . /var/www/html

EXPOSE 80
EXPOSE 443

STOPSIGNAL SIGTERM

CMD ["nginx", "-g", "daemon off;"]