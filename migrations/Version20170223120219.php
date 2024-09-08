<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170223120219 extends AbstractMigration
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
          'CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE accounts ADD client_id INT DEFAULT NULL');
        $this->addSql(
          'ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EAC19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)'
        );
        $this->addSql(
          'CREATE INDEX IDX_CAC89EAC19EB6921 ON accounts (client_id)'
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
          'ALTER TABLE accounts DROP FOREIGN KEY FK_CAC89EAC19EB6921'
        );
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP INDEX IDX_CAC89EAC19EB6921 ON accounts');
        $this->addSql('ALTER TABLE accounts DROP client_id');
    }

}
