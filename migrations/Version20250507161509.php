<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507161509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            <<<'SQL'
                ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CA76ED395
                SQL,
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
                SQL,
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE "user" ALTER password TYPE VARCHAR(60)
                SQL,
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE "user" ALTER password DROP DEFAULT
                SQL,
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE "user" ADD enabled BOOLEAN NOT NULL DEFAULT true
                SQL,
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(
            <<<'SQL'
                ALTER TABLE media DROP CONSTRAINT fk_6a2ca10ca76ed395
                SQL,
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE media ADD CONSTRAINT fk_6a2ca10ca76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
                SQL,
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE "user" ALTER password TYPE VARCHAR(60)
                SQL,
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE "user" ALTER password SET DEFAULT ''
                SQL,
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE "user" DROP enabled
                SQL,
        );
    }
}
