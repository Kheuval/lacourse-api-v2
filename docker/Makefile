include ../make/formatter.mk
include ../make/help.mk

.DEFAULT_GOAL := help

.PHONY: install
install: docker.lock up ## [Docker] Install

docker.lock: .env
	@/bin/echo -e "[Docker] ${GREEN}Start install${END}";
	@docker-compose build
	@touch docker.lock

.env: ## [Docker] Generate the .env file
	@/bin/echo -e "[Docker]️ ${GREEN}Generate .env file${END}";
	@cp .env.dist .env;
	@/bin/echo -e "[Docker]️️️ ${GREEN}.env file generated${END}";

.PHONY: krup
krup: kill remove up ## [Docker] Kill, remove & up

.PHONY: up
up: docker.lock ## [Docker] Up
	@/bin/echo -e "[Docker]️ ${GREEN}Stack is going up${END}";
	@docker-compose up -d

.PHONY: stop
stop: ## [Docker] Stop
	@docker-compose stop

.PHONY: kill
kill: ## [Docker] Kill
	docker-compose kill

.PHONY: remove
remove: ## [Docker] Remove
	docker-compose rm -f
