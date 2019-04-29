<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190429061227 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Customer (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, email VARCHAR(30) DEFAULT NULL, qb_cust_id VARCHAR(10) DEFAULT NULL, sf_cust_id VARCHAR(30) DEFAULT NULL, qb_sync_token VARCHAR(10) DEFAULT NULL, status VARCHAR(15) DEFAULT NULL, name VARCHAR(100) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, mailingStreet VARCHAR(50) DEFAULT NULL, mailingCity VARCHAR(20) DEFAULT NULL, mailingState VARCHAR(50) DEFAULT NULL, mailingPostalCode VARCHAR(10) DEFAULT NULL, mailingCountry VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_784FEC5FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(30) NOT NULL, password VARCHAR(100) NOT NULL, qb_realmId VARCHAR(20) DEFAULT NULL, sf_accountId VARCHAR(30) DEFAULT NULL, sf_instance_url VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE OAuth (id INT AUTO_INCREMENT NOT NULL, grant_type VARCHAR(15) DEFAULT NULL, client_id VARCHAR(100) DEFAULT NULL, client_secret VARCHAR(100) DEFAULT NULL, access_token VARCHAR(255) DEFAULT NULL, refresh_token VARCHAR(255) DEFAULT NULL, redirect_uri VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, User INT DEFAULT NULL, INDEX IDX_6529F8FE2DA17977 (User), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Customer ADD CONSTRAINT FK_784FEC5FA76ED395 FOREIGN KEY (user_id) REFERENCES User (id)');
        $this->addSql('ALTER TABLE OAuth ADD CONSTRAINT FK_6529F8FE2DA17977 FOREIGN KEY (User) REFERENCES User (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Customer DROP FOREIGN KEY FK_784FEC5FA76ED395');
        $this->addSql('ALTER TABLE OAuth DROP FOREIGN KEY FK_6529F8FE2DA17977');
        $this->addSql('DROP TABLE Customer');
        $this->addSql('DROP TABLE User');
        $this->addSql('DROP TABLE OAuth');
    }
}
