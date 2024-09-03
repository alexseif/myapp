#!/bin/bash
git reset HEAD --hard
git pull origin master
export SYMFONY_ENV=prod
php8.2 /usr/local/bin/composer dump-env prod
php8.2 /usr/local/bin/composer install --no-dev --optimize-autoloader
APP_ENV=prod APP_DEBUG=0 php8.2 bin/console cache:clear --env=prod
php8.2 bin/console doctrine:migrations:migrate --no-debug --no-interaction --env=prod
php8.2 bin/console assets:install --env=prod
npm install
npm run build