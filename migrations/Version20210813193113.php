<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210813193113 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE mnt_perfil_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mnt_perfil_rol_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mnt_ruta_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mnt_ruta_rol_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mnt_usuario_perfil_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE mnt_perfil (id INT NOT NULL, nombre VARCHAR(30) NOT NULL, codigo VARCHAR(5) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mnt_perfil_rol (id INT NOT NULL, id_perfil INT DEFAULT NULL, id_rol INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_256E507CB052C3AA ON mnt_perfil_rol (id_perfil)');
        $this->addSql('CREATE INDEX IDX_256E507C90F1D76D ON mnt_perfil_rol (id_rol)');
        $this->addSql('CREATE TABLE mnt_ruta (id INT NOT NULL, nombre VARCHAR(50) NOT NULL, uri TEXT DEFAULT NULL, nombre_uri TEXT DEFAULT NULL, mostrar BOOLEAN NOT NULL, icono VARCHAR(255) DEFAULT NULL, orden INT DEFAULT NULL, publico BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mnt_ruta_rol (id INT NOT NULL, id_ruta INT DEFAULT NULL, id_rol INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9B7D3C68488EEC8E ON mnt_ruta_rol (id_ruta)');
        $this->addSql('CREATE INDEX IDX_9B7D3C6890F1D76D ON mnt_ruta_rol (id_rol)');
        $this->addSql('CREATE TABLE mnt_usuario_perfil (id INT NOT NULL, id_perfil INT DEFAULT NULL, id_usuario INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2D3053D6B052C3AA ON mnt_usuario_perfil (id_perfil)');
        $this->addSql('CREATE INDEX IDX_2D3053D6FCF8192D ON mnt_usuario_perfil (id_usuario)');
        $this->addSql("ALTER TABLE mnt_perfil_rol ALTER COLUMN id SET DEFAULT nextval('mnt_perfil_rol_id_seq')");
        $this->addSql("ALTER TABLE mnt_usuario_perfil ALTER COLUMN id SET DEFAULT nextval('mnt_usuario_perfil_id_seq')");
        $this->addSql("ALTER TABLE mnt_ruta ALTER COLUMN id SET DEFAULT nextval('mnt_ruta_id_seq')");
        $this->addSql("ALTER TABLE mnt_ruta_rol ALTER COLUMN id SET DEFAULT nextval('mnt_ruta_rol_id_seq')");
        $this->addSql('ALTER TABLE mnt_perfil_rol ADD CONSTRAINT FK_256E507CB052C3AA FOREIGN KEY (id_perfil) REFERENCES mnt_perfil (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mnt_perfil_rol ADD CONSTRAINT FK_256E507C90F1D76D FOREIGN KEY (id_rol) REFERENCES mnt_rol (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mnt_ruta_rol ADD CONSTRAINT FK_9B7D3C68488EEC8E FOREIGN KEY (id_ruta) REFERENCES mnt_ruta (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mnt_ruta_rol ADD CONSTRAINT FK_9B7D3C6890F1D76D FOREIGN KEY (id_rol) REFERENCES mnt_rol (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mnt_ruta ADD id_ruta_padre INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mnt_ruta ADD CONSTRAINT FK_MNTRUTAPADRE FOREIGN KEY (id_ruta_padre) REFERENCES mnt_ruta(id) on update cascade on delete cascade');
        $this->addSql('ALTER TABLE mnt_usuario_perfil ADD CONSTRAINT FK_2D3053D6B052C3AA FOREIGN KEY (id_perfil) REFERENCES mnt_perfil (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mnt_usuario_perfil ADD CONSTRAINT FK_2D3053D6FCF8192D FOREIGN KEY (id_usuario) REFERENCES mnt_usuario (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE mnt_perfil_rol DROP CONSTRAINT FK_256E507CB052C3AA');
        $this->addSql('ALTER TABLE mnt_usuario_perfil DROP CONSTRAINT FK_2D3053D6B052C3AA');
        $this->addSql('ALTER TABLE mnt_ruta_rol DROP CONSTRAINT FK_9B7D3C68488EEC8E');
        $this->addSql('DROP SEQUENCE mnt_perfil_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mnt_perfil_rol_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mnt_ruta_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mnt_ruta_rol_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mnt_usuario_perfil_id_seq CASCADE');
        $this->addSql('DROP TABLE mnt_perfil');
        $this->addSql('DROP TABLE mnt_perfil_rol');
        $this->addSql('DROP TABLE mnt_ruta');
        $this->addSql('DROP TABLE mnt_ruta_rol');
        $this->addSql('DROP TABLE mnt_usuario_perfil');
    }
}
