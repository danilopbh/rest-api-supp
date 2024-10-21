<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241021141903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certidao_divida_siatu ADD situacao VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE certidao_divida_supp ADD id_certidao_divida_siatu INT NOT NULL');
        $this->addSql('ALTER TABLE certidao_divida_supp ADD situacao VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE certidao_divida_supp DROP id_certidao_divida_siatu');
        $this->addSql('ALTER TABLE certidao_divida_supp DROP situacao');
        $this->addSql('ALTER TABLE certidao_divida_siatu DROP situacao');
    }
}
