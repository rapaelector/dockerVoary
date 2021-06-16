#!/bin/bash
PHP=`which php`

composer install
npm install
yarn add maildev -g
yarn encore dev
node_modules/maildev/bin/maildev --web 1080 --smtp 25 --hide-extensions STARTTLS &
$PHP bin/console doctrine:database:drop --if-exists --env=test
$PHP bin/console doctrine:database:create --if-not-exists --env=test
$PHP bin/console doctrine:schema:update --force --env=test
$PHP bin/console doctrine:fixtures:load --no-interaction --env=test
$PHP bin/console assets:install

# Add write permissions on media folder
chmod 777 -R public/*


# test
$PHP ./vendor/bin/phpunit


