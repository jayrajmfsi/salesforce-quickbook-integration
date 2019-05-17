<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190517083241 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE oauth CHANGE client_id client_id VARCHAR(255) DEFAULT NULL, CHANGE client_secret client_secret VARCHAR(255) DEFAULT NULL, CHANGE access_token access_token LONGTEXT DEFAULT NULL, CHANGE refresh_token refresh_token LONGTEXT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE oauth CHANGE client_id client_id VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE client_secret client_secret VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE access_token access_token VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE refresh_token refresh_token VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
