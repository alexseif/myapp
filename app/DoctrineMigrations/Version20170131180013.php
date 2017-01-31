<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170131180013 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE scheduler CHANGE until until DATETIME DEFAULT NULL, CHANGE byseconds byseconds VARCHAR(255) DEFAULT NULL, CHANGE byminutes byminutes VARCHAR(255) DEFAULT NULL, CHANGE byhours byhours VARCHAR(255) DEFAULT NULL, CHANGE bydays bydays VARCHAR(255) DEFAULT NULL, CHANGE bymonthdays bymonthdays VARCHAR(255) DEFAULT NULL, CHANGE byyeardays byyeardays VARCHAR(255) DEFAULT NULL, CHANGE byweeknos byweeknos VARCHAR(255) DEFAULT NULL, CHANGE bymonths bymonths VARCHAR(255) DEFAULT NULL, CHANGE bysetpos bysetpos VARCHAR(255) DEFAULT NULL, CHANGE wkst wkst VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE scheduler CHANGE until until DATETIME NOT NULL, CHANGE byseconds byseconds VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE byminutes byminutes VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE byhours byhours VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE bydays bydays VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE bymonthdays bymonthdays VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE byyeardays byyeardays VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE byweeknos byweeknos VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE bymonths bymonths VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE bysetpos bysetpos VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE wkst wkst VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
