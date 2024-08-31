<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181118161627 extends AbstractMigration
{

    public function getDescription(): string
    {
        return 'Migration description';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
          'mysql' != $this->connection->getDatabasePlatform()->getName(),
          'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
          'ALTER TABLE balance CHANGE balanceDate balanceDate DATE NOT NULL'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
          'mysql' != $this->connection->getDatabasePlatform()->getName(),
          'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
          'ALTER TABLE balance CHANGE balanceDate balanceDate DATETIME NOT NULL'
        );
    }

}
