<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170131174652 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE scheduler (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, freq VARCHAR(255) NOT NULL, until DATETIME NOT NULL, count INT NOT NULL, intrvl INT NOT NULL, byseconds VARCHAR(255) NOT NULL, byminutes VARCHAR(255) NOT NULL, byhours VARCHAR(255) NOT NULL, bydays VARCHAR(255) NOT NULL, bymonthdays VARCHAR(255) NOT NULL, byyeardays VARCHAR(255) NOT NULL, byweeknos VARCHAR(255) NOT NULL, bymonths VARCHAR(255) NOT NULL, bysetpos VARCHAR(255) NOT NULL, wkst VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE scheduler');
    }
}
