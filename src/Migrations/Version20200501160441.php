<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200501160441 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE moto ADD categorie_id INT NOT NULL, ADD marque_id INT NOT NULL, ADD filename VARCHAR(255) NOT NULL, ADD prix INT NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD year INT NOT NULL, ADD kilometrage INT NOT NULL, ADD a2 TINYINT(1) NOT NULL, ADD sold TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE moto ADD CONSTRAINT FK_3DDDBCE4BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE moto ADD CONSTRAINT FK_3DDDBCE44827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (id)');
        $this->addSql('CREATE INDEX IDX_3DDDBCE4BCF5E72D ON moto (categorie_id)');
        $this->addSql('CREATE INDEX IDX_3DDDBCE44827B9B2 ON moto (marque_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE moto DROP FOREIGN KEY FK_3DDDBCE4BCF5E72D');
        $this->addSql('ALTER TABLE moto DROP FOREIGN KEY FK_3DDDBCE44827B9B2');
        $this->addSql('DROP INDEX IDX_3DDDBCE4BCF5E72D ON moto');
        $this->addSql('DROP INDEX IDX_3DDDBCE44827B9B2 ON moto');
        $this->addSql('ALTER TABLE moto DROP categorie_id, DROP marque_id, DROP filename, DROP prix, DROP updated_at, DROP year, DROP kilometrage, DROP a2, DROP sold');
    }
}
