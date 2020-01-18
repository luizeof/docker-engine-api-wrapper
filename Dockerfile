FROM php:7.3-apache

LABEL name="br.com.luizeof.docker_engine_api"

LABEL version="1.0.0"

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update

EXPOSE 80

RUN mkdir -p /var/www

RUN a2enmod rewrite headers

COPY index.php /var/www/html/index.php

COPY .htaccess-template /var/www/html/.htaccess

COPY docker-entrypoint.sh /entrypoint.sh

RUN chown -R www-data:www-data /var/www/html

STOPSIGNAL SIGWINCH

WORKDIR /var/www/html

EXPOSE 80

RUN ["chmod", "+x", "/entrypoint.sh"]

ENTRYPOINT ["/entrypoint.sh"]

CMD ["apache2-foreground"]