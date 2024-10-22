<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241022003350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certidao_divida_siatu (id INT NOT NULL, contribuinte_siatu_id INT NOT NULL, valor DOUBLE PRECISION NOT NULL, pdfdivida BYTEA NOT NULL, descricao VARCHAR(255) NOT NULL, data_vencimento DATE NOT NULL, situacao VARCHAR(20) NOT NULL, data_situacao DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_36C56A914D6ED255 ON certidao_divida_siatu (contribuinte_siatu_id)');
        $this->addSql('CREATE TABLE certidao_divida_supp (id INT NOT NULL, contribuinte_supp_id INT DEFAULT NULL, valor DOUBLE PRECISION NOT NULL, pdfdivida BYTEA NOT NULL, descricao VARCHAR(255) NOT NULL, data_vencimento DATE NOT NULL, id_contribuinte_siatu INT NOT NULL, id_certidao_divida_siatu INT NOT NULL, situacao VARCHAR(20) NOT NULL, data_situacao DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_88C2AAE4B758B859 ON certidao_divida_supp (contribuinte_supp_id)');
        $this->addSql('CREATE TABLE contribuinte_siatu (id INT NOT NULL, nome VARCHAR(255) NOT NULL, cpf VARCHAR(25) NOT NULL, endereco VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE contribuinte_supp (id INT NOT NULL, nome VARCHAR(255) NOT NULL, cpf VARCHAR(25) NOT NULL, endereco VARCHAR(255) NOT NULL, id_contribuinte_siatu INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE certidao_divida_siatu ADD CONSTRAINT FK_36C56A914D6ED255 FOREIGN KEY (contribuinte_siatu_id) REFERENCES contribuinte_siatu (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE certidao_divida_supp ADD CONSTRAINT FK_88C2AAE4B758B859 FOREIGN KEY (contribuinte_supp_id) REFERENCES contribuinte_supp (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE certidao_divida_siatu DROP CONSTRAINT FK_36C56A914D6ED255');
        $this->addSql('ALTER TABLE certidao_divida_supp DROP CONSTRAINT FK_88C2AAE4B758B859');
        $this->addSql('DROP TABLE certidao_divida_siatu');
        $this->addSql('DROP TABLE certidao_divida_supp');
        $this->addSql('DROP TABLE contribuinte_siatu');
        $this->addSql('DROP TABLE contribuinte_supp');
    }
}
