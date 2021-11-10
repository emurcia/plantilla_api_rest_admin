<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210419192825 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE bt_bitacora_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE bt_error_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE bt_bitacora (id INT NOT NULL, id_usuario INT DEFAULT NULL, fecha_hora_reg TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT DATE_TRUNC(\'SECONDS\', NOW()) NOT NULL, ip_cliente VARCHAR(15) DEFAULT NULL, ip_servidor VARCHAR(15) DEFAULT NULL, metodo_http VARCHAR(10) NOT NULL, request_headers TEXT DEFAULT NULL, request_uri TEXT DEFAULT NULL, request_parameters TEXT DEFAULT NULL, request_content TEXT DEFAULT NULL, xrd_userid VARCHAR(255) DEFAULT NULL, xrd_messageid VARCHAR(255) DEFAULT NULL, xrd_client VARCHAR(255) DEFAULT NULL, xrd_service VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C4D314C0FCF8192D ON bt_bitacora (id_usuario)');
        $this->addSql('COMMENT ON COLUMN bt_bitacora.id IS \'Llave primaria de la tabla\'');
        $this->addSql('COMMENT ON COLUMN bt_bitacora.fecha_hora_reg IS \'Campo que almacena la fecha y hora en que se realiza una acción en la API\'');
        $this->addSql('COMMENT ON COLUMN bt_bitacora.ip_cliente IS \'Campo que almacena la IP del cliente Externo del Sistema Consultante del cuál el usuario externo esta realizando la acción\'');
        $this->addSql('COMMENT ON COLUMN bt_bitacora.ip_servidor IS \'Campo que almacena la IP del servidor Externo del Sistema Consultante\'');
        $this->addSql('COMMENT ON COLUMN bt_bitacora.metodo_http IS \'Campo que almacena el método HTTP del Servicio REST ejecutado\'');
        $this->addSql('COMMENT ON COLUMN bt_bitacora.request_headers IS \'Campo que almacena los parámetros proporcionados en el encabezado cuando se ejecutó la acción\'');
        $this->addSql('COMMENT ON COLUMN bt_bitacora.request_uri IS \'Campo que almacena la ruta que fue ejecutada para realizar la acción\'');
        $this->addSql('COMMENT ON COLUMN bt_bitacora.request_parameters IS \'Campo que almacena en formato json como string los parámetros ejecuados\'');
        $this->addSql('COMMENT ON COLUMN bt_bitacora.request_content IS \'Campo que almacena contenido de la petición POST | PUT | DELETE\'');
        $this->addSql('COMMENT ON COLUMN bt_bitacora.xrd_userid IS \'Campo que almacena el id de usaurio según la pasarela TENOLI (XROAD)\'');
        $this->addSql('COMMENT ON COLUMN bt_bitacora.xrd_messageid IS \'Campo que almacena el id del mensaje según la pasarela TENOLI (XROAD)\'');
        $this->addSql('COMMENT ON COLUMN bt_bitacora.xrd_client IS \'Campo que almacena cliente según la pasarela TENOLI (XROAD)\'');
        $this->addSql('COMMENT ON COLUMN bt_bitacora.xrd_service IS \'Campo que almacena nombre del servicio según la pasarela TENOLI (XROAD)\'');
        $this->addSql('CREATE TABLE bt_error (id INT NOT NULL, id_bitacora INT DEFAULT NULL, codigo INT DEFAULT NULL, mensaje TEXT DEFAULT NULL, trace TEXT DEFAULT NULL, fecha_hora_reg TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT DATE_TRUNC(\'SECONDS\', NOW()) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_89EC7DD9C05C126C ON bt_error (id_bitacora)');
        $this->addSql('COMMENT ON COLUMN bt_error.id IS \'Llave primaria de la tabla\'');
        $this->addSql('COMMENT ON COLUMN bt_error.id_bitacora IS \'Llave primaria de la tabla\'');
        $this->addSql('COMMENT ON COLUMN bt_error.codigo IS \'Campo que almacena el código HTTP o el código de la excepción que se produjo en la API\'');
        $this->addSql('COMMENT ON COLUMN bt_error.mensaje IS \'Campo que almacena el mensaje de la excepción que se produjo en la API\'');
        $this->addSql('COMMENT ON COLUMN bt_error.trace IS \'Campo que almacena el trazo de la ruta de la excepción que se produjo en la API\'');
        $this->addSql('COMMENT ON COLUMN bt_error.fecha_hora_reg IS \'Campo que almacena el fecha y hora de la excepción que se produjo en la API\'');
        $this->addSql('ALTER TABLE bt_bitacora ADD CONSTRAINT FK_C4D314C0FCF8192D FOREIGN KEY (id_usuario) REFERENCES mnt_usuario (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bt_error ADD CONSTRAINT FK_89EC7DD9C05C126C FOREIGN KEY (id_bitacora) REFERENCES bt_bitacora (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bt_error DROP CONSTRAINT FK_89EC7DD9C05C126C');
        $this->addSql('DROP SEQUENCE bt_bitacora_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE bt_error_id_seq CASCADE');
        $this->addSql('DROP TABLE bt_bitacora');
        $this->addSql('DROP TABLE bt_error');
    }
}
