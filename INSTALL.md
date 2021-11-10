# Plantilla REST

A continuación se muestra la guía para la instalación de la plantilla para una REST, basada en:

- Symfony en Modo Microservicio/API.
- RESTful (friendsofsymfony/rest-bundle).
- Autenticación Json Web Token (lexik/jwt-authentication-bundle).
- Open Api Specification, AKA Swagger (nelmio/api-doc-bundle).
- JsonSchema (justinrainbow/json-schema).
- Sonata Admin 4.x

## Contenido

- [Requisitos](#requisitos)
  - [Software](#1-software)
  - [Sistema Operativo](#2-sistema-operativo)
- [Instalación del Sistema](#instalación-de-la-aplicación)
  - [Preparación del servidor](#1-preparación-del-servidor)
    - [Configuración de repositorios Debian](#11-configuración-de-repositorios-debian)
    - [Instalación de Docker](#12-instalación-de-docker)
    - [Instalación de Docker-Compose](#13-instalación-de-docker-compose)
  - [Instalación y configuración del Sistema](#2-instalación-y-configuración-del-sistema)
    - [Clonación del proyecto](#21-clonación-del-proyecto)
    - [Instalación de vendors](#22-instalación-de-vendors)
    - [Configurar la conexión a la base de datos](#23-configurar-las-conexiones-a-la-base-de-datos)
    - [Creación del esquema](#24-creación-del-esquema)
    - [Generar SSH Keys](#25-generar-ssh-keys)
    - [Brindar permisos directorios cache y logs](#26-brindar-permisos-directorios-cache-y-logs)
    - [Compilado de assets](#27-compilado-de-assets)
    - [Limpiar cache y assets](#28-limpiar-cache-y-assets)
    - [Creación del usuario inicial](#29-creación-del-usuario-inicial)
    - [Creación del VirtualHost](#210-creación-del-virtualhost)
    - [Pruebas de acceso a la api](#211-pruebas-de-acceso-a-la-api)

## Requisitos

### 1 Software

| Software          | Versión |
| ----------------- | ------- |
| Apache            | 2.4     |
| PHP               | 7.4     |
| Composer          | \>=2.0  |
| PostgreSQL        | \>= 11  |
| NodeJs            | \>=12   |
| Yarn              | \>=1.22 |
| Docker (opcional) | \>=19.0 |

### 2 Sistema Operativo

El sistema funciona bajo el Sistema Operativo Linux, en su distribución Debian Buster o superior, los pasos de instalación y configuración se especifican tomando como base este sistema operativo.

### 3 Cliente API

Para el consumo de la API es necesario que cliente se conecte a través del protocolo HTTP y permita ejecutar los métodos **GET, POST, PUT, DELETE**.

## Instalación de la aplicación

En esta sección se brinda una serie de pasos a seguir para la instalación del software, como se ha mencionado anteriormente, la instalación se realizará bajo el entorno del Sistema Operativo Linux en sus Distribución Debian Buster con una arquitectura de 64-bits.

### 1 Preparación del servidor

A continuación se listan los pasos para la preparación del servidor para entorno de desarrollo o producción.

El sistema puede ser instalado directamente dentro del sistema operativo cumpliendo con los requisitos de software listados en el [apartado 1](#1-software) o puede configurarse a través de docker.

#### 1.1 Configuración de repositorios Debian

Antes de comenzar con la instalación es necesario configurar los repositorios de los cuales se obtendrán los paquetes a instalar, cabe aclarar que si ya se posee repositorios configurados se puede omitir esta sección. Los pasos para la configuración de los repositorios son los siguientes:

1. Abrir una terminal e identificarse como usuario root:

```bash
su             # Presionar Enter
Contraseña:    # Ingresar Contraseña
```

2. Editar el archivo sources.list que se encuentra dentro del directorio `/etc/apt/`:

```bash
vim /etc/apt/sources.list # alternativa a vim: nano
```

Agregar los repositorios que se listan a continuación:

```bash
##Repositorios del MINSAL para debian stretch.
deb http://debian.salud.gob.sv/debian/ buster main contrib non-free
deb-src http://debian.salud.gob.sv/debian/ buster main contrib non-free

deb http://debian.salud.gob.sv/debian/ buster-updates main contrib non-free
deb-src http://debian.salud.gob.sv/debian/ buster-updates main contrib non-free

deb http://debian.salud.gob.sv/debian-security/ buster/updates main contrib non-free
deb-src http://debian.salud.gob.sv/debian-security/ buster/updates main contrib non-free
```

3. Actualizar la lista de paquetes de los repositorios.

```bash
aptitude update
```

Una vez hecho lo anterior ya se tienen los repositorios actualizados y con ello se puede proceder con la instalación de los paquetes necesarios para la la instalación de la plantilla REST.

#### 1.2 Instalación de Docker

Los pasos de instalación de Docker se describen en el siguiente enlace: [**Click aquí**](https://docs.docker.com/engine/install/debian/),

> Se recomienda la configuración del uso de Docker como usuario no root en Linux: [**Click aqui**](https://docs.docker.com/engine/install/linux-postinstall/)

#### 1.3 Instalación de Docker-Compose

Los pasos de instalación de Docker-compose se describen en el siguiente enlace: [**Click aquí**](https://docs.docker.com/compose/install/).

### 2 Instalación y configuración de la Plantilla

A continuación se listan los pasos para la instalación y configuración de la API-REST:

#### 2.1 Clonación del proyecto

Clonar el proyecto desde los repositorios oficiales ejecutando el siguiente comando:

```bash
git clone http://codigo.salud.gob.sv/plantillas/api-rest-admin.git
```

#### 2.2 Instalación de vendors

Descargar los vendors en el directorio raíz del proyecto ejecutando el siguiete comando:

```bash
wget https://next.salud.gob.sv/index.php/s/KtXdJBkBGDq7pXt/download -O vendors.tar.gz
```

Descomprimir los vendors descargados ejecutando el siguiente comando:

```
tar xzvf vendors.tar.gz
```

Instalar los vendors ejecutando el siguiente comando:

```bash
composer install
```

#### 2.3 Configurar la conexión a la base de datos

Si no se tiene creada la base de datos, debe ser creada. Se debe especificar en el archivo **`.env.local`** los datos de la conexión a la BD:

Crear el archivo **.env.local** desde el directorio raíz del proyecto ejecutando el siguiente comando

```bash
cp .env .env.local
```

Editar la siguiente línea del archivo **.env.local**

```bash
DATABASE_URL=postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=11&charset=utf8
```

#### 2.4 Creación del esquema

Si no se posee la estructura de tablas de la base de datos se puede crear las tablas a partir de las entidades de la plantilla ejecutando el comando que se lista a continuación, si ya se posee esquema se puede omitir este paso.

```bash
php bin/console doctrine:migrations:migrate
```

**Creación de los roles predefinidos.**

Antes de la creación del primer usuario es necesario crear los roles por defecto que se pueden asignar, para ello se ejecuta el siguiente script SQL en la base de datos:

```sql
INSERT INTO mnt_rol (id,name) VALUES
     (1,'ROLE_SUPER_ADMIN'),
     (2,'ROLE_ADMIN'),
     (3,'ROLE_ADMIN_USER_EDIT'),
     (4,'ROLE_ADMIN_USER_LIST'),
     (5,'ROLE_ADMIN_USER_VIEW'),
     (6,'ROLE_ADMIN_USER_CREATE'),
     (7,'ROLE_ADMIN_USER_DELETE'),
     (8,'ROLE_ADMIN_USER_EXPORT'),
     (9,'ROLE_ADMIN_USER_ALL'),
     (10,'ROLE_ADMINISTRADOR')
;

ALTER SEQUENCE mnt_rol_id_seq RESTART WITH 10;
```

#### 2.5 Generar SSH Keys

Crear la carpeta `jwt` dentro de la carpeta `config` del proyecto:

```bash
mkdir -p config/jwt
```

Generar el certificado privado utilizando el pass phrase de su preferencia:

```bash
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
```

Generar el certificado publico utilizando el mismo pass phrase:

```bash
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
```

Editar en el archivo `.env.local` y reemplazar **passphrase** por la asignada en la llave privada de los comandos anteriores:

```yaml
###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=passphrase
JWT_TOKENTTL=3600
###< lexik/jwt-authentication-bundle ###
```

Dar permisos de lectura al archivo `config/jwt/private.pem` ejecutando el siguiente comando:

```
chmod 755 config/jwt/*.pem
```

#### 2.6 Brindar permisos directorios cache y logs

```bash
setfacl -R -m u:www-data:rwx -m u:`whoami`:rwx var/cache/ var/log/
setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx var/cache/ var/log/
```

#### 2.7 Compilado de assets

Antes de proceder a realizar el compilado es necesario tener instalado NodeJS y Yarn según lo indicado en el [apartado 1](#1-software).

```bash
# Instalación del node_modules
yarn install
# Compilado de los assets en modo dev
yarn watch
# Compilado de asses ( solo para poroduccion )
yarn build
```

#### 2.8 Limpiar cache y assets

```bash
php bin/console cache:clear; php bin/console cache:clear --env=prod; php bin/console assets:install --symlink; php bin/console assets:install --symlink --env=prod
```

#### 2.9 Creación del usuario Inicial

Para poder iniciar sesion es necesario registrar los usuarios, para poder registrar un usuario, es necesario ejecutar el siguiente comando:

```bash
# Comando de creación de usuario
php bin/console app:user:create admin@mail.com password 'ROLE'
```

En donde:

- **admin@mail.com:** Es el email de usuario que se ha de crear.
- **password:** Contraseña a asignar al usuario
- **ROLE:** Role que se asingará al usuario.

#### 2.10 Creación del VirtualHost

Para probar la plantilla se recomienda la creación del VirtualHost para realizar las pruebas del proyecto o puede utilizarse a través de Docker.

Si se ha decidido utilizar la aplicación a traves de **`Docker`**, puede seguir los pasos que se describen en el siguiente enlace: [Ver aquí](http://codigo.salud.gob.sv/plantillas/docker)

#### 2.11 Pruebas de acceso a la API

Una vez realizado los pasos anteriores puede realizar las pruebas de acceso, para ello puede acceder a la documentación de la **[forma de uso](README.md#forma-de-uso)** del **[README.md](README.md)**
