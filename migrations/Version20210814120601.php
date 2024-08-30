<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210814120601 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE planner (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planner_thing (planner_id INT NOT NULL, thing_id INT NOT NULL, INDEX IDX_D14732E35346EAE1 (planner_id), INDEX IDX_D14732E3C36906A7 (thing_id), PRIMARY KEY(planner_id, thing_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planner_task_lists (planner_id INT NOT NULL, task_lists_id INT NOT NULL, INDEX IDX_2B4F4C445346EAE1 (planner_id), INDEX IDX_2B4F4C44DDE6A3D7 (task_lists_id), PRIMARY KEY(planner_id, task_lists_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planner_objective (planner_id INT NOT NULL, objective_id INT NOT NULL, INDEX IDX_7A913B8D5346EAE1 (planner_id), INDEX IDX_7A913B8D73484933 (objective_id), PRIMARY KEY(planner_id, objective_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE planner_thing ADD CONSTRAINT FK_D14732E35346EAE1 FOREIGN KEY (planner_id) REFERENCES planner (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planner_thing ADD CONSTRAINT FK_D14732E3C36906A7 FOREIGN KEY (thing_id) REFERENCES thing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planner_task_lists ADD CONSTRAINT FK_2B4F4C445346EAE1 FOREIGN KEY (planner_id) REFERENCES planner (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planner_task_lists ADD CONSTRAINT FK_2B4F4C44DDE6A3D7 FOREIGN KEY (task_lists_id) REFERENCES task_lists (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planner_objective ADD CONSTRAINT FK_7A913B8D5346EAE1 FOREIGN KEY (planner_id) REFERENCES planner (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planner_objective ADD CONSTRAINT FK_7A913B8D73484933 FOREIGN KEY (objective_id) REFERENCES objective (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planner_thing DROP FOREIGN KEY FK_D14732E35346EAE1');
        $this->addSql('ALTER TABLE planner_task_lists DROP FOREIGN KEY FK_2B4F4C445346EAE1');
        $this->addSql('ALTER TABLE planner_objective DROP FOREIGN KEY FK_7A913B8D5346EAE1');
        $this->addSql('DROP TABLE planner');
        $this->addSql('DROP TABLE planner_thing');
        $this->addSql('DROP TABLE planner_task_lists');
        $this->addSql('DROP TABLE planner_objective');
    }
}
