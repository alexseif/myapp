<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210104124301 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, task_list_id INT DEFAULT NULL, duration INT DEFAULT NULL, sizing INT DEFAULT NULL, due DATETIME DEFAULT NULL, eta DATETIME DEFAULT NULL, is_completed TINYINT(1) DEFAULT NULL, completed_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_527EDB25126F525E (item_id), INDEX IDX_527EDB25224F3C61 (task_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25224F3C61 FOREIGN KEY (task_list_id) REFERENCES task_lists (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE task');
    }
}
