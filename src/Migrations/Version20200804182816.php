<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200804182816 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AC40FCFA8');
        $this->addSql('DROP TABLE piece_detachee');
        $this->addSql('ALTER TABLE moto DROP sold');
        $this->addSql('DROP INDEX IDX_E01FBE6AC40FCFA8 ON images');
        $this->addSql('ALTER TABLE images DROP piece_id, CHANGE moto_id moto_id INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE piece_detachee (id INT AUTO_INCREMENT NOT NULL, vendeur_id INT NOT NULL, marque_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, model VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, prix INT NOT NULL, descriptif LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, sold TINYINT(1) NOT NULL, INDEX IDX_A29BA4EF4827B9B2 (marque_id), INDEX IDX_A29BA4EF858C065E (vendeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE piece_detachee ADD CONSTRAINT FK_A29BA4EF4827B9B2 FOREIGN KEY (marque_id) REFERENCES marque (id)');
        $this->addSql('ALTER TABLE piece_detachee ADD CONSTRAINT FK_A29BA4EF858C065E FOREIGN KEY (vendeur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE images ADD piece_id INT DEFAULT NULL, CHANGE moto_id moto_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AC40FCFA8 FOREIGN KEY (piece_id) REFERENCES piece_detachee (id)');
        $this->addSql('CREATE INDEX IDX_E01FBE6AC40FCFA8 ON images (piece_id)');
        $this->addSql('ALTER TABLE moto ADD sold TINYINT(1) NOT NULL');
    }
}
