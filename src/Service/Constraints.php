<?php
// src/Service/Constraints.php
namespace App\Service;

class Constraints {

    public function isBlank( $value ) {
        $status = false;

        if ( false === $value || ( empty($value) && '0' != $value ) ) {
            $status = true;
        }

        return $status;
    }

    public function isJSON( $string ) {
        // verificando si es un json valido
        $isJson = is_string($string) && is_array( json_decode( $string, true ) ) && ( json_last_error() == JSON_ERROR_NONE ) ? true : false;

        return $isJson;
    }

    public function validarDui( $dui ) {
        $valid = false;

        if( preg_match("/^[0-9-]+$/", $dui) ) {

            // Validar la longitud del numero de DUI
            if( strlen($dui) === 10 ) {

                // Validar que no sean cero todos los dígitos
                if( $dui !== '00000000-0' ) {
                    $parts = explode('-', $dui);

                    // Verificar que se pueda separar mediante (-)
                    if( isset($parts[0]) && isset($parts[1]) && count($parts) == 2 ){
                        $digits    = $parts[0];
                        $validator = $parts[1];

                        // Verificando que el validador sea de un solo caracter
                        if( strlen( $validator ) == 1 ){

                            // Convirtiendo los digitos a array
                            $digits = str_split($digits);

                            // Convirtiendo los datos a tipo integer
                            $digits    = array_map('intval', $digits);
                            $validator = intval($validator);
                            $suma      = 0;

                            // Realizando suma
                            array_walk( $digits, function($val, $key) use(&$suma){
                                $suma += ( $val * ( 9 - $key ) );
                            });

                             // Obteniendo el Modulo base 10
                            $mod = $suma % 10;
                            $mod = ( $validator === 0 && $mod === 0 ) ? 10 : $mod;
                            $resta = 10 - $mod;

                            if( $resta == $validator ){
                                $valid = true;
                            }
                        }
                    }
                }
            }
        }
        return $valid;
    }

    public function isValidDate($date, $format = 'Y-m-d H:i:s'){

        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;

    }
}