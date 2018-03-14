CURRENT_BRANCH="$(shell git rev-parse --abbrev-ref HEAD)"
CONTAINER_NAME_BASE="hgraca/explicit-architecture:app.sfn.base"
CONTAINER_NAME_DEV="hgraca/explicit-architecture:app.sfn.dev"
CONTAINER_NAME_PRD="hgraca/explicit-architecture:app.sfn.prd"
COVERAGE_REPORT_PATH="var/coverage.clover.xml"
DB_PATH='var/data/blog.sqlite'

.SILENT:

# .DEFAULT: If command does not exist in this makefile
# default:  If no command was specified:
.DEFAULT default:
	if [ -f ./Makefile.custom.sh ]; then ./Makefile.custom.sh "$@"; else ./Makefile.aliases.dist.sh "$@"; fi

help:
	@echo "Usage:"
	@echo "     make [command]"
	@echo "Available commands:"
	@grep '^[^#[:space:]].*:' Makefile | grep -v '^default' | grep -v '^\.' | grep -v '=' | grep -v '^_' | sed 's/://' | xargs -n 1 echo ' -'

box-base-build:
	docker build -t ${CONTAINER_NAME_BASE} -f ./build/container/app.base.dockerfile .

box-base-push:
	docker push ${CONTAINER_NAME_BASE}

box-dev-build:
	docker build -t ${CONTAINER_NAME_DEV} -f ./build/container/dev/app.dockerfile .

box-prd-build:
	docker build -t ${CONTAINER_NAME_PRD} -f ./build/container/prd/app.dockerfile .

box-prd-push:
	docker push ${CONTAINER_NAME_PRD}

db-setup:
	ENV='dev' ./bin/run make db-setup-guest

db-setup-guest:
	mkdir -p /opt/app/var/data
	php bin/console doctrine:database:drop -n --force
	php bin/console doctrine:database:create -n
	php bin/console doctrine:schema:create -n
	php bin/console doctrine:fixtures:load -n

dep-clearcache-guest:
	composer clearcache

dep-install:
	ENV='dev' ./bin/run make dep-install-guest

dep-install-prd-guest:
	composer install --no-dev --optimize-autoloader --no-ansi --no-interaction --no-progress --no-scripts

dep-update:
	ENV='dev' ./bin/run make dep-update-guest

fix-cs:
	ENV='tst' ./bin/run php vendor/bin/php-cs-fixer fix --verbose

test:
	ENV='tst' ./bin/run
	ENV='tst' ./bin/run make db-setup-guest
	ENV='tst' ./bin/run php vendor/bin/simple-phpunit
	$(MAKE) fix-cs
	ENV='tst' ./bin/stop

#   We use phpdbg because is part of the core and so that we don't need to install xdebug just to get the coverage.
#   Furthermore, phpdbg gives us more info in certain conditions, ie if the memory_limit has been reached.
test-cov:
	ENV='tst' ./bin/run phpdbg -qrr vendor/bin/simple-phpunit --coverage-text --coverage-clover=${COVERAGE_REPORT_PATH}

up:
	if [ ! -f ${DB_PATH} ]; then $(MAKE) db-setup; fi
	ENV='dev' ./bin/up

up-prd:
	if [ ! -f ${DB_PATH} ]; then ENV='prd' ./bin/run make db-setup-guest; fi
	ENV='prd' ./bin/up
