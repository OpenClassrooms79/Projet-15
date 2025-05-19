<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519194146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                ALTER TABLE "user" ALTER enabled TYPE BOOLEAN
                SQL,
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE "user" ALTER enabled DROP DEFAULT
                SQL,
        );

        $this->addSql(
            <<<'SQL'
                UPDATE "user" SET password = '$2y$13$7JS0ehfU8vZhB3Q8o1sPGuoQxkiPGXRGgrAizmNfI5Sgy.Dqt9xoW' WHERE email <> 'ina@zaoui.com'
                SQL,
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(
            <<<'SQL'
                ALTER TABLE "user" ALTER enabled TYPE BOOLEAN
                SQL,
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE "user" ALTER enabled SET DEFAULT true
                SQL,
        );

        $this->addSql(
            <<<'SQL'
                UPDATE "user" SET password = '' WHERE email <> 'ina@zaoui.com'
                SQL,
        );
    }
}
