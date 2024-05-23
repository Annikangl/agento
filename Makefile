#vars
DB_USER=root
DB_PASSWORD=root
DB_NAME=laravel
PHP_CONTAINER=php-fpm
NODEJS_CONTAINER=nodejs

# Setip project
setup: env-prepare create-ssl-dir docker-build composer-install-dev key storage_link migrate seed
cache-cache: cache-config-cache cache-view-cache cache-route-cache

env-prepare: # create if not exist env file
	cp -n .env.example .env

key: # generate env APP_KEY
	php artisan key:generate

create-ssl-dir: # create ssl dir
	mkdir $(CURDIR)/docker/nginx/ssl || true

test: # run tests
	docker compose exec $(PHP_CONTAINER) php artisan test

migrate: # run migrations
	docker compose exec $(PHP_CONTAINER) php artisan migrate

seed: # run seeders
	docker compose exec $(PHP_CONTAINER) php artisan db:seed

.PHONY: artisan
artisan: #run artisan command
	docker compose exec $(PHP_CONTAINER) php artisan $(COMMAND)

storage_link: # create storage symlink
	docker compose exec $(PHP_CONTAINER) php artisan storage:link

cache-config-clear: # clear config cache
	docker compose exec $(PHP_CONTAINER) php artisan config:clear

cache-config-cache: # cache config
	docker compose exec $(PHP_CONTAINER) php artisan config:cache

cache-view-clear: # clear view cache
	docker compose exec $(PHP_CONTAINER) php artisan view:clear

cache-view-cache: # cache view
	docker compose exec $(PHP_CONTAINER) php artisan view:cache

cache-route-clear: # clear route cache
	docker compose exec $(PHP_CONTAINER) php artisan route:clear

cache-route-cache: # cache route
	docker compose exec $(PHP_CONTAINER) php artisan route:cache

cache-clear: # clear all cache
	docker compose exec $(PHP_CONTAINER) php artisan cache:clear

migrate-rollback: # rollback migrations
	docker compose exec $(PHP_CONTAINER) php artisan migrate:rollback

migrate-refresh: # refresh migrations
	docker compose exec $(PHP_CONTAINER) php artisan migrate:refresh --seed

composer-install-dev: # install all project dependency
	docker compose exec $(PHP_CONTAINER) composer install

composer-require: # required package
	docker compose exec $(PHP_CONTAINER) composer require $(PACKAGE)

composer-update: # update project dependency
	docker compose exec $(PHP_CONTAINER) composer update

composer-install-prod: # install project dependency for production env
	docker compose exec $(PHP_CONTAINER) composer install --no-dev --optimize-autoloader

docker-build: # build docker
	docker compose up -d --build

docker-up: # up docker
	docker compose up -d

docker-down: # down docker
	docker compose down

dump-db:
	docker compose exec mysql /usr/bin/mysqldump -u $(DB_USER) --password=$(DB_PASSWORD) $(DB_NAME) > backup_`date +'%y.%m.%d %H:%M:%S'`.sql --no-tablespaces

restore-db:
	cat backup_.sql | docker compose exec -T mysql /usr/bin/mysql -u $(USER) --password=$(PASSWORD) $(DB_NAME)

npm-install:
	docker compose exec $(NODEJS_CONTAINER) npm install

npm-run-dev:
	docker compose exec $(NODEJS_CONTAINER) npm run dev

npm-run-build:
	docker compose exec $(NODEJS_CONTAINER) npm run build
