<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210311091519 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE proposal_details (id INT AUTO_INCREMENT NOT NULL, proposal_id INT NOT NULL, title VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_27823BC1F4792058 (proposal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proposal (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, status VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_BFE5947219EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE proposal_details ADD CONSTRAINT FK_27823BC1F4792058 FOREIGN KEY (proposal_id) REFERENCES proposal (id)');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE5947219EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE proposal_details DROP FOREIGN KEY FK_27823BC1F4792058');
        $this->addSql('DROP TABLE proposal_details');
        $this->addSql('DROP TABLE proposal');
    }
}
