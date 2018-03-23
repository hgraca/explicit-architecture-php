# Makefile
#
# This file contains the commands most used in DEV, plus the ones used in CI and PRD environments.
#
# The commands are to be organized semantically and alphabetically, so that similar commands are nex to each other
# and we can compare them and update them easily.
#
# For example in a format like `subject-action-environment`, ie:
#
#   cs-fix:             # here we don't use the env because we only do it in dev
#   dep-install:        # again, by default the env is dev
#   dep-install-prd:
#   dep-update:
#   test:               # here we don't even have a subject because it is the app itself, and by default the env is dev
#

# Mute all `make` specific output. Comment this out to get some debug information.
.SILENT:

# .DEFAULT: If the command does not exist in this makefile
# default:  If no command was specified
.DEFAULT default:
	if [ -f ./Makefile.custom ]; then $(MAKE) -f Makefile.custom "$@"; else $(MAKE) help; fi

help:
	@echo "Usage:"
	@echo "     make [command]"
	@echo
	@echo "Available commands:"
	@grep '^[^#[:space:]].*:' Makefile | grep -v '^default' | grep -v '^\.' | grep -v '=' | grep -v '^_' | sed 's/://' | xargs -n 1 echo ' -'

########################################################################################################################

COVERAGE_REPORT_PATH="var/coverage.clover.xml"
DB_PATH='var/data/blog.sqlite'

#   We run this in tst ENV so that we never run it with xdebug on
cs-fix:
	php vendor/bin/php-cs-fixer fix --verbose

db-setup:
	mkdir -p var/data
	php bin/console doctrine:database:drop -n --force
	php bin/console doctrine:database:create -n
	php bin/console doctrine:schema:create -n
	php bin/console doctrine:fixtures:load -n

dep-clearcache:
	composer clearcache

dep-install:
	composer install

dep-install-prd:
	composer install --no-dev --optimize-autoloader --no-ansi --no-interaction --no-progress --no-scripts

dep-update:
	composer update

test:
	$(MAKE) db-setup
	php vendor/bin/phpunit
	$(MAKE) cs-fix

#   We use phpdbg because is part of the core and so that we don't need to install xdebug just to get the coverage.
#   Furthermore, phpdbg gives us more info in certain conditions, ie if the memory_limit has been reached.
test_cov:
	phpdbg -qrr vendor/bin/phpunit --coverage-text --coverage-clover=${COVERAGE_REPORT_PATH}

up:
	if [ ! -f ${DB_PATH} ]; then $(MAKE) db-setup; fi
	php bin/console server:run 0.0.0.0:8000
