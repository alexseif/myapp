<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170129232818 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE transactions DROP FOREIGN KEY FK_EAA81A4C12469DE2');
        $this->addSql('ALTER TABLE txn_tags DROP FOREIGN KEY FK_54FAA3458D7B4FB4');
        $this->addSql('ALTER TABLE txn_tags DROP FOREIGN KEY FK_54FAA34577E1607F');
        $this->addSql('DROP TABLE account_balances');
        $this->addSql('DROP TABLE account_payments');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE transactions');
        $this->addSql('DROP TABLE txn_tags');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE account_balances (id INT AUTO_INCREMENT NOT NULL, account_id INT DEFAULT NULL, balance INT NOT NULL, createdAt DATETIME NOT NULL, INDEX IDX_396906B99B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE account_payments (id INT AUTO_INCREMENT NOT NULL, account_id INT DEFAULT NULL, Amount INT NOT NULL, createdAt DATETIME NOT NULL, INDEX IDX_1D1C79849B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, category VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_3AF3466864C19C1 (category), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, tag VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_6FBC9426389B783 (tag), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transactions (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, amount INT NOT NULL, txn_date DATE NOT NULL, description LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_EAA81A4C12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE txn_tags (transactions_id INT NOT NULL, tags_id INT NOT NULL, INDEX IDX_54FAA34577E1607F (transactions_id), INDEX IDX_54FAA3458D7B4FB4 (tags_id), PRIMARY KEY(transactions_id, tags_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account_balances ADD CONSTRAINT FK_396906B99B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id)');
        $this->addSql('ALTER TABLE account_payments ADD CONSTRAINT FK_1D1C79849B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id)');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4C12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE txn_tags ADD CONSTRAINT FK_54FAA34577E1607F FOREIGN KEY (transactions_id) REFERENCES transactions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE txn_tags ADD CONSTRAINT FK_54FAA3458D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
    }
}
