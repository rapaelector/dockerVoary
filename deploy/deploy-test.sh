#!/bin/bash
PHP=`which php`

composer install
npm install
yarn encore dev
$PHP bin/console doctrine:database:drop --if-exists --env=test
$PHP bin/console doctrine:database:create --if-not-exists --env=test
$PHP bin/console doctrine:schema:update --force --env=test
$PHP bin/console doctrine:fixtures:load --no-interaction --env=test
$PHP bin/console assets:install

# Add write permissions on media folder
chmod 777 -R public/*


# test
$PHP ./vendor/bin/phpunit


