<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181118160414 extends AbstractMigration
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
          'CREATE TABLE balance (id INT AUTO_INCREMENT NOT NULL, item VARCHAR(255) NOT NULL, balanceDate DATETIME NOT NULL, balance INT NOT NULL, createdAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
          'mysql' != $this->connection->getDatabasePlatform()->getName(),
          'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE balance');
    }

}
