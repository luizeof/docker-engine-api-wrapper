#!/bin/sh
set -e

chown -R www-data:www-data /var/www/html

htpasswd -cb /var/www/.htpasswd $AUTHUSER $AUTHPWD

chown -R www-data:www-data /var/www/.htpasswd

ver=$(curl -s --unix-socket /var/run/docker.sock http:/info/version | jq .ApiVersion | tr -d '"')

sed -i "s/APIVER/$ver/g" /var/www/html/index.php

echo 'safe_mode=Off' >/usr/local/etc/php/conf.d/safe.ini

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- apache2-foreground "$@"
fi

exec "$@"
