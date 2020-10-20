#!/bin/bash

if [ ! -f composer.json ]; then
    echo "Please make sure to run this script from the root directory of this repo."
    exit 1
fi

echo "Installing composer dependencies"
composer install -q

echo "Creating .env file..."
cp .env.example .env

echo -e "\e[1;32mFile created.\e[0m"

read -p $'\e[33mPlease update the .env file, when you are done press [ENTER]\e[0m'

echo "Setting application key."
php artisan key:generate

echo "Migrating database."
php artisan migrate

echo "Creating storage symlink"
php artisan storage:link

echo "Installing NPM dependencies"
npm install --silent

echo "Building assets (dev)"
npm run dev

printf "\n\e[1;32mSetup success! \e[0m"
