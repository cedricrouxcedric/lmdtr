<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200805070940 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE piecedetachee (id INT AUTO_INCREMENT NOT NULL, marque_id INT NOT NULL, name VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, prix INT NOT NULL, INDEX IDX_FE1739144827B9B2 (marque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE piecedetachee ADD CONSTRAINT FK_FE1739144827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (id)');
        $this->addSql('ALTER TABLE images ADD piecedetachee_id INT DEFAULT NULL, CHANGE moto_id moto_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AF039C61F FOREIGN KEY (piecedetachee_id) REFERENCES piecedetachee (id)');
        $this->addSql('CREATE INDEX IDX_E01FBE6AF039C61F ON images (piecedetachee_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AF039C61F');
        $this->addSql('DROP TABLE piecedetachee');
        $this->addSql('DROP INDEX IDX_E01FBE6AF039C61F ON images');
        $this->addSql('ALTER TABLE images DROP piecedetachee_id, CHANGE moto_id moto_id INT NOT NULL');
    }
}
