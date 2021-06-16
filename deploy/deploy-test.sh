#!/bin/bash
PHP=`which php`

composer install
$PHP bin/console doctrine:database:drop --if-exists --env=test
$PHP bin/console doctrine:database:create --if-not-exists --env=test
$PHP bin/console doctrine:schema:update --force --env=test
$PHP bin/console doctrine:fixtures:load --force --env=test
$PHP bin/console assets:install

# Add write permissions on media folder
chmod 777 -R public/*


# test
$PHP ./vendor/bin/phpunit
