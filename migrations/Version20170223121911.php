<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170223121911 extends AbstractMigration
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

        $this->addSql('ALTER TABLE task_lists ADD account_id INT DEFAULT NULL');
        $this->addSql(
          'ALTER TABLE task_lists ADD CONSTRAINT FK_CF8284859B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id)'
        );
        $this->addSql(
          'CREATE INDEX IDX_CF8284859B6B5FBA ON task_lists (account_id)'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
          'mysql' !== $this->connection->getDatabasePlatform()->getName(),
          'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
          'ALTER TABLE task_lists DROP FOREIGN KEY FK_CF8284859B6B5FBA'
        );
        $this->addSql('DROP INDEX IDX_CF8284859B6B5FBA ON task_lists');
        $this->addSql('ALTER TABLE task_lists DROP account_id');
    }

}
