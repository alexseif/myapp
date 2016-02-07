git pull
php -d allow_url_fopen=1 composer.phar install --no-dev --optimize-autoloader
php app/console cache:clear --env=prod --no-debug
php app/console doctrine:schema:update --force --env=prod --no-debug
php app/console assets:install --env=prod --no-debug