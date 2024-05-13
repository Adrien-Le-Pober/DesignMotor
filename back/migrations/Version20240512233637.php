<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240512233637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE discount (id INT AUTO_INCREMENT NOT NULL, storage_duration INT NOT NULL, rate DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount_vehicle (discount_id INT NOT NULL, vehicle_id INT NOT NULL, INDEX IDX_AD8889974C7C611F (discount_id), INDEX IDX_AD888997545317D1 (vehicle_id), PRIMARY KEY(discount_id, vehicle_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE discount_vehicle ADD CONSTRAINT FK_AD8889974C7C611F FOREIGN KEY (discount_id) REFERENCES discount (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discount_vehicle ADD CONSTRAINT FK_AD888997545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discount_vehicle DROP FOREIGN KEY FK_AD8889974C7C611F');
        $this->addSql('ALTER TABLE discount_vehicle DROP FOREIGN KEY FK_AD888997545317D1');
        $this->addSql('DROP TABLE discount');
        $this->addSql('DROP TABLE discount_vehicle');
    }
}
