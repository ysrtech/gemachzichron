#!/bin/bash

if [ ! -f composer.json ]; then
    echo "Please make sure to run this script from the root directory of this repo."
    exit 1
fi

echo -e "\e[1;32mDeploying application...\e[0m"

printf "Entering maintenance mode.\n"
php artisan down
php artisan backup:run

printf "Updating codebase...\n"
git fetch origin
git reset --hard origin/master

printf "Updating composer dependencies...\n"
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

printf "Updating npm dependencies...\n"
npm install

printf "Building assets...\n"
npm run prod

printf "Optimizing code...\n"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# printf "Reloading PHP...\n"
# echo "" | sudo -S service php reload

printf "Migrating database...\n"
php artisan migrate --force

printf "Restarting queue workers...\n"
php artisan queue:restart

printf "Exiting maintenance mode.\n"
php artisan up

echo -e "\e[1;32mApplication deployed! \e[0m"
