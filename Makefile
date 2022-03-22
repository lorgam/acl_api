include .env
export

start:
	docker-compose up -d --remove-orphans

stop:
	docker-compose stop

down:
	docker-compose down -v

restart: stop start

logs:
	docker-compose logs -f

ssh:
	docker-compose exec php sh

prune:
	docker-compose down -v
	docker volume prune

build:
	docker-compose build

phpv:
	docker-compose exec php php -v

xdebug:
	docker-compose exec php sh "/etc/xdebug_install.sh"

install: c-install sf-migrate sf-load-fixtures
tests: newman

# Databse commands
sql:
	docker-compose exec db mysql -uroot -p$(MARIADB_ROOT_PASSWORD)

db-export:
	docker-compose exec db mysqldump --all-databases -uroot -p$(MARIADB_ROOT_PASSWORD) > dump.sql

db-import:
	DB_ID=$$(docker-compose ps -q db); \
	docker exec -i $$DB_ID sh -c 'exec mysql -uroot -p"$$MARIADB_ROOT_PASSWORD"' < dump.sql

# Composer commands
c-install:
	docker-compose exec php composer install

# Symfony commands
sf-routes:
	docker-compose exec php bin/console debug:router

sf-env:
	docker-compose exec php bin/console debug:container --env-vars

sf-params:
	docker-compose exec php bin/console debug:container --parameters

sf-migration:
	docker-compose exec php bin/console make:migration

sf-migrate:
	docker-compose exec php bin/console doctrine:migrations:migrate

sf-load-fixtures:
	docker-compose exec php bin/console doctrine:fixtures:load

sf-dropdb:
	docker-compose exec php bin/console doctrine:database:drop --force

sf-createdb:
	docker-compose exec php bin/console doctrine:database:create

#Node commands
newman:
	docker-compose run node newman run /postman_tests.json

