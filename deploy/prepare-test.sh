#!/bin/bash
PHP=`which php`

echo "======= install composer ========"
#rm -Rf vendor
#composer install
echo "==== install node module ====="
rm yarn.lock
rm -Rf node_modules
yarn install
echo "======compole the js======="
yarn encore dev
echo "======= prepare for the test ========"
chmod 777 -R var/cache
chmod 777 -R var/log
node_modules/maildev/bin/maildev --web 1999 --smtp 25 --hide-extensions STARTTLS &
$PHP bin/console doctrine:database:drop --force --if-exists --env=test
$PHP bin/console doctrine:database:create --if-not-exists --env=test
$PHP bin/console doctrine:schema:update --force --env=test
$PHP bin/console doctrine:fixtures:load --no-interaction --env=test
$PHP bin/console assets:install

# Add write permissions on media folder
chmod 777 -R public/*
