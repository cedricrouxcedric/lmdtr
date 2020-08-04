<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200803153625 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE moto (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, marque_id INT NOT NULL, vendeur_id INT NOT NULL, prix INT NOT NULL, year INT NOT NULL, kilometrage INT NOT NULL, a2 TINYINT(1) NOT NULL, model VARCHAR(255) NOT NULL, din INT NOT NULL, fisc INT NOT NULL, cylindree INT NOT NULL, INDEX IDX_3DDDBCE4BCF5E72D (categorie_id), INDEX IDX_3DDDBCE44827B9B2 (marque_id), INDEX IDX_3DDDBCE4858C065E (vendeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, moto_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E01FBE6A78B8F2AC (moto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE moto ADD CONSTRAINT FK_3DDDBCE4BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE moto ADD CONSTRAINT FK_3DDDBCE44827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (id)');
        $this->addSql('ALTER TABLE moto ADD CONSTRAINT FK_3DDDBCE4858C065E FOREIGN KEY (vendeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A78B8F2AC FOREIGN KEY (moto_id) REFERENCES moto (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A78B8F2AC');
        $this->addSql('DROP TABLE moto');
        $this->addSql('DROP TABLE images');
    }
}
