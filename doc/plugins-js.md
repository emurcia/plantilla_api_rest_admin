# Plantilla REST

### Guía para la utilización de plugins del proyecto



Esta guía pretende dar a conocer al lector la manera en que puede utilizar los plugins de Javascritp integrados al la plantilla, esta guía no pretende profundizar en el uso del plugin sino dar a conocer como invocarlos o utilizarlos dentro del proyecto, si el lector quire conocer más a profundidad el uso de cada uno se recomienda ver la documentación oficial de cada uno.



## Tabla de Contenido

* [FOSJsRouting](#fosjsrouting)
* [waitMe](#waitme)
* [DataTables](#datatables)
* [datePicker](#datepicker)



## FOSJsRouting

**Documentación Oficial**: [FOSJsRouting](https://symfony.com/doc/2.x/bundles/FOSJsRoutingBundle/usage.html)

Librería que permite la generación de URIs o URLs desde JavaScript usando los nombre de rutas de Symfony.



**Declaración de ral ruta:**

Es necasario declarar la ruta y exponerla para que pueda ser generada con el plugin a través de la opición: **`options={"expose"=true}`** tal y como se muestra en el siguiente ejemplo.

```php
// src/AppBundle/Controller/DefaultController.php

/**
 * @Route("/foo/{id}/bar", options={"expose"=true}, name="my_route_to_expose")
 */
public function indexAction($foo) {
    // ...
}
```



**Actualización o generación del archivo fos_js_routes.json**

Cada vez que se declara una nueva ruta es necesario actualizar el archivo **`fos_js_routes.json`** que contiene las configuraciones de las rutas para su generación con el comando **`Routing.generate`**

```bash
# Symfony Flex
bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json
```



**Importación del plugin en JS con webpack y generacion de la ruta**

Usando webpack en el archivo JS dentro del directorio **`/assets/js`** se debe de importar la librería y generar la ruta como se muestra en el siguiente ejemplo:

```javascript
const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);
/* Generación de la ruta */
Routing.generate('my_route_to_expose');
```



## waitMe

**Documentacion Oficial:** [waitMe](https://github.com/vadimsva/waitMe)

Librería que permite el uso de pantallas de cargas.



**Uso**

```javascript
// ...
import 'waitme/waitMe.css'
import 'waitme/waitMe';

jQuery(document).ready(function($) {
	$('#id').waitMe({effect: 'stretch', text: 'Buscando...'});
}
```



## DataTables

**Documentación Oficial:** [DataTables](https://datatables.net/examples/index)

Librería que permite brindarle más funcionalidades a las tablas estáticas de HTML.



**Importación de la librería:**

Para este caso no se podrá utilizar webpack, debido a que la librería no está adaptada para usarse dicicha manera, por lo que se realizará de la manera tradicional a través del archivo **`twig.html`**, tal y como se muestra a continuación.

```twig
{% extends '@SonataAdmin/CRUD/action.html.twig' %}

{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('libraries/DataTables/datatables.min.css') }}">

    {{ encore_entry_link_tags('page-js') }}
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script type="text/javascript" charset="utf8" src="{{ asset('libraries/DataTables/datatables.min.js') }}"></script>
  
    {{ encore_entry_script_tags('page-js') }}
{% endblock %}
```



**Uso**

Usando webpack en el archivo JS del directorio **`/assets/js`**, se debe de importar las opciones por defecto de datatable y luego asignarselo a la tabla como se muestra a continuación.

```javascript
// ...
import dtOptions from '../Components/DataTables.js';

jQuery(document).ready(function($) {
	$('#id-table').DataTable(dtOptions);
}
```



## datePicker

**Documentación Oficial:** [flatpickr](https://flatpickr.js.org/examples/)

Libería para el uso de calendarios para la seleccion de fechas y horas.



**Uso**

```javascript
// ...
import datepicker from '../Components/datepicker';

jQuery(document).ready(function($) {
	const fecha = $('#' + admin.dataset.uniqid + '_date');
    const { flatpickr: pkfecha, imask: mkfecha } = datepicker(fecha[0]);
}
```

