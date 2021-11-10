/*
 *  validarDui
 *      Función que verifica si la estructura del número DUI
 *      es válida, comprobado contra el validador del número.
 *
 *  Parámetros:
 *      dui:  String de 10 caracteres en formato 99999999-9.
 *
 *  Retorna: Boolean TRUE | FALSE si es un número válido.
 */
const validarDui = ( dui ) => {
    // Inicializacion
    let valido = false;

    // Validando la longitud del numero de DUI
    if( dui.length === 10 ) {

        // Validando que no sea ceros todos los dígitos
        if( dui !== '00000000-0' ) {

            // Obteniendo los dígitos y el verificador
            let [ digitos, validador ] = dui.split('-');

            // Verficiando que la cadena
            if( typeof digitos !== 'undefined' && typeof validador !== 'undefined' ) {

                // Verificando que el validador sea de un solo caracter
                if (validador.length === 1) {

                    // Convirtiendo los digitos a array
                    digitos = digitos.split('');

                    // Convirtiendo los datos a tipo integer
                    validador = parseInt(validador, 10);
                    digitos   = digitos.map( digito => parseInt(digito, 10) );

                    // Obteniendo la suma corresponiente
                    let suma = digitos.reduce( (total, digito, index) => total += ( digito * ( 9 - index ) ), 0);

                    // Obteniendo el Modulo base 10
                    let mod = suma % 10;
                    mod = ( validador === 0 && mod === 0 ) ? 10 : mod;

                    let resta = 10 - mod;

                    if( resta === validador ) {
                        valido = true;
                    }
                }
            }
        }
    }

    return valido;
}

/*
 *  slugToCamelCase:
 *      Funcion que permite convertir un string separado por guion '-'
 *      en un string en formato camelCase
 *
 *  Parámetros:
 *      string: Cadena de texto dividido por guiones.
 *
 *  Retorna:
 *      String en formato camelCase
 *
 *  Ejemplo:
 *      Entrada: 'esto-es-un-string'
 *      Salida:  'estoEsUnString'
 */
const slugToCamelCase = (string) => {
    return string.replace(/-([a-z])/ig, function(all, letter) {
        return letter.toUpperCase();
    });
}

/*
 *  isJson
 *      Función que verifica si un string es un json válido.
 *
 *  Parámetros:
 *      str:  String que contiene el json.
 *
 *  Retorna: Boolean TRUE | FALSE si es un json válido o no.
 */
const isJson = ( str ) => {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }

    return true;
}

/*
 *  isBlank
 *      Función que verifica si el valor proporcionado se considera
 *      vacio según el tipo de dato.
 *
 *  Parámetros:
 *      value:  Mixed, number, boolean, string, arrays.
 *
 *  Retorna: Boolean TRUE | FALSE si es un valor vacio.
 */
const isBlank = ( value ) => {
    if( typeof( value ) == 'number' || typeof( value ) == 'boolean' ) {
        return false;
    }

    if( typeof( value ) == 'undefined' || value === null ) {
        return true;
    }

    if( typeof( value.length ) != 'undefined' ) {
        return value.length == 0;
    }

    var count = 0;
    for( var i in value ) {
        if( value.hasOwnProperty(i) ) {
            count ++;
        }
    }
    return count == 0;
}

export { validarDui, slugToCamelCase, isJson, isBlank };