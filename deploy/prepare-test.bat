echo "======= prepare for the test ========"
chmod 777 -R var/cache
chmod 777 -R var/log
node_modules/maildev/bin/maildev --web 1080 --smtp 25 --hide-extensions STARTTLS &
"C:\wamp64\bin\php\php8.0.5\php.exe" bin/console doctrine:database:drop --force --if-exists --env=test
"C:\wamp64\bin\php\php8.0.5\php.exe" bin/console doctrine:database:create --if-not-exists --env=test
"C:\wamp64\bin\php\php8.0.5\php.exe" bin/console doctrine:schema:update --force --env=test
"C:\wamp64\bin\php\php8.0.5\php.exe" bin/console doctrine:fixtures:load --no-interaction --env=test
"C:\wamp64\bin\php\php8.0.5\php.exe" bin/console assets:install

chmod 777 -R public/*