#!/bin/bash

if [ ! -f composer.json ]; then
    echo "Please make sure to run this script from the root directory of this repo."
    exit 1
fi

echo -e "\e[1;32mDeploying application...\e[0m"

echo "Entering maintenance mode."
php artisan down

echo "Updating codebase..."
git fetch origin master
git reset --hard origin/master

echo "Updating composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo "Updating npm dependencies..."
npm ci

echo "Building assets..."
npm run prod

echo "Optimizing code..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# echo "Reloading PHP..."
# echo "" | sudo -S service php reload

echo "Migrating database..."
php artisan migrate --force

# echo "Restarting queue workers..."
# php artisan queue:restart

echo "Exiting maintenance mode."
php artisan up

echo -e "\e[1;32mApplication deployed! \e[0m"
