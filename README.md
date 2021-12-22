# About
This is a simple REST API to handle urls bookmarks (image, video)

The supported url providers :
* [Flickr](https://www.flickr.com/)
* [Vimeo](https://vimeo.com/)

# Installation

> You only need `docker` and `docker-compose` installed and running

* **Method 1 :** Using [make](https://www.gnu.org/software/make/), just run `make init`

## Useful commands

```bash
# Start docker containers
make start

# Stop docker containers
make stop

# Display logs in follow mode
make logs-php

# Install composer dependencies
make composer-install

# Create and initialize database
make db-init

# Clear application cache
make cache-clear
```

* **Method 2 :** using [docker-compose](https://docs.docker.com/compose/)

1. Start the development server : `./dc up -d`
1. Install composer dependencies : `./dc exec php composer install`
1. Create database : `./dc exec php bin/console doctrine:database:create --if-not-exists`
1. Execute migrations : `./dc exec php bin/console doctrine:migrations:migrate --no-interaction`
1. <**Optional**> Insert some fake data :  `./dc exec php bin/console doctrine:fixtures:load --no-interaction`

## Useful commands

```bash
# dc is a wrapper around docker-compose that allows services to be run under the current user
./dc up

# Composer is installed into the php container, it can be invoked as such:
./dc exec php composer [command]

# This is a Symfony Flex project, you can install any Flex recipe
./dc exec php composer req annotations

# Symfony console
./dc exec php bin/console

# Start the MySQL cli
./dc exec mysql mysql symfony

# Stop all services
./dc down
```
