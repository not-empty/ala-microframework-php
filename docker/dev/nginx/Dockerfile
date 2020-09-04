FROM docker.pkg.github.com/kiwfy/nginx-alpine/nginx-alpine:latest

COPY ./default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
EXPOSE 443

STOPSIGNAL SIGTERM

CMD ["nginx", "-g", "daemon off;"]