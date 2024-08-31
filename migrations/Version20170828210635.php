<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170828210635 extends AbstractMigration
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

        $this->addSql('ALTER TABLE work_log ADD createdAt DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
          'mysql' !== $this->connection->getDatabasePlatform()->getName(),
          'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE work_log DROP createdAt');
    }

}
