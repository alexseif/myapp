<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170131195622 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE scheduler');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE scheduler (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, start_date DATETIME NOT NULL, freq VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, until DATETIME DEFAULT NULL, count INT NOT NULL, intrvl INT NOT NULL, byseconds VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, byminutes VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, byhours VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, bydays VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, bymonthdays VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, byyeardays VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, byweeknos VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, bymonths VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, bysetpos VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, wkst VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }
}
