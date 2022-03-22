FROM kiwfydev/nginx-alpine:latest

COPY ./docker/dev/nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
EXPOSE 443

STOPSIGNAL SIGTERM

CMD ["nginx", "-g", "daemon off;"]