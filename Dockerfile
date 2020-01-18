FROM php:7.3-apache

LABEL name="br.com.luizeof.docker_engine_api"

LABEL version="1.0.0"

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update

EXPOSE 80

RUN mkdir -p /var/www

RUN htpasswd -cb /var/www/.htpasswd ${AUTHUSER} ${AUTHPWD}

RUN chown -R www-data:www-data /var/www/.htpasswd

RUN a2enmod rewrite headers

COPY index.php /var/www/html/index.php
COPY .htaccess /var/www/html/.htaccess

RUN chown -R www-data:www-data /var/www/html

CMD ["apache2-foreground"]

