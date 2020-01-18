#!/bin/sh
set -e

chown -R www-data:www-data /var/www/html

htpasswd -cb /var/www/.htpasswd $AUTHUSER $AUTHPWD

chown -R www-data:www-data /var/www/.htpasswd

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- apache2-foreground "$@"
fi

exec "$@"
