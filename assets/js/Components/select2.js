import { slugToCamelCase, isJson } from '../functions';

import '../../styles/Components/select2.scss';

/* Funcion que permite inicializar un Select2 especificando:
 *   element:        Selector del objeto
 *   blankOption:    Si es true, agrega una opcion en blanco al select2
 *   removeChildren: Si es true, remueve las opciones iniciales del select2
 *   options:        Opciones propias utilizadas por el select2
 */
export default (element, blankOption, removeChildren, options)  => {
    var attr = element[0].attributes;
    var newOptions = options;

    if ( removeChildren ) {
        element.children().remove();
    }

    if ( blankOption ) {
        if ( element.find('option[value=""]').length === 0 && element.find('option[value=null]').length === 0 && element.find('option:not([value])').length === 0 ) {
            element.prepend('<option/>').val(function() {
                return $('[selected]', this).val();
            });
        }
    }

    if (typeof options === 'undefined' || options == '' || options === null) {
        newOptions = {
            placeholder: 'Seleccione...',
            allowClear: true,
            width: '100%'
        }
    }

    if(attr) {
        jQuery.each(attr, function(akey, attribute) {
            let key   = attribute.name;
            let value = attribute.value;
            if (key.match('^data-select2-')) {
                var option = slugToCamelCase(key.replace('data-select2-', ''));

                newOptions[option] = isNaN(value) ? (value === 'true' ? true : (value === 'false' ? false : (isJson(value) ? JSON.parse(value) : value))) : parseInt(value);
            }
        });
    }

    element.select2(newOptions);
};