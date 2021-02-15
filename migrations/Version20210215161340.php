<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210215161340 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bus_ride (id INT AUTO_INCREMENT NOT NULL, trip_id INT DEFAULT NULL, means_number VARCHAR(20) DEFAULT NULL, start_location VARCHAR(255) NOT NULL, end_location VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME DEFAULT NULL, means_type VARCHAR(50) NOT NULL, means_start_point VARCHAR(255) DEFAULT NULL, means_end_point VARCHAR(50) DEFAULT NULL, INDEX IDX_C430039AA5BC2E0E (trip_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flight_ride (id INT AUTO_INCREMENT NOT NULL, trip_id INT DEFAULT NULL, seat_number VARCHAR(20) DEFAULT NULL, means_number VARCHAR(20) DEFAULT NULL, baggage_info VARCHAR(255) DEFAULT NULL, start_location VARCHAR(255) NOT NULL, end_location VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME DEFAULT NULL, means_type VARCHAR(50) NOT NULL, means_start_point VARCHAR(255) DEFAULT NULL, means_end_point VARCHAR(50) DEFAULT NULL, INDEX IDX_1A0321B7A5BC2E0E (trip_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE journey (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE train_ride (id INT AUTO_INCREMENT NOT NULL, trip_id INT DEFAULT NULL, seat_number VARCHAR(20) DEFAULT NULL, means_number VARCHAR(20) DEFAULT NULL, start_location VARCHAR(255) NOT NULL, end_location VARCHAR(255) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME DEFAULT NULL, means_type VARCHAR(50) NOT NULL, means_start_point VARCHAR(255) DEFAULT NULL, means_end_point VARCHAR(50) DEFAULT NULL, INDEX IDX_F2D69A28A5BC2E0E (trip_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trip (id INT AUTO_INCREMENT NOT NULL, journey_id INT DEFAULT NULL, trip_start_date DATETIME DEFAULT NULL, INDEX IDX_7656F53BD5C9896F (journey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bus_ride ADD CONSTRAINT FK_C430039AA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id)');
        $this->addSql('ALTER TABLE flight_ride ADD CONSTRAINT FK_1A0321B7A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id)');
        $this->addSql('ALTER TABLE train_ride ADD CONSTRAINT FK_F2D69A28A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id)');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BD5C9896F FOREIGN KEY (journey_id) REFERENCES journey (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BD5C9896F');
        $this->addSql('ALTER TABLE bus_ride DROP FOREIGN KEY FK_C430039AA5BC2E0E');
        $this->addSql('ALTER TABLE flight_ride DROP FOREIGN KEY FK_1A0321B7A5BC2E0E');
        $this->addSql('ALTER TABLE train_ride DROP FOREIGN KEY FK_F2D69A28A5BC2E0E');
        $this->addSql('DROP TABLE bus_ride');
        $this->addSql('DROP TABLE flight_ride');
        $this->addSql('DROP TABLE journey');
        $this->addSql('DROP TABLE train_ride');
        $this->addSql('DROP TABLE trip');
    }
}
