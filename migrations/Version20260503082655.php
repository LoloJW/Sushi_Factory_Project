<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260503082655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation_room_user (reservation_room_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F4D05DB23382CB16 (reservation_room_id), INDEX IDX_F4D05DB2A76ED395 (user_id), PRIMARY KEY (reservation_room_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE reservation_room_user ADD CONSTRAINT FK_F4D05DB23382CB16 FOREIGN KEY (reservation_room_id) REFERENCES reservation_room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_room_user ADD CONSTRAINT FK_F4D05DB2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation_room_user DROP FOREIGN KEY FK_F4D05DB23382CB16');
        $this->addSql('ALTER TABLE reservation_room_user DROP FOREIGN KEY FK_F4D05DB2A76ED395');
        $this->addSql('DROP TABLE reservation_room_user');
        $this->addSql('ALTER TABLE user DROP updated_at');
    }
}
