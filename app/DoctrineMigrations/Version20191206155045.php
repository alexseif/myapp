<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191206155045 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE recurr (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, date_start DATE DEFAULT NULL, date_until DATE DEFAULT NULL, count INT DEFAULT NULL, freq_interval INT DEFAULT NULL, freq INT DEFAULT NULL, by_day LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', by_month_day LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', by_year_day LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', by_month LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE recurr');
    }
}
