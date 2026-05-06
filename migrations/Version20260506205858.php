<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260506205858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_7CE748A9692E25D ON reset_password_request');
        $this->addSql('ALTER TABLE reset_password_request CHANGE hashed_token hashed_token VARCHAR(100) NOT NULL, CHANGE selector selector VARCHAR(20) NOT NULL, CHANGE expired_at expires_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reset_password_request CHANGE selector selector VARCHAR(255) NOT NULL, CHANGE hashed_token hashed_token VARCHAR(255) NOT NULL, CHANGE expires_at expired_at DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7CE748A9692E25D ON reset_password_request (selector)');
    }
}
