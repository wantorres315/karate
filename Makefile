.PHONY: help ps build start fresh stop restart destroy \
	cache cache-clear migrate migrate-fresh tests

SHELL := /bin/bash

LARAVEL_CONTAINER=karate-laravel.test-1
MYSQL_CONTAINER=karate-mysql-1


ssh: # Access php bash
	docker exec -it ${LARAVEL_CONTAINER} bash
mysql: # Access mysql bash
	docker exec -it ${MYSQL_CONTAINER} bash
migrate:
	docker exec -it ${LARAVEL_CONTAINER} php artisan migrate
rollback:
	docker exec -it ${LARAVEL_CONTAINER} php artisan migrate:rollback
#used on laravel
up:
	./vendor/bin/sail up
