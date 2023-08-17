#!/bin/bash
set -e

echo "Deployment started   ..."

# Install composer dependencies
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Compile npm assets
npm i
npm run prod

# Run database migrations
php artisan migrate --force

# Pull the latest version of the app
git pull origin master

# Enter maintenance mode or return true
# if already is in maintenance mode
(php artisan down) || true


# Clear the old cache
php artisan clear-compiled

# Recreate cache
php artisan optimize

# Exit maintenance mode
php artisan up

echo "Deployment finished!"
