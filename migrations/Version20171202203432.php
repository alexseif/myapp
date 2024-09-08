<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171202203432 extends AbstractMigration
{

    public function getDescription(): string
    {
        return 'Migration description';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
          'mysql' !== $this->connection->getDatabasePlatform()->getName(),
          'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
          'ALTER TABLE planner_tasks DROP FOREIGN KEY FK_DA537BF75346EAE1'
        );
        $this->addSql('DROP TABLE planner');
        $this->addSql('DROP TABLE planner_tasks');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
          'mysql' !== $this->connection->getDatabasePlatform()->getName(),
          'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
          'CREATE TABLE planner (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, due DATETIME NOT NULL, UNIQUE INDEX UNIQ_955177615E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
          'CREATE TABLE planner_tasks (planner_id INT NOT NULL, task_id INT NOT NULL, UNIQUE INDEX UNIQ_DA537BF78DB60186 (task_id), INDEX IDX_DA537BF75346EAE1 (planner_id), PRIMARY KEY(planner_id, task_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
          'ALTER TABLE planner_tasks ADD CONSTRAINT FK_DA537BF75346EAE1 FOREIGN KEY (planner_id) REFERENCES planner (id)'
        );
        $this->addSql(
          'ALTER TABLE planner_tasks ADD CONSTRAINT FK_DA537BF78DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id)'
        );
    }

}
