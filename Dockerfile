FROM php:7.3-apache

LABEL name="br.com.luizeof.docker_engine_api"

LABEL version="1.0.0"

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && apt-get install -y nano jq && apt-get clean

RUN a2enmod rewrite headers

COPY index.php /var/www/html/index.php

COPY ./vendor/ /var/www/html/

COPY .htaccess-template /var/www/html/.htaccess

COPY docker-entrypoint.sh /entrypoint.sh

STOPSIGNAL SIGWINCH

EXPOSE 80

RUN ["chmod", "+x", "/entrypoint.sh"]

ENTRYPOINT ["/entrypoint.sh"]

CMD ["apache2-foreground"]