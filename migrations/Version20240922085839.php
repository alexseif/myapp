<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240922085839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        // Add this line to set a default value for updated_at
        $this->addSql('UPDATE work_log SET updated_at = CURRENT_TIMESTAMP WHERE updated_at IS NULL OR updated_at = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE work_log ADD CONSTRAINT FK_F5513F598DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_log DROP FOREIGN KEY FK_F5513F598DB60186');
    }
}
