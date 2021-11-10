<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\JsonSchema\translations\ConstraintErrorES;
use \JsonSchema\Validator AS JsonValidator;

class JsonSchema {

    private $container;
    private $requestStack;

    public function __construct(ContainerInterface $container, RequestStack $requestStack) {
        $this->container    = $container;
        $this->requestStack = $requestStack;
    }

    public function getTranslateErrors( $value, $error ) {
        $constraint = new ConstraintErrorES();
        $name       = $constraint ? $error['constraint']['name'] : '';
        $message    = $constraint ? $constraint->getMessage($name) : '';
        $errorMsg   = "";

        if( !isset( $error['property'] ) || !isset( $error['constraint'] ) ) {

            if( !isset( $error['property'] ) ) {
                throw new \Exception("Error la propiedad \"property\" del Array de Error no esta definido.");
            }

            if( !isset( $error['constraint'] ) ) {
                throw new \Exception("Error la propiedad \"constraint\" del Array de Error no esta definido.");
            }

        }

        $errorMsg = ucfirst( vsprintf( $message, array_map(function ($val) {
                if (is_scalar($val)) {
                    return $val;
                }

                return json_encode($val);
            }, array_values($error['constraint']['params']))));

        return $errorMsg;
    }

    public function validate($content, $jsonSchemaFile)
    {
        // Verifying valid json structure
        try {
            $json = json_decode($content);
            $data = json_decode($content, true);
        } catch(\Exception $ex) {
            $response = array(
                "message" => "El json proporcionado posee una estructura no válida",
                "data"    => []
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }

        // Verifying data is not empty
        if( empty($data) ){
            $response = array(
                "message" => "No se han encontrado datos en la peticion.",
                "data"    => []
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }

        try {
            $jsonSchema = json_decode( file_get_contents( __DIR__.'/../JsonSchema/schemas/'.$jsonSchemaFile ) );
        } catch (\Exception $ex) {
            $response = array(
                "message" => "Se ha producido un error interno.",
                "data"    => []
            );

            throw new HttpException( JsonResponse::HTTP_INTERNAL_SERVER_ERROR, json_encode($response) );
        }

        // Verifying json against json schema
        $validator  = new JsonValidator();
        $errorArray = [];

        $validator->validate( $json, $jsonSchema );

        // if json is not valid, build response structure
        if( $validator->isValid() === false ) {
            // extracts the specific error by property
            foreach ($validator->getErrors() as $error) {
                $errorArray[] = array( 'propiedad' => $error['property'], 'error' => $this->getTranslateErrors( null, $error, false ) );
            }

            $response = array(
                'message' => 'Se han encontrado errores en la peticion.',
                'errors'  => $errorArray
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }

        return true;
    }
}
