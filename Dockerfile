FROM registry.dev.torche.id/torche/nginx:stable

RUN apk --no-cache add supervisor
COPY .kubernetes/nginx_cors.conf /etc/nginx/cors.conf
COPY .kubernetes/www.conf /etc/nginx/conf.d/default.conf
COPY .kubernetes/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html
COPY --chown=www-data . /var/www/html
USER www-data

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
