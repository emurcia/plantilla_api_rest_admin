<?php

namespace App\JsonSchema\translations;

use JsonSchema\Exception\InvalidArgumentException;

class ConstraintErrorES
{
    const ADDITIONAL_ITEMS = 'additionalItems';
    const ADDITIONAL_PROPERTIES = 'additionalProp';
    const ALL_OF = 'allOf';
    const ANY_OF = 'anyOf';
    const DEPENDENCIES = 'dependencies';
    const DISALLOW = 'disallow';
    const DIVISIBLE_BY = 'divisibleBy';
    const ENUM = 'enum';
    const EXCLUSIVE_MINIMUM = 'exclusiveMinimum';
    const EXCLUSIVE_MAXIMUM = 'exclusiveMaximum';
    const FORMAT_COLOR = 'colorFormat';
    const FORMAT_DATE = 'dateFormat';
    const FORMAT_DATE_TIME = 'dateTimeFormat';
    const FORMAT_DATE_UTC = 'dateUtcFormat';
    const FORMAT_EMAIL = 'emailFormat';
    const FORMAT_HOSTNAME = 'styleHostName';
    const FORMAT_IP = 'ipFormat';
    const FORMAT_PHONE = 'phoneFormat';
    const FORMAT_REGEX= 'regexFormat';
    const FORMAT_STYLE = 'styleFormat';
    const FORMAT_TIME = 'timeFormat';
    const FORMAT_URL = 'urlFormat';
    const FORMAT_URL_REF = 'urlRefFormat';
    const INVALID_SCHEMA = 'invalidSchema';
    const LENGTH_MAX = 'maxLength';
    const LENGTH_MIN = 'minLength';
    const MAXIMUM = 'maximum';
    const MIN_ITEMS = 'minItems';
    const MINIMUM = 'minimum';
    const MISSING_ERROR = 'missingError';
    const MISSING_MAXIMUM = 'missingMaximum';
    const MISSING_MINIMUM = 'missingMinimum';
    const MAX_ITEMS = 'maxItems';
    const MULTIPLE_OF = 'multipleOf';
    const NOT = 'not';
    const ONE_OF = 'oneOf';
    const REQUIRED = 'required';
    const REQUIRES = 'requires';
    const PATTERN = 'pattern';
    const PREGEX_INVALID = 'pregrex';
    const PROPERTIES_MIN = 'minProperties';
    const PROPERTIES_MAX = 'maxProperties';
    const TYPE = 'type';
    const UNIQUE_ITEMS = 'uniqueItems';

    public function getMessage($name)
    {
        static $messages = array(
            self::ADDITIONAL_ITEMS => 'El elemento %s[%s] no está definido y la definición del esquema no permite elementos adicionales',
            self::ADDITIONAL_PROPERTIES => 'La propiedad %s no esta definida y la definicion del esquema no permite propiedades adicionales',
            self::ALL_OF => 'Debe de coincidir con todos los esquemas',
            self::ANY_OF => 'Debe de coincidir con al menos un esquema',
            self::DEPENDENCIES => '%s depende de %s, la cual no se encuentra presente',
            self::DISALLOW => 'El valor no es permitido',
            self::DIVISIBLE_BY => 'No es divisible por %d',
            self::ENUM => 'No coincide con alguno de los valores predefinidos %s',
            self::EXCLUSIVE_MINIMUM => 'El valor mínimo debe ser mayor o igual a %d',
            self::EXCLUSIVE_MAXIMUM => 'El valor máximo debe ser menor o igual a %d',
            self::FORMAT_COLOR => 'Color no válido',
            self::FORMAT_DATE => 'Fecha no válida %s, formato esperado YYYY-MM-DD',
            self::FORMAT_DATE_TIME => 'Fecha y Hora no válida %s, formato esperado YYYY-MM-DDThh:mm:ssZ',
            self::FORMAT_DATE_UTC => 'Tiempo no válido %s, entero esperado en milisegundos desde Epoch',
            self::FORMAT_EMAIL => 'Email no válido',
            self::FORMAT_HOSTNAME => 'Hostname no válido',
            self::FORMAT_IP => 'Dirección IP no válida',
            self::FORMAT_PHONE => 'Número de Teléfono no válido',
            self::FORMAT_REGEX=> 'Formato regex no válido %s',
            self::FORMAT_STYLE => 'Estilo no válido',
            self::FORMAT_TIME => 'Tiempo no válido %s, formato esperado hh:mm:ss',
            self::FORMAT_URL => 'Formato URL no válido',
            self::FORMAT_URL_REF => 'Formato de referencia URL no válido',
            self::LENGTH_MAX => 'La logintud de la cadena debe ser menor a %d caracter(es)',
            self::INVALID_SCHEMA => 'Esquema no válido',
            self::LENGTH_MIN => 'La longitud de la cadena deber ser mayor a %d caracter(es)',
            self::MAX_ITEMS => 'Debe haber un máximo de %d elementos en el array',
            self::MAXIMUM => 'Debe de tener un máximo de valores menores que o igual a %d',
            self::MIN_ITEMS => 'Debe de haber un mínimo de %d elementos en el array',
            self::MINIMUM => 'Debe de tener un mínimo de valores mayores que o igual a %d',
            self::MISSING_MAXIMUM => 'El uso de exclusiveMaximum requiere la definición de maximum',
            self::MISSING_MINIMUM => 'El uso de exclusiveMinimum requiere la definición de minimum',
            /*self::MISSING_ERROR => 'Used for tests; this error is deliberately commented out',*/
            self::MULTIPLE_OF => 'Debe ser múltiplo de %d',
            self::NOT => 'Coincide con un esquema con el cual no debería',
            self::ONE_OF => 'Debe de coincidir con exactamente un esquema',
            self::REQUIRED => 'La propiedad %s es requerida',
            self::REQUIRES => 'La presencia de la propiedad %s requiere que %s también esté presente',
            self::PATTERN => 'No coincide con el patrón regex %s',
            self::PREGEX_INVALID => 'El patrón %s no es válido',
            self::PROPERTIES_MIN => 'Debe de contener un mínimo de %d propiedad(es)',
            self::PROPERTIES_MAX => 'Debe de contener un máximo de %d propiedad(es)',
            self::TYPE => '%s valor encontrado, pero %s es requerido',
            self::UNIQUE_ITEMS => 'Hay valores duplicados en el array'
        );

        if ( !isset( $messages[$name] ) ) {
            throw new InvalidArgumentException('No se encuentra un mensaje de error para ' . $name);
        }

        return $messages[$name];
    }
}
