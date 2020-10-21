# Gemach Hakehilos


## Installation

- Clone the repo:
```sh
git clone git@github.com:emargareten/gemachhakehilos.git
cd gemachhakehilos
```

- Install PHP dependencies:
```sh
composer install
# add --no-dev for production enviroment
```

- Install NPM dependencies:
```sh
npm install
```

- Setup configuration:

  Start by running `cp .env.example .env` then update this file accordingly

- Generate application key:
```sh
php artisan key:generate
```

- Create storage symlink:
```sh
php artisan storage:link
```

- Build assets:
```sh
# development enviroment
npm run dev
# or
npm run watch
# production enviroment
npm run prod
```


- Run database migrations:
```sh
php artisan migrate
```
