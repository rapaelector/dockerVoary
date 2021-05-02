# Bonhomme Batiment Project 🏗️

## Project configuration

### Without docker

To use without docker, you need to have PHP8 installed on your system

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
