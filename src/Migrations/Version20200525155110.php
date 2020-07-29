<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200525155110 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comunicationsendregistry (id INT AUTO_INCREMENT NOT NULL, useroperator_id INT DEFAULT NULL, opnop VARCHAR(255) NOT NULL, sendtype VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, cuerpo LONGTEXT DEFAULT NULL, `to` VARCHAR(255) NOT NULL, visitas INT UNSIGNED DEFAULT 0 NOT NULL, createdDate DATETIME DEFAULT NULL, INDEX IDX_38090E729C69AE37 (useroperator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comunicationsendregistry ADD CONSTRAINT FK_38090E729C69AE37 FOREIGN KEY (useroperator_id) REFERENCES useroperator (id)');
       
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE comunicationsendregistry');
    }
}
