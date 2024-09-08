<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20170207202550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration description';
    }

    public function up(Schema $schema): void
    {
        // Ensure the migration can only be executed on MySQL
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        // Add your SQL statements here
        $this->addSql('ALTER TABLE cost_of_life DROP INDEX UNIQ_FA9B3EF238248176, ADD INDEX IDX_FA9B3EF238248176 (currency_id)');
    }

    public function down(Schema $schema): void
    {
        // Ensure the migration can only be executed on MySQL
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        // Add your SQL statements here
        $this->addSql('ALTER TABLE cost_of_life DROP INDEX IDX_FA9B3EF238248176, ADD UNIQUE INDEX UNIQ_FA9B3EF238248176 (currency_id)');
    }
}