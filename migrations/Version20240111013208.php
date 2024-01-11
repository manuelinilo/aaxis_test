<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240111013208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create products table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE products (
                id SERIAL PRIMARY KEY NOT NULL, 
                sku VARCHAR(50) NOT NULL UNIQUE, 
                product_name VARCHAR(250) NOT NULL, 
                description TEXT, 
                created_at TIMESTAMP NOT NULL, 
                updated_at TIMESTAMP DEFAULT NULL)
        ');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE products');
    }
}

