# Development tasks. Everything runs in the php container (compose.yaml), so
# you need only Docker and make. `make help` lists the targets.

.DEFAULT_GOAL := help

# Files created in the container belong to you.
export USER_ID ?= $(shell id -u)
export GROUP_ID ?= $(shell id -g)
export DOCKER_BUILDKIT = 1

# The versions to resolve against; the defaults match the newest CI matrix
# entry (.github/workflows/phpunit.yml). composer.lock is not committed.
export PHP_VERSION ?= 8.5
SYMFONY ?= 8.*
UX ?= ^3.0

RUN := docker compose run --rm php

help: ## List the targets
	@grep -hE '^[a-z0-9-]+:.*## ' $(MAKEFILE_LIST) | awk -F':.*## ' '{printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2}'
.PHONY: help

build: ## Build the PHP image (PHP_VERSION=8.4 for the older one)
	docker compose build php
.PHONY: build

install: build ## Resolve and install the dependencies for PHP_VERSION, SYMFONY and UX
	$(RUN) composer update --prefer-dist --no-progress --no-interaction -W \
		--with "symfony/framework-bundle:$(SYMFONY)" \
		--with "symfony/form:$(SYMFONY)" \
		--with "symfony/validator:$(SYMFONY)" \
		--with "symfony/ux-live-component:$(UX)" \
		--with "symfony/ux-twig-component:$(UX)" \
		--with "symfony/ux-icons:$(UX)"
.PHONY: install

test: ## Run PHPUnit: make test ARGS="--filter FilterForm"
	$(RUN) vendor/bin/phpunit $(ARGS)
.PHONY: test

test-ci: ## Install and test both CI matrix entries (leaves the second installed)
	$(MAKE) install test PHP_VERSION=8.4 SYMFONY=7.4.* UX=^2.0
	$(MAKE) install test PHP_VERSION=8.5 SYMFONY=8.* UX=^3.0
.PHONY: test-ci

composer: ## Run composer: make composer ARGS="why symfony/form"
	$(RUN) composer $(ARGS)
.PHONY: composer

sh: ## Open a shell in the PHP container
	$(RUN) bash
.PHONY: sh

clean: ## Remove vendor/, composer.lock, test caches and the composer cache volume
	rm -rf vendor composer.lock .phpunit.cache .phpunit.result.cache
	docker compose down --volumes --remove-orphans
.PHONY: clean
