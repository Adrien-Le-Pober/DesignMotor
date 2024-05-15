<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240515083349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE discount_rule (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount_rule_action (id INT AUTO_INCREMENT NOT NULL, discount_rule_id INT NOT NULL, type VARCHAR(48) NOT NULL, value INT NOT NULL, INDEX IDX_9E17883AC2466E40 (discount_rule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount_rule_condition (id INT AUTO_INCREMENT NOT NULL, discount_rule_id INT NOT NULL, type VARCHAR(48) NOT NULL, operator VARCHAR(48) NOT NULL, value VARCHAR(48) NOT NULL, INDEX IDX_1FD04A1AC2466E40 (discount_rule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE discount_rule_action ADD CONSTRAINT FK_9E17883AC2466E40 FOREIGN KEY (discount_rule_id) REFERENCES discount_rule (id)');
        $this->addSql('ALTER TABLE discount_rule_condition ADD CONSTRAINT FK_1FD04A1AC2466E40 FOREIGN KEY (discount_rule_id) REFERENCES discount_rule (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discount_rule_action DROP FOREIGN KEY FK_9E17883AC2466E40');
        $this->addSql('ALTER TABLE discount_rule_condition DROP FOREIGN KEY FK_1FD04A1AC2466E40');
        $this->addSql('DROP TABLE discount_rule');
        $this->addSql('DROP TABLE discount_rule_action');
        $this->addSql('DROP TABLE discount_rule_condition');
    }
}
