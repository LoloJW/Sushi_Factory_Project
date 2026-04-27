<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260416133718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announcement (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, user_id INT NOT NULL, INDEX IDX_4DB9D91CA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, total INT NOT NULL, available INT NOT NULL, rented INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, subject_id INT NOT NULL, INDEX IDX_5A8A6C8DA76ED395 (user_id), INDEX IDX_5A8A6C8D23EDC87 (subject_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation_equipment (id INT AUTO_INCREMENT NOT NULL, rented_at DATETIME NOT NULL, return_at DATETIME DEFAULT NULL, user_id INT NOT NULL, equipment_id INT NOT NULL, INDEX IDX_C97FB41CA76ED395 (user_id), INDEX IDX_C97FB41C517FE9FE (equipment_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation_room (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, reserved_for DATE NOT NULL, start_time TIME NOT NULL, start_end TIME NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, room_id INT NOT NULL, INDEX IDX_64A69CF3A76ED395 (user_id), INDEX IDX_64A69CF354177093 (room_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, hashed_token VARCHAR(255) NOT NULL, selector VARCHAR(255) NOT NULL, requested_at DATETIME NOT NULL, expired_at DATETIME NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_7CE748A9692E25D (selector), INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE rooms (id INT AUTO_INCREMENT NOT NULL, room_number INT NOT NULL, capacity INT NOT NULL, projector TINYINT NOT NULL, whiteboard TINYINT NOT NULL, UNIQUE INDEX UNIQ_7CA11A96D7DED995 (room_number), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, private TINYINT NOT NULL, title VARCHAR(255) NOT NULL, reservation_id INT DEFAULT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_FBCE3E7AB83297E7 (reservation_id), INDEX IDX_FBCE3E7AA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_like (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, post_id INT DEFAULT NULL, subject_id INT DEFAULT NULL, announcement_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_D6E20C7A4B89032C (post_id), INDEX IDX_D6E20C7A23EDC87 (subject_id), INDEX IDX_D6E20C7A913AEA17 (announcement_id), INDEX IDX_D6E20C7AA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE reservation_equipment ADD CONSTRAINT FK_C97FB41CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation_equipment ADD CONSTRAINT FK_C97FB41C517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id)');
        $this->addSql('ALTER TABLE reservation_room ADD CONSTRAINT FK_64A69CF3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation_room ADD CONSTRAINT FK_64A69CF354177093 FOREIGN KEY (room_id) REFERENCES rooms (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7AB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation_room (id)');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_like ADD CONSTRAINT FK_D6E20C7A4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE user_like ADD CONSTRAINT FK_D6E20C7A23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE user_like ADD CONSTRAINT FK_D6E20C7A913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id)');
        $this->addSql('ALTER TABLE user_like ADD CONSTRAINT FK_D6E20C7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE test_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE test_user (first_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, last_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE announcement DROP FOREIGN KEY FK_4DB9D91CA76ED395');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D23EDC87');
        $this->addSql('ALTER TABLE reservation_equipment DROP FOREIGN KEY FK_C97FB41CA76ED395');
        $this->addSql('ALTER TABLE reservation_equipment DROP FOREIGN KEY FK_C97FB41C517FE9FE');
        $this->addSql('ALTER TABLE reservation_room DROP FOREIGN KEY FK_64A69CF3A76ED395');
        $this->addSql('ALTER TABLE reservation_room DROP FOREIGN KEY FK_64A69CF354177093');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7AB83297E7');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7AA76ED395');
        $this->addSql('ALTER TABLE user_like DROP FOREIGN KEY FK_D6E20C7A4B89032C');
        $this->addSql('ALTER TABLE user_like DROP FOREIGN KEY FK_D6E20C7A23EDC87');
        $this->addSql('ALTER TABLE user_like DROP FOREIGN KEY FK_D6E20C7A913AEA17');
        $this->addSql('ALTER TABLE user_like DROP FOREIGN KEY FK_D6E20C7AA76ED395');
        $this->addSql('DROP TABLE announcement');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE reservation_equipment');
        $this->addSql('DROP TABLE reservation_room');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE rooms');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE user_like');
    }
}
