<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241018131836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certidao_divida_supp ADD id_contribuinte_siatu INT NOT NULL');
        $this->addSql('ALTER TABLE contribuinte_supp ADD id_contribuinte_siatu INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE certidao_divida_supp DROP id_contribuinte_siatu');
        $this->addSql('ALTER TABLE contribuinte_supp DROP id_contribuinte_siatu');
    }
}
