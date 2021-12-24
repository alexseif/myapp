<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171002223423 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE planner (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, due DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planner_tasks (planner_id INT NOT NULL, task_id INT NOT NULL, INDEX IDX_DA537BF75346EAE1 (planner_id), UNIQUE INDEX UNIQ_DA537BF78DB60186 (task_id), PRIMARY KEY(planner_id, task_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE planner_tasks ADD CONSTRAINT FK_DA537BF75346EAE1 FOREIGN KEY (planner_id) REFERENCES planner (id)');
        $this->addSql('ALTER TABLE planner_tasks ADD CONSTRAINT FK_DA537BF78DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planner_tasks DROP FOREIGN KEY FK_DA537BF75346EAE1');
        $this->addSql('DROP TABLE planner');
        $this->addSql('DROP TABLE planner_tasks');
    }
}
