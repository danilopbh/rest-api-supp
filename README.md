Padrão supp com as camadas controller, counters, dto, mapper, resource, rules, triggers

composer install
composer require --dev orm-fixtures
composer require fakerphp/faker


php bin/console doctrine:fixtures:load

composer require tecnickcom/tcpdf