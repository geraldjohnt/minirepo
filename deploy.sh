#!/bin/sh

# activate maintenance mode
# php artisan down

git pull origin cebu-dev

# update PHP dependencies
# export COMPOSER_HOME='/tmp/composer'
# composer install --no-interaction --no-dev --prefer-dist
# 	# --no-interaction	Do not ask any interactive question
# 	# --no-dev		Disables installation of require-dev packages.
	# --prefer-dist		Forces installation from package dist even for dev versions.

# update database
#php artisan migrate
	# --force		Required to run when in production.

# stop maintenance mode
# $echo sN3gFa9D | sudo -S "npm run prod"
# php artisan up
