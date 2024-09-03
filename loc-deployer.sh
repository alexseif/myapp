#!/bin/bash
git pull
php8.2 /usr/local/bin/composer install
php8.2 bin/console cache:clear
php8.2 bin/console doctrine:migrations:migrate --no-interaction
php8.2 bin/console make:migration
php8.2 bin/console doctrine:migrations:migrate --no-interaction
php8.2 bin/console assets:install
npm install
npm run build