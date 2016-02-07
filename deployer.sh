git pull
php -d allow_url_fopen=1 composer.phar install 
php app/console cache:clear 
php app/console assetic:dump 
php app/console doctrine:schema:update --force
php app/console assets:install 