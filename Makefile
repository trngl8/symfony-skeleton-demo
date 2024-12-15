SHELL := /bin/bash

cc:
	php bin/console cache:clear
.PHONY: cc

update:
	composer update
	yarn upgrade
.PHONY: update

deploy:
	vendor/bin/dep deploy --branch=develop
.PHONY: deploy

tests:
	php bin/console doctrine:database:drop --env=test --force
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:create --env=test
	php bin/console doctrine:fixtures:load --env=test --no-interaction
	php bin/phpunit --configuration phpunit.xml.dist tests
.PHONY: tests

coverage:
	XDEBUG_MODE=coverage php bin/phpunit --configuration phpunit.xml.dist tests --coverage-html var/unit/coverage
.PHONY: tests

trans:
	#php bin/console translation:extract --force --format=yaml --as-tree=3 --sort=asc --domain=messages en
.PHONY: trans

restore:
	symfony run pg_restore -d db_name -f filename.dump
.PHONY: restore

backup:
	sqlite3 var/data.db .schema > var/backup_data_schema.sql
	sqlite3 var/data.db .dump > var/backup_data_dump.sql
.PHONY: restore
