<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200306104247 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

//        $this->addSql('ALTER TABLE commande DROP amount');
//        $this->addSql('ALTER TABLE product_order ADD amount NUMERIC(6, 2) DEFAULT NULL');
        $this->addSql(
            'UPDATE product_order AS po
                    LEFT JOIN product p ON po.product_id = p.id
                  SET po.amount = COALESCE(p.price, 1)'
        );
        $this->addSql('ALTER TABLE product_order MODIFY amount NUMERIC(6, 2) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commande ADD amount DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE product_order DROP amount');
    }
}
