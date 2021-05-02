#!/bin/bash
set -e

# Generate JWT keys not in PROD env
# Originally from API Platform: https://github.com/api-platform/demo/blob/master/api/docker/php/docker-entrypoint.sh
if [ "$APP_ENV" != 'prod' ]; then
  jwt_passphrase=$(grep '^JWT_PASSPHRASE=' .env | cut -f 2 -d '=')
  if [ ! -f config/jwt/private.pem ] || ! echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -noout > /dev/null 2>&1; then
    echo "Generating public / private keys for JWT"
    mkdir -p config/jwt
    echo "$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
    setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
    setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
  fi
fi

eval $(stat -c 'usermod -u %u -g %g www-data' /var/www) || true
/etc/init.d/php8.0-fpm start
exec "$@"
