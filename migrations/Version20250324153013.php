<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250324153013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE point_de_vente ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE point_de_vente ADD CONSTRAINT FK_C9182F7BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C9182F7BA76ED395 ON point_de_vente (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493F95E273');
        $this->addSql('DROP INDEX IDX_8D93D6493F95E273 ON user');
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(50) DEFAULT NULL, ADD prenom VARCHAR(30) DEFAULT NULL, ADD telephone VARCHAR(20) DEFAULT NULL, ADD est_actif TINYINT(1) NOT NULL, DROP point_de_vente_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE point_de_vente DROP FOREIGN KEY FK_C9182F7BA76ED395');
        $this->addSql('DROP INDEX IDX_C9182F7BA76ED395 ON point_de_vente');
        $this->addSql('ALTER TABLE point_de_vente DROP user_id');
        $this->addSql('ALTER TABLE user ADD point_de_vente_id INT DEFAULT NULL, DROP nom, DROP prenom, DROP telephone, DROP est_actif');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493F95E273 FOREIGN KEY (point_de_vente_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D6493F95E273 ON user (point_de_vente_id)');
    }
}
