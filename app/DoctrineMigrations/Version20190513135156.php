<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190513135156 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', qb_realm_id VARCHAR(20) DEFAULT NULL, sf_account_id VARCHAR(30) DEFAULT NULL, sf_instance_url VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth (id INT AUTO_INCREMENT NOT NULL, grant_type VARCHAR(15) DEFAULT NULL, app_type TINYINT(1) DEFAULT \'1\' NOT NULL COMMENT \'1 means saleforce account, 2 means quickbooks account\', client_id VARCHAR(100) DEFAULT NULL, client_secret VARCHAR(100) DEFAULT NULL, access_token VARCHAR(255) DEFAULT NULL, refresh_token VARCHAR(255) DEFAULT NULL, redirect_uri VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, User INT DEFAULT NULL, INDEX IDX_4DA78C42DA17977 (User), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, email VARCHAR(30) DEFAULT NULL, qb_cust_id VARCHAR(10) DEFAULT NULL, sf_cust_id VARCHAR(30) DEFAULT NULL, qb_sync_token VARCHAR(10) DEFAULT NULL, status VARCHAR(15) DEFAULT NULL, name VARCHAR(100) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, mailingStreet VARCHAR(50) DEFAULT NULL, mailingCity VARCHAR(20) DEFAULT NULL, mailingState VARCHAR(50) DEFAULT NULL, mailingPostalCode VARCHAR(10) DEFAULT NULL, mailingCountry VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_81398E09A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE oauth ADD CONSTRAINT FK_4DA78C42DA17977 FOREIGN KEY (User) REFERENCES user (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE oauth DROP FOREIGN KEY FK_4DA78C42DA17977');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09A76ED395');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE oauth');
        $this->addSql('DROP TABLE customer');
    }
}
