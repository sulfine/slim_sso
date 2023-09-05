ENV_VERSION:=2.2
SHELL = /bin/bash
DC = DOCKER_BUILDKIT=1 docker compose
EXEC = $(DC_DEV) exec php-fpm

COMPOSER = $(EXEC) composer

help: ## View all make targets
	@echo -e "#############################################"
	@echo -e "##               \x1b[0;33mOPTEO\x1b[0m \x1b[0;32mAU\x1b[1;32mTH\x1b[0m                ##"
	@echo -e "#############################################"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
	| sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
.PHONY: help

build: ## Construit l'image PHP (Nginx via image)
	$(DC) build
.PHONY: build

php: ## Lance un container php
	$(DC) run --rm -it php-fpm sh
.PHONY: php

exec: ## Ouvre le container php lancé
	$(DC) exec php-fpm sh
.PHONY: exec

start: ## Démarre l'application
	@$(DC) up -d --remove-orphans
.PHONY: start

stop: ## Stop l'application
	@$(DC) kill
	@$(DC) rm -v --force
.PHONY: stop

restart: ## Stop et relance l'application
	@$(MAKE) stop
	@$(MAKE) start
.PHONY: restart

logs: ## Log instantané
	@$(DC) logs
.PHONY: logs

logs-f: ## Log en mode continu
	@$(DC) logs -t -f
.PHONY: logs-f

ps: ## Liste des services lancés
	@$(DC) ps -a --services
.PHONY: ps

vendor:
	@$(COMPOSER) install --optimize-autoloader
.PHONY: vendor
