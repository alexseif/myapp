git pull
composer install
bin/console cache:clear
bin/console doctrine:migrations:migrate --no-interaction
bin/console make:migration
bin/console doctrine:migrations:migrate --no-interaction
bin/console assets:install