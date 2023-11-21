git reset HEAD --hard
git pull
export SYMFONY_ENV=prod
php8.2 /usr/local/bin/composer install --no-dev --optimize-autoloader
php8.2 bin/console cache:clear --env=prod --no-debug
php8.2 bin/console doctrine:migrations:migrate --no-debug --no-interaction --env=prod
php8.2 bin/console assets:install --env=prod