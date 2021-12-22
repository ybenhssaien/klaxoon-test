<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210314185727 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bookmarks (id VARCHAR(36) NOT NULL, bookmark_url VARCHAR(1000) NOT NULL, url VARCHAR(1000) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, author VARCHAR(255) DEFAULT NULL, create_date DATETIME NOT NULL, update_date DATETIME DEFAULT NULL, type VARCHAR(10) NOT NULL, height DOUBLE PRECISION DEFAULT NULL, width DOUBLE PRECISION DEFAULT NULL, duration DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id VARCHAR(36) NOT NULL, bookmark_id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_6FBC942692741D25 (bookmark_id), INDEX tag_idx (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tags ADD CONSTRAINT FK_6FBC942692741D25 FOREIGN KEY (bookmark_id) REFERENCES bookmarks (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tags DROP FOREIGN KEY FK_6FBC942692741D25');
        $this->addSql('DROP TABLE bookmarks');
        $this->addSql('DROP TABLE tags');
    }
}
