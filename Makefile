# Variables
DOCKER_COMPOSE="./dc"
PHP_EXEC=$(DOCKER_COMPOSE) exec php
SF=$(PHP_EXEC) bin/console

# Installation
init:
	"$(MAKE)" start composer-install db-init
	echo "The app should be running at : http://127.0.0.1:8000/api/bookmarks"

# Docker
start:
	$(DOCKER_COMPOSE) up -d
rebuild:
	$(DOCKER_COMPOSE) up -d --force-recreate --build
stop:
	$(DOCKER_COMPOSE) stop

# SSH
ssh-php:
	$(PHP_EXEC) sh

# Logs
logs-php:
	$(DOCKER_COMPOSE) logs -f php

# composer
composer-install:
	$(PHP_EXEC) composer install
composer-update:
	$(PHP_EXEC) composer update

# Database
db-init:
	"$(MAKE)" db-create db-migrate db-fixtures-load
db-reset:
	"$(MAKE)" db-remove db-init
db-recreate:
	"$(MAKE)" db-remove db-create
db-remove:
	$(SF) doctrine:database:drop --if-exists --force
db-create:
	$(SF) doctrine:database:create --if-not-exists
db-migrate:
	$(SF) doctrine:migrations:migrate --no-interaction
db-migrations-generate:
	$(SF) make:migration --no-interaction
db-fixtures-load:
	$(SF) doctrine:fixtures:load --no-interaction

# Application
cache-clear:
	$(SF) cache:clear --no-debug

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
