# Plantilla REST

### Ministerio de Salud de El Salvador (MINSAL)

<div align="center">
	<a href="http://codigo.salud.gob.sv/plantillas/api-rest-admin">
		<img alt="SUIS" title="SUIS" src="https://next.salud.gob.sv/index.php/s/yXfAcAnwakNb779/preview" width="250" style="width: 250px;">
	</a>
</div>

## Tabla de Contenido

- [Descripción](#descripción)
- [Instalación](#instalación)
- [Forma de uso](#forma-de-uso)
- [Primeros pasos](#primeros-pasos)
- [Uso Plugins JS](#uso-plugins-js)
- [Colaboradores](#colaboradores)
- [Enlaces de ayuda](#enlaces-de-ayuda)
- [Licencia](#licencia)

## Descripción

El objetivo de este proyecto es facilitar y agilizar la generación de Servicios Web y sitio de Administración del MINSAL, ofreciendo una estructura básica para iniciar un proyecto nuevo.

Proyecto base que puede servir para el desarrollo de un Backend completo (API - REST-FULL, y Admin), basada en:

- Symfony 5.1 en Modo Microservicio/API.
- RESTful (friendsofsymfony/rest-bundle).
- Autenticación Json Web Token (lexik/jwt-authentication-bundle).
- Open Api Specification, AKA Swagger (nelmio/api-doc-bundle).
- JsonSchema (justinrainbow/json-schema).
- Administración de usuarios y catálogos (sonata-project/SonataAdminBundle).

## Instalación

Requisitos y pasos de instalación se encuentran definidos en el archivo [**INSTALL.md**](INSTALL.md), seguir dicha guía para proceder con la instalación y posteriormente su uso.

## Forma de uso

### Admin

La administración de catálogos y/o usuarios puede realizarse a través del framework SonataAdmin, el cual permite gestionar de manera rápida el CRUD de estos, para ello solamente se tiene que ingresar al dominio raíz o la uri: **`/admin/login`**, accediendo a la siguiente interfaz:

![admin-login](https://next.salud.gob.sv/index.php/s/pbQ44JEEjzcDrKC/preview)

Para más información sobre como realizar el CRUD acceder a la documentación [oficial de SonataAdmin](https://symfony.com/doc/4.x/bundles/SonataAdminBundle/getting_started/creating_an_admin.html).

### API

Gracias a la integración de Open Api Specification (Swagger) la plantilla pone a disposición del cliente el listado de servicios disponibles (Endpoints), los cuales pueden ser consumidos a través de un cliente REST o del Navegador Web.

**Cliente REST**

```bash
curl -X GET -H "Accept: application/json" http://localhost/api/doc.json
```

**Navegador Web**

Ingresar a: [http://localhost/api/doc](http://localhost/api/doc), y se mostrará una pantalla como la siguiente:

![imagen-documentacion](https://next.salud.gob.sv/index.php/s/bSCi2MtxtgzQz8N/preview)

### Autenticación

El método de autenticación definido e integrado a la plantilla de desarrollo de APIs es **`JWT`** el cuál utiliza un token para toda la comunicación que se realiza a través de los endpoints que han sido asegurados.

Método: **`POST`**

URI: **`/api/login_check`**

**Headers:**

|                            Parámetro | Descripción                                                                                              |
| -----------------------------------: | -------------------------------------------------------------------------------------------------------- |
| `Content-type` <br />_semi-opcional_ | Parámetro que le indica al servidor que tipo de contenido es enviado, valor a enviar: `application/json` |

**Query String:**

> No se requiere ningún parámetro de búsqueda.

**Body: **

Formato: **`JSON`**

|                    Parámetro | Descripción                                                            |
| ---------------------------: | ---------------------------------------------------------------------- |
| `username` <br />_requerido_ | Nombre de usuario con el que se iniciará sesión para obtener el token. |
| `password` <br />_requerido_ | Contraseña con el que se iniciará sesión para obtener el token.        |

```json
{
  "username": "username",
  "password": "passwrod"
}
```

**Response:**

```
HTTP 200 OK
```

```json
{
  "token": "string"
}
```

**Códigos de respuesta:**

|                           Código | Descripción                                                                                                                                                                   |
| -------------------------------: | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
|                    `200`<br />OK | Implica que la petición fue completada exitosamente                                                                                                                           |
|           `400`<br />Bad Request | Implica que hubo un error en la petición, <br />esto puede darse debido a que alguno de los parámetros<br />requeridos de Encabezado o Query String no ha sido proporcionado. |
|          `401`<br />Unauthorized | Implica que los datos de acceso son erróneo o que no se posee<br />privilegio para acceder al recurso.                                                                        |
| `500`<br />Internal Server Error | Indica que hubo un error interno dentro de la API.                                                                                                                            |

**Ejemplo de consumo:**

Request:

```bash
curl -X POST -H "Content-Type: application/json" http://localhost/api/login_check -d '{"username":"user","password":"pass"}'
```

En donde:

- **user:** Es el nombre de usuario creado en el **[paso 2.9](INSTALL.md#29-creación-del-usuario-inicial)** del **[INSTALL.md](INSTALL.md)**.
- **password:** Es la contraseña del usuario creado en el **[paso 2.9](INSTALL.md#29-creación-del-usuario-inicial)** del **[INSTALL.md](INSTALL.md)**.

Response:

```json
{
  "token": "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJleHAiOjE0MzQ3Mjc1MzYsInVzZXJuYW1lIjoia29ybGVvbiIsImlhdCI6IjE0MzQ2NDExMzYifQ.nh0L_wuJy6ZKIQWh6OrW5hdLkviTs1_bau2GqYdDCB0Yqy_RplkFghsuqMpsFls8zKEErdX5TYCOR7muX0aQvQxGQ4mpBkvMDhJ4-pE4ct2obeMTr_s4X8nC00rBYPofrOONUOR4utbzvbd4d2xT_tj4TdR_0tsr91Y7VskCRFnoXAnNT-qQb7ci7HIBTbutb9zVStOFejrb4aLbr7Fl4byeIEYgp2Gd7gY"
}
```

## Primeros pasos

Como parte de esta plantilla se brinda una guía de inicio rápido para la creación de los endpoints de la API de manera muy básica, la intención es brindar al lector conceptos básicos que le permitan crear su primer API, depende de este profundizar en los temas para la creación de APIs mas complejas. Se recomienda leer los enlaces de la documentación a las tecnologías utilizadas.

[**Ver guía de inicio rápido**](./doc/guia-inicio-rapido.md)

## Uso Plugins JS

Como parte de esta plantilla se brinda una guía de uso de los plugins JS incorporados en el proyecto, esta guía no pretende profundizar en el uso del plugin sino dar a conocer como invocarlos o utilizarlos dentro del proyecto, si el lector quire conocer más a profundidad el uso de cada uno se recomienda ver la documentación oficial de cada uno

[**Ver guía de plugins JS**](./doc/plugins-js.md)

## Colaboradores

El proyecto es de propiedad intelectual del Ministerio de Salud de El Salvador y ha sido desarrollado en colaboración con las siguientes personas:

<div align="center">
    <table>
        <tr>
            <td align="center">
                <div align="center">
                    <a href="http://codigo.salud.gob.sv/caromero"  target="_blank"><img  style="width: 90px; height: 90px;" width="90" src="http://codigo.salud.gob.sv/uploads/-/system/user/avatar/13/avatar.png"></a><br />
                    Aaron Romero<br/>
                    <a href="mailto:caromero@salud.gob.sv">caromero@salud.gob.sv</a>
                </div>
            </td>
            <td align="center">
                <div align="center">
                    <a href="http://codigo.salud.gob.sv/crorozco"  target="_blank"><img  style="width: 90px; height: 90px;" width="90" src="http://codigo.salud.gob.sv/uploads/-/system/user/avatar/8/avatar.png"></a><br />
                    Caleb Rodriguez<br/>
                    <a href="mailto:crorozco@salud.gob.sv">crorozco@salud.gob.sv</a>
                </div>
            </td>
        </tr>
    </table>
</div>
<div align="center">
    <b>Dirección de Tecnologías de Información y Comunicaciones (DTIC).</b><br />
    <b>Ministerio de Salud</b><br />
    <a href="http://www.salud.gob.sv" alt="minsal" target="_blank">www.salud.gob.sv</a>
</div>

## Enlaces de ayuda

A continuación se presentan enlaces externos de ayuda referentes a tecnologías utilizadas para el desarrollo del proyecto:

- [Symfony](https://symfony.com/download) en Modo Microservicio/API.

- RESTful ([friendsofsymfony/rest-bundle](https://symfony.com/doc/master/bundles/FOSRestBundle/index.html)).

- Autenticación Json Web Token ([lexik/jwt-authentication-bundle](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md)).

- Open Api Specification, AKA Swagger ([nelmio/api-doc-bundle](https://symfony.com/doc/current/bundles/NelmioApiDocBundle/index.html)).

- JsonSchema ([justinrainbow/json-schema](https://github.com/justinrainbow/json-schema/tree/6.0.0-dev)).

- Gestor de contenedores [Docker](https://docs.docker.com/).

- Gestor de control de cambios [Git](https://git-scm.com/doc).

- Administración [SonataProyect](https://sonata-project.org/bundles/)

## Licencia

<a rel="license" href="https://www.gnu.org/licenses/gpl-3.0.en.html"><img alt="Licencia GNU GPLv3" style="border-width:0" src="https://next.salud.gob.sv/index.php/s/qxdZd5iwcqCyJxn/preview" width="96" /></a>

Este proyecto está bajo la <a rel="license" href="http://codigo.salud.gob.sv/plantillas/api-rest-admin/blob/master/LICENSE">licencia GNU General Public License v3.0</a>
