#!/bin/bash
set -e

# Generate JWT keys not in PROD env
# Originally from API Platform: https://github.com/api-platform/demo/blob/master/api/docker/php/docker-entrypoint.sh

eval $(stat -c 'usermod -u %u -g %g www-data' /var/www) || true
/etc/init.d/php8.0-fpm start
exec "$@"
