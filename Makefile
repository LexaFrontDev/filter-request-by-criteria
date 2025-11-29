# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec -e COMPOSER_MEMORY_LIMIT=-1 php

# Executables
PHP      = $(PHP_CONT) php -d memory_limit=-1
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console
PHPUNIT  = $(PHP) bin/phpunit


# Node.js / npm
NODE = $(PHP_CONT) node
NPM  = $(PHP_CONT) npm


# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) -f docker-compose.yml -f docker-compose.dev.yml build #--pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) -f docker-compose.yml -f docker-compose.dev.yml up --detach

up-build: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) -f docker-compose.yml -f docker-compose.dev.yml up --build --detach

up-prod: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) -f docker-compose.yml -f docker-compose.prod.yml up --detach

up-prod-build: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) -f docker-compose.yml -f docker-compose.prod.yml up --build --detach

start: build up ## Build and start the containers

db-reset: ## reset databases
	@echo "Stopping containers..."
	@$(DOCKER_COMP) down
	@echo "Removing PostgreSQL volume..."
	@docker volume rm timer_pgdata || true
	@echo "Done. You can now rebuild containers."

stop: ## Stop the docker hub
	@$(DOCKER_COMP) stop

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh

node: ## Check Node.js version inside container
	@$(NODE) -v

npm: ## Check npm version inside container
	@$(NPM) -v

npm-install: ## Run npm install inside container
	@$(NODE_CONT) npm install

perms: ## Connect to the PHP FPM container
	@$(PHP_CONT) chown www-data ./var/cache -R
	@$(PHP_CONT) chmod 0777 ./var/cache -R

remove-cache: ## Connect to the PHP FPM container
	@$(PHP_CONT) rm -rf ./var/cache/dev
	@$(PHP_CONT) rm -rf ./var/cache/prod

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
phpunit: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(PHPUNIT)

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf