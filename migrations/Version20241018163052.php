<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241018163052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE certidao_divida_supp_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE certidao_divida_supp ADD contribuinte_supp_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE certidao_divida_supp ADD CONSTRAINT FK_88C2AAE4B758B859 FOREIGN KEY (contribuinte_supp_id) REFERENCES contribuinte_supp (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_88C2AAE4B758B859 ON certidao_divida_supp (contribuinte_supp_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE certidao_divida_supp_id_seq CASCADE');
        $this->addSql('ALTER TABLE certidao_divida_supp DROP CONSTRAINT FK_88C2AAE4B758B859');
        $this->addSql('DROP INDEX IDX_88C2AAE4B758B859');
        $this->addSql('ALTER TABLE certidao_divida_supp DROP contribuinte_supp_id');
    }
}
