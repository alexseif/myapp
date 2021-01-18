<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191129125307 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE balance');
        $this->addSql('ALTER TABLE contract ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE notes ADD updated_at DATETIME NOT NULL, CHANGE createdat created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE dashboard_task_lists ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE currency ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE objective ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE cost_of_life ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE rate ADD updated_at DATETIME NOT NULL, CHANGE createdat created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE task_lists ADD updated_at DATETIME NOT NULL, CHANGE createdat created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE days ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE account_transactions ADD updated_at DATETIME NOT NULL, CHANGE createdat created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE accounts ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE work_log ADD updated_at DATETIME NOT NULL, CHANGE createdat created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE thing ADD updated_at DATETIME NOT NULL, CHANGE createdat created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE tasks ADD updated_at DATETIME NOT NULL, CHANGE createdat created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE balance (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, balanceAt DATE NOT NULL, amount INT NOT NULL, createdAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE account_transactions ADD createdAt DATETIME NOT NULL, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE accounts DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE contract DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE cost_of_life DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE currency DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE dashboard_task_lists DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE days DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE notes ADD createdAt DATETIME NOT NULL, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE objective DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE rate ADD createdAt DATETIME NOT NULL, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE task_lists ADD createdAt DATETIME NOT NULL, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE tasks ADD createdAt DATETIME NOT NULL, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE thing ADD createdAt DATETIME NOT NULL, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE work_log ADD createdAt DATETIME NOT NULL, DROP created_at, DROP updated_at');
    }
}
