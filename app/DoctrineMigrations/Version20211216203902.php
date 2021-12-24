<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211216203902 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE planner_objective DROP FOREIGN KEY FK_7A913B8D5346EAE1');
        $this->addSql('ALTER TABLE planner_task_lists DROP FOREIGN KEY FK_2B4F4C445346EAE1');
        $this->addSql('ALTER TABLE planner_thing DROP FOREIGN KEY FK_D14732E35346EAE1');
        $this->addSql('ALTER TABLE scenario_details DROP FOREIGN KEY FK_6DE0CB8AE04E49DF');
        $this->addSql('ALTER TABLE scenario_objective DROP FOREIGN KEY FK_22F22C77E04E49DF');
        $this->addSql('ALTER TABLE shopping_item DROP FOREIGN KEY FK_6612795F23245BF9');
        $this->addSql('DROP TABLE planner');
        $this->addSql('DROP TABLE planner_objective');
        $this->addSql('DROP TABLE planner_task_lists');
        $this->addSql('DROP TABLE planner_thing');
        $this->addSql('DROP TABLE scenario');
        $this->addSql('DROP TABLE scenario_details');
        $this->addSql('DROP TABLE scenario_objective');
        $this->addSql('DROP TABLE shopping_item');
        $this->addSql('DROP TABLE shopping_list');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE planner (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE planner_objective (planner_id INT NOT NULL, objective_id INT NOT NULL, INDEX IDX_7A913B8D5346EAE1 (planner_id), INDEX IDX_7A913B8D73484933 (objective_id), PRIMARY KEY(planner_id, objective_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE planner_task_lists (planner_id INT NOT NULL, task_lists_id INT NOT NULL, INDEX IDX_2B4F4C445346EAE1 (planner_id), INDEX IDX_2B4F4C44DDE6A3D7 (task_lists_id), PRIMARY KEY(planner_id, task_lists_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE planner_thing (planner_id INT NOT NULL, thing_id INT NOT NULL, INDEX IDX_D14732E35346EAE1 (planner_id), INDEX IDX_D14732E3C36906A7 (thing_id), PRIMARY KEY(planner_id, thing_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE scenario (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE scenario_details (id INT AUTO_INCREMENT NOT NULL, scenario_id INT NOT NULL, title VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, date DATE NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_6DE0CB8AE04E49DF (scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE scenario_objective (id INT AUTO_INCREMENT NOT NULL, scenario_id INT NOT NULL, title VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, value DOUBLE PRECISION NOT NULL, priority INT NOT NULL, urgency INT NOT NULL, date DATE DEFAULT NULL, INDEX IDX_22F22C77E04E49DF (scenario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE shopping_item (id INT AUTO_INCREMENT NOT NULL, shopping_list_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, est INT DEFAULT NULL, priority INT NOT NULL, urgency INT NOT NULL, completed TINYINT(1) NOT NULL, completed_at DATETIME DEFAULT NULL, eta DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6612795F23245BF9 (shopping_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE shopping_list (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE planner_objective ADD CONSTRAINT FK_7A913B8D5346EAE1 FOREIGN KEY (planner_id) REFERENCES planner (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planner_objective ADD CONSTRAINT FK_7A913B8D73484933 FOREIGN KEY (objective_id) REFERENCES objective (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planner_task_lists ADD CONSTRAINT FK_2B4F4C445346EAE1 FOREIGN KEY (planner_id) REFERENCES planner (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planner_task_lists ADD CONSTRAINT FK_2B4F4C44DDE6A3D7 FOREIGN KEY (task_lists_id) REFERENCES task_lists (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planner_thing ADD CONSTRAINT FK_D14732E35346EAE1 FOREIGN KEY (planner_id) REFERENCES planner (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planner_thing ADD CONSTRAINT FK_D14732E3C36906A7 FOREIGN KEY (thing_id) REFERENCES thing (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE scenario_details ADD CONSTRAINT FK_6DE0CB8AE04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE scenario_objective ADD CONSTRAINT FK_22F22C77E04E49DF FOREIGN KEY (scenario_id) REFERENCES scenario (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE shopping_item ADD CONSTRAINT FK_6612795F23245BF9 FOREIGN KEY (shopping_list_id) REFERENCES shopping_list (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
