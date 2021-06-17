# Bonhomme Batiment Project 🏗️

## Project configuration

### Without docker

To use without docker, you need to have **PHP8** installed on your system

In case you have multiple php versions on your system, you can create a ``.php-version`` file on the root of the project
and put the desired php version to use to tell ``symfony cli`` which version to use, for example :
```8.0.5```

### With docker 🐳

To use docker, make sure the following environment variables exists
If not changed during the development of the project, they are already defined in ``.env`` file
| Name | Default |
|-----|--------|
| MYSQL_PORT | 33069 |
| ADMINER_PORT | 8089 |
| SERVER_PORT | 809 |


```bash
docker-compose build
docker-compose up -d
```

## PHP CS (Code Sniffer) - Optional

We use php-cs to follow PSR

- Install php-cs globally, https://github.com/FriendsOfPHP/PHP-CS-Fixer
```
composer global require friendsofphp/php-cs-fixer
```
- For VSCode, add php-cs-fixer extension https://github.com/junstyle/vscode-php-cs-fixer configuration in your settings.json, optionaly, you can copy ```.vscode/settings.json.dist``` in a file ```.vscode/settings.json``` and update the vscode configuration there.

Note: For windows, after php-cs-fixer installation, you can simply add ``C:\Users\stephan\AppData\Roaming\Composer\vendor\bin`` to Windows ``PATH`` environment variables, then you can use the ```php-cs-fixer``` command anywhere


## Database (Doctrine ORM)

### Environments variables
Provide ``DATABASE_URL`` env var whether in ``.env.local`` (don't change the provided in ``.env``)

### Create database (run only once)

Run the following command on your system terminal or inside docker container (if you use docker)

```php bin/console doctrine:database:create ```
### Update database schema (when there are entity changes)

```
php bin/console doctrine:schema:update -f
```

## Testing

We use PHPUnit for testing, before running any tests, please create a test user with the following command **(run only once)**
```
php8 bin/console app:user:create test@gmail.com Test123 --env=test
```
you can execute the bash one command **(run only once)**
```
chown +x deploy/test.sh
deploy/test.sh
```
This user will be used throughout the application.

## Environment Variables

Along with Symfony required env var, these are the env vars used by the app

| name                  | Description                                       | Default        |
|-----------------------|---------------------------------------------------|----------------|
|MAILER_FROM            | Used as mailer default sender                     |                |
|MAILER_TO              | Used as mailer default recipient                  |                |

## Tips and helps 💡

### Docker

To execute commands inside docker container, you can (note that ``worker`` is the container name)
- Enter in the container bash
```
docker-compose exec worker bash
```
Then execute the command you want to execute
- Execute the command directly
```
docker-compose exec worker __the_command_to_execute__
```
Example:
```
docker-compose exec worker php bin/console doctrine:schema:update --dump-sql
```
