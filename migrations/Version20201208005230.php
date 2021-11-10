<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201208005230 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE mnt_rol_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mnt_usuario_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE refresh_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE mnt_rol (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mnt_usuario (id INT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_suspended BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A1A94280E7927C74 ON mnt_usuario (email)');
        $this->addSql('CREATE TABLE mnt_usuario_rol (id_usuario INT NOT NULL, id_rol INT NOT NULL, PRIMARY KEY(id_usuario, id_rol))');
        $this->addSql('CREATE INDEX IDX_36D5D86FCF8192D ON mnt_usuario_rol (id_usuario)');
        $this->addSql('CREATE INDEX IDX_36D5D8690F1D76D ON mnt_usuario_rol (id_rol)');
        $this->addSql('CREATE TABLE refresh_tokens (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token)');
        $this->addSql("ALTER TABLE mnt_usuario ALTER COLUMN id SET DEFAULT nextval('mnt_usuario_id_seq')");
        $this->addSql("ALTER TABLE mnt_rol ALTER COLUMN id SET DEFAULT nextval('mnt_rol_id_seq')");
        $this->addSql('ALTER TABLE mnt_usuario_rol ADD CONSTRAINT FK_36D5D86FCF8192D FOREIGN KEY (id_usuario) REFERENCES mnt_usuario (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mnt_usuario_rol ADD CONSTRAINT FK_36D5D8690F1D76D FOREIGN KEY (id_rol) REFERENCES mnt_rol (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mnt_usuario ADD token_valid_after TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN mnt_usuario.token_valid_after IS \'Fecha y hora a partir de la cual los tokens del usuario son válidos\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE mnt_usuario_rol DROP CONSTRAINT FK_36D5D8690F1D76D');
        $this->addSql('ALTER TABLE mnt_usuario_rol DROP CONSTRAINT FK_36D5D86FCF8192D');
        $this->addSql('ALTER TABLE mnt_usuario DROP token_valid_after');
        $this->addSql('DROP SEQUENCE mnt_rol_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mnt_usuario_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE refresh_tokens_id_seq CASCADE');
        $this->addSql('DROP TABLE mnt_rol');
        $this->addSql('DROP TABLE mnt_usuario');
        $this->addSql('DROP TABLE mnt_usuario_rol');
        $this->addSql('DROP TABLE refresh_tokens');
    }
}
