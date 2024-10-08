<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170223112719 extends AbstractMigration
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
          'ALTER TABLE account_costs DROP FOREIGN KEY FK_99928ED91DBF857F'
        );
        $this->addSql(
          'ALTER TABLE tasks_costs DROP FOREIGN KEY FK_16916C0F1DBF857F'
        );
        $this->addSql('DROP TABLE account_costs');
        $this->addSql('DROP TABLE cost');
        $this->addSql('DROP TABLE tasks_costs');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
          'mysql' !== $this->connection->getDatabasePlatform()->getName(),
          'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
          'CREATE TABLE account_costs (account_id INT NOT NULL, cost_id INT NOT NULL, UNIQUE INDEX UNIQ_99928ED91DBF857F (cost_id), INDEX IDX_99928ED99B6B5FBA (account_id), PRIMARY KEY(account_id, cost_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
          'CREATE TABLE cost (id INT AUTO_INCREMENT NOT NULL, currency_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, value DOUBLE PRECISION NOT NULL, generatedAt DATETIME NOT NULL, createdAt DATETIME NOT NULL, note LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_182694FC38248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
          'CREATE TABLE tasks_costs (task_id INT NOT NULL, cost_id INT NOT NULL, UNIQUE INDEX UNIQ_16916C0F1DBF857F (cost_id), INDEX IDX_16916C0F8DB60186 (task_id), PRIMARY KEY(task_id, cost_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
          'ALTER TABLE account_costs ADD CONSTRAINT FK_99928ED91DBF857F FOREIGN KEY (cost_id) REFERENCES cost (id)'
        );
        $this->addSql(
          'ALTER TABLE account_costs ADD CONSTRAINT FK_99928ED99B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id)'
        );
        $this->addSql(
          'ALTER TABLE cost ADD CONSTRAINT FK_182694FC38248176 FOREIGN KEY (currency_id) REFERENCES currency (id)'
        );
        $this->addSql(
          'ALTER TABLE tasks_costs ADD CONSTRAINT FK_16916C0F1DBF857F FOREIGN KEY (cost_id) REFERENCES cost (id)'
        );
        $this->addSql(
          'ALTER TABLE tasks_costs ADD CONSTRAINT FK_16916C0F8DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id)'
        );
    }

}
