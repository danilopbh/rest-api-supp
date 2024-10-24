Padrão supp com as camadas controller, counters, dto, mapper, resource, rules, triggers

composer install
composer require --dev orm-fixtures
composer require fakerphp/faker


php bin/console doctrine:fixtures:load

composer require tecnickcom/tcpdf


A cada alteracao nas rotas durante o desenvolvimento, devera ser feita a limpeza da cache
php bin/console cache:clear

composer require doctrine/doctrine-bundle

Ajustar senha do banco