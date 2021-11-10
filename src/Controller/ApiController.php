<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JsonSchema;
use App\Service\Mail;
use Doctrine\DBAL\Driver\Connection;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;
use JMS\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use \JsonSchema\Validator AS JsonValidator;

/**
 * Class ApiController
 *
 * @Route("/api")
 */
class ApiController extends AbstractFOSRestController
{
    private $serializer;
    private $kernel;
    private $connection;
    private $security;

    public function __construct( SerializerInterface $serializer, KernelInterface $kernel, Connection $connection, Security $security)
    {
        $this->serializer = $serializer;
        $this->kernel     = $kernel;
        $this->connection = $connection;
        $this->security   = $security;
    }

    // Documentacion de uso de annotations con swagger
    // https://github.com/zircote/swagger-php/blob/master/Examples/petstore-3.0/controllers/Pet.php
    /**
     * @Rest\Post("/login_check", name="user_login_check")
     *
     * @OA\Response(
     *     response=200,
     *     description="Se ha iniciado sesión exitosamente.",
     *     @OA\JsonContent(
     *         type= "object",
     *         @OA\Property(
     *             property="token",
     *             type="string",
     *             description="Bearer token"
     *         ),
     *         @OA\Property(
     *             property="refresh_token",
     *             type="string",
     *             description="Refresh token"
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Se han encontrado errores en la petición. Debe proveer Usuario y Contraseña."
     * )
     *
     * @OA\Response(
     *     response=500,
     *     description="No se ha podido realizar el inicio de sesión."
     * )
     *
     * @OA\RequestBody(
     *     description="Nombre de usuario y password para autenticación.",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *             type="object",
     *             required={"username", "password"},
     *             @OA\Property(
     *                 property="username",
     *                 description="Nombre de usuario",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 description="Contraseña",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Tag(name="Usuario")
     */
    public function getLoginCheckAction() {
    }

    /**
     * @Rest\Get("/v1/usuarios/{email}", name="get_usuario")
     *
     * @OA\Parameter(
     *     name="email",
     *     in="path",
     *     required=true,
     *     description="Email del usuario",
     *     @OA\Schema(
     *         type="string"
     *     )
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="La petición ha sido procesada exitosamente",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Mensaje de exito"
     *         ),
     *         @OA\Property(
     *             property="data",
     *             type="array",
     *             description="Datos del usuario.",
     *             @OA\Items(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="Correo electronico del usuario"
     *                 ),
     *                 @OA\Property(
     *                     property="fecha_creacion",
     *                     type="string",
     *                     description="Fecha y hora de creacion del usuario. DateTime formato ISO 8601 YYYY-MM-DDTHH:MI:SSZ"
     *                 ),
     *                 @OA\Property(
     *                     property="ultima_conexion",
     *                     type="string",
     *                     description="Fecha y hora de última conexión del usuario. DateTime formato ISO 8601 YYYY-MM-DDTHH:MI:SSZ"
     *                 ),
     *                 @OA\Property(
     *                     property="deshabilitado",
     *                     type="boolean",
     *                     description="Indica si el usuario se encuentra deshabilitado"
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Se han encontrado errores en la petición.",
     *     @OA\JsonContent(
     *         type= "object",
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Mensaje de error"
     *         ),
     *         @OA\Property(
     *             property="errors",
     *             type="array",
     *             description="Detalle de errores.",
     *             @OA\Items(
     *                 @OA\Property(
     *                     property="propiedad",
     *                     type="string",
     *                     description="Propiedad asociada al error. Si es null aplica sobre el objeto completo."
     *                 ),
     *                 @OA\Property(
     *                     property="error",
     *                     type="string",
     *                     description="Descripción del error."
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=500,
     *     description="Error interno del servidor."
     * )
     *
     * @OA\Tag(name="Usuario")
     */
    public function getUsuario(string $email, Request $request, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager) {
        // Obteniendo el usuasrio
        $em      = $this->getDoctrine()->getManager();
        $payload = $jwtManager->decode( $tokenStorageInterface->getToken() );
        $emailPayload = $payload['username'];

        $mailerBaseDomain = $_ENV['MAILER_BASE_DOMAIN'];
        $email = $email.( preg_match('/@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/', $email) ? '' : '@'.$mailerBaseDomain );
        if( $email !== $emailPayload ) {
            $response = array(
                'message' => 'Se han encontrado errores en la peticion',
                'errors'  => [
                    [
                        "propiedad" => "email",
                        "error"     => sprintf('Email proporcionado: "%s" no es un email válido.', $email)
                    ]
                ]
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }

        $user = $em->getRepository('App:User')->findOneBy(['email' => $email]);
        if (!$user) {
            $response = array(
                'message' => 'Se han encontrado errores en la peticion',
                'errors'  => [
                    [
                        "propiedad" => "email",
                        "error"     => sprintf('Usuario identificado por el correo "%s" no existe.', $email)
                    ]
                ]
            );

            throw new HttpException( JsonResponse::HTTP_NOT_FOUND, json_encode($response) );
        }

        $data = [
            'email'           => $user->getEmail(),
            'fecha_creacion'  => $user->getCreatedAt() ? $user->getCreatedAt()->format('Y-m-d\TH:i:s\Z') : null,
            'ultima_conexion' => $user->getLastLogin() ? $user->getLastLogin()->format('Y-m-d\TH:i:s\Z') : null,
            'deshabilitado'   => $user->isSuspended()
        ];

        // AGERGAR DATOS DE USUARIO CUANDO SE TENGA EL EMPLEADO.
        // if( $user->getIdEmpleado() )  {
        //     $empleado = $user->getIdEmpleado();
        //     $persona  = $empleado->getIdPersona();

        //     $data['dui']                     = $persona->getDui();
        //     $data['carne_residente']         = $persona->getCarneResidente();
        //     $data['nombres']                 = $persona->getNombres();
        //     $data['apellidos']               = $persona->getApellidos();
        //     $data['id_tipo_empleado']        = $empleado->getIdTipoEmpleado() ? $empleado->getIdTipoEmpleado()->getId() : null;
        //     $data['tipo_empleado']           = $empleado->getIdTipoEmpleado() ? $empleado->getIdTipoEmpleado()->getNombre() : null;
        //     $data['numero_junta_vigilancia'] = $empleado->getNumeroJuntaVigilancia();
        //     $data['telefono']                = $empleado->getTelefono();
        //     $data['establecimientos']        = $empleado->getEstablecimientos() ? array_map(
        //         function( $establecimiento ) {
        //             return [
        //                 "id"                => $establecimiento->getId(),
        //                 "nombre"            => $establecimiento->getNombre(),
        //                 "centro_vacunacion" => $establecimiento->getCentroVacunacion() != null ? $establecimiento->getCentroVacunacion() : false,
        //                 "ignorar_fase"      => $establecimiento->getIgnorarFase() != null ? $establecimiento->getIgnorarFase() : false,
        //             ];
        //         },
        //         $empleado->getEstablecimientos()->toArray()
        //     ) : [];
        // }

        $response = [
            "message" => "La petición ha sido procesada exitosamente",
            "data"    => [ $data ]
        ];

        return new Response(json_encode( $response ), JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Post("/change/password", name="user_change_password")
     *
     * @OA\RequestBody(
     *     description="Datos para la actualización de la nueva contraseña",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *             type="object",
     *             required={"old_password", "new_password", "repeat_password"},
     *             @OA\Property(
     *                 property="old_password",
     *                 description="Contraseña anterior",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="new_password",
     *                 description="Nueva contraseña",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="repeat_password",
     *                 description="Repetir nueva contraseña",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="La petición ha sido procesada exitosamente",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Mensaje de exito"
     *         ),
     *         @OA\Property(
     *             property="data",
     *             type="array",
     *             description="datos de token de nueva contraseña.",
     *             @OA\Items(
     *                 @OA\Property(
     *                     property="token",
     *                     type="string",
     *                     description="Bearer token"
     *                 ),
     *                 @OA\Property(
     *                     property="refresh_token",
     *                     type="string",
     *                     description="Refresh token"
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Se han encontrado errores en la petición.",
     *     @OA\JsonContent(
     *         type= "object",
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Mensaje de error"
     *         ),
     *         @OA\Property(
     *             property="errors",
     *             type="array",
     *             description="Detalle de errores.",
     *             @OA\Items(
     *                 @OA\Property(
     *                     property="propiedad",
     *                     type="string",
     *                     description="Propiedad asociada al error. Si es null aplica sobre el objeto completo."
     *                 ),
     *                 @OA\Property(
     *                     property="error",
     *                     type="string",
     *                     description="Descripción del error."
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=500,
     *     description="Error interno del servidor."
     * )
     *
     * @OA\Tag(name="Usuario")
     */
    public function changePassword(Request $request, JsonSchema $jsonService, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager, UserPasswordEncoderInterface $encoder, RefreshTokenManagerInterface $refreshTokenManager) {
        // obteniendo el contenido del cuerpo en formato string
        $json = $request->getContent();

        // Convirtiendo el json a objeto
        try {
            $jsonObject = json_decode($json);
            $json       = json_decode($json, true);
        } catch(\Exception $ex) {
            // Error en la petición
            $response = [
                "message" => "El json proporcionado posee una estructura no válida",
                "data"    => []
            ];

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }

        try {
            // Obteniendo el JSON SCHEMA
            // Url del Json Schema original
            $schema = json_decode( file_get_contents( __DIR__.'/../JsonSchema/schemas/usuario_change_password.json' ) );
        } catch (\Exception $ex) {
            // throw $ex;
            // Error en la petición
            $response = [
                "message" => "Se ha producido un error interno.",
                "data"    => []
            ];

            throw new HttpException( JsonResponse::HTTP_INTERNAL_SERVER_ERROR, json_encode($response) );
        }

        $errorArray = [];
        $validator  = new JsonValidator();

        // Validando el json con el schema
        $validator->validate( $jsonObject, $schema );

        if( $validator->isValid() === false ) {
            foreach ($validator->getErrors() as $error) {
                if( in_array( $error['property'], ['new_password', 'repeat_password'] ) ) {
                    $errorMsg = "El password debe de contener mínimo 6 y máximo 16 caracteres, al menos una mayúscula, una minúscula, al menos un número y al menos un carácter especial: $@$!%*?&-_";
                } else {
                    $errorMsg = $jsonService->getTranslateErrors( null, $error );
                }

                $errorArray[] = array( 'propiedad' => $error['property'], 'error' => $errorMsg );
            }

            $response = array(
                'message' => 'Se han encontrado errores en la peticion.',
                'errors'  => $errorArray
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }

        // Obteniendo el usuasrio
        $em      = $this->getDoctrine()->getManager();
        $payload = $jwtManager->decode( $tokenStorageInterface->getToken() );
        $email   = $payload['username'];
        $user    = $em->getRepository('App:User')->findOneBy(['email' => $email]);

        if (!$user) {
            $response = array(
                'message' => 'Se han encontrado errores en la peticion',
                'errors'  => [
                    [
                        "propiedad" => "email",
                        "error"     => sprintf('Usuario identificado por el correo "%s" no existe.', $email)
                    ]
                ]
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }

        $oldPassword    = $json['old_password'];
        $newPassword    = $json['new_password'];
        $repeatPassword = $json['repeat_password'];
        if( !$encoder->isPasswordValid($user, $oldPassword) ) {
            $response = array(
                'message' => 'Se han encontrado errores en la peticion',
                'errors'  => [
                    [
                        "propiedad" => "old_password",
                        "error"     => 'Contraseña actual no es válida.'
                    ]
                ]
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }

        if( $newPassword != $repeatPassword ) {
            $response = array(
                'message' => 'Se han encontrado errores en la peticion',
                'errors'  => [
                    [
                        "propiedad" => "new_password",
                        "error"     => 'Las nuevas contraseñas no coinciden.'
                    ]
                ]
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }

        $now = new \DateTime();
        // actualizando la contraseña
        $user->setPlainPassword($newPassword);
        // reestableciendo el tiempo a partir del cual
        // los tokens son válidos
        $user->setTokenValidAfter($now);
        $em->persist($user);
        $em->flush();

        // generando nuevo token
        $token = $jwtManager->create( $user );

        // generando tiempo TTL de refresh token
        $valid = clone $now;
        $valid->modify('+'.$_ENV['JWT_REFRESHTTL'].' seconds');

        // invalidando los refresh token
        $invalidTokens = $em->createQueryBuilder()
            ->select('u')
            ->from(RefreshToken::class, 'u')
            ->where('u.valid < :datetime')
            ->andWhere('u.username = :username')
            ->setParameters([
                ':datetime' => $valid,
                ':username' => $user->getEmail()
            ])
            ->getQuery()
            ->getResult()
        ;

        if( count( $invalidTokens ) > 0 ) {
            foreach ($invalidTokens as $invalidToken) {
                $em->remove($invalidToken);
            }

            $em->flush();
        }

        // generando nuevo refresh token
        $refreshToken = $refreshTokenManager->create();
        $refreshToken->setUsername($user->getEmail());
        $refreshToken->setRefreshToken();
        $refreshToken->setValid($valid);

        $refreshTokenManager->save($refreshToken);

        $response = [
            "message" => "Contraseña acutalizada exitosamente",
            "data"    => [
                "token" => $token,
                "refresh_token" => $refreshToken->getRefreshToken()
            ]
        ];

        return new Response(json_encode( $response ), JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Post("/recovery/password", name="user_recovery_password")
     *
     * @OA\RequestBody(
     *     description="Datos de recuperación de contraseña.",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *             type="object",
     *             required={"email"},
     *             @OA\Property(
     *                 property="email",
     *                 description="Correo electrónico de recuperación.",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="La petición ha sido procesada exitosamente.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Mensaje de exito"
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Se han encontrado errores en la petición.",
     *     @OA\JsonContent(
     *         type= "object",
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Mensaje de error"
     *         ),
     *         @OA\Property(
     *             property="errors",
     *             type="array",
     *             description="Detalle de errores.",
     *             @OA\Items(
     *                 @OA\Property(
     *                     property="propiedad",
     *                     type="string",
     *                     description="Propiedad asociada al error. Si es null aplica sobre el objeto completo."
     *                 ),
     *                 @OA\Property(
     *                     property="error",
     *                     type="string",
     *                     description="Descripción del error."
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=500,
     *     description="Error interno del servidor."
     * )
     *
     * @OA\Tag(name="Usuario")
     */
    public function recoveryPassword(Request $request, Mail $mailer, JWTTokenManagerInterface $jwtManager) {
        $response = ['message' => "La petición ha sido procesada exitosamente", 'data' => [] ];
        // obteniendo email
        $email = $request->request->get('email');

        // verificando que el email sea valido
        if( preg_match('/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/', $email ) ) {
            $em   = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy([ 'email' => $email ]);

            // Si el correo electrónico existe
            if($user) {
                // generacion de token de reseteo
                $now = new \DateTime();

                // generando nuevo token
                $token = $jwtManager->create( $user );

                $parameters = [
                    'from'       => $_ENV['MAILER_EXCEPTION_FROM'],
                    'to'         => $user->getEmail(),
                    'subject'    => "Recuperación de contraseña.!",
                    'template'   => 'email/user/password_recovery.html.twig',
                    'parameters' => [
                        'user'        => $user,
                        'frontDomain' => $_ENV['FRONT_DOMAIN'],
                        'token'       => $token
                    ]
                ];

                $mailer->send($parameters);
            }
        }

        return new Response(json_encode( $response ), JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Post("/recovery/checktoken", name="user_recovery_checktoken")
     *
     * @OA\Response(
     *     response=200,
     *     description="La petición ha sido procesada exitosamente.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Mensaje de exito"
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=401,
     *     description="No se posee autorización de acceso al recurso!.",
     *     @OA\JsonContent(
     *         type= "object",
     *         @OA\Property(
     *             property="code",
     *             type="string",
     *             description="Código 401"
     *         ),
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Token JWT no válido"
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=500,
     *     description="Error interno del servidor."
     * )
     *
     * @OA\Tag(name="Usuario")
     */
    public function recoveryChecktoken() {
        $response = ['message' => "La petición ha sido procesada exitosamente.", 'data' => [] ];

        return new Response(json_encode( $response ), JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Post("/reset/password", name="user_reset_password")
     *
     * @OA\RequestBody(
     *     description="Datos para el restablecimiento de la contraseña",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *             type="object",
     *             required={"new_password", "repeat_password"},
     *             @OA\Property(
     *                 property="new_password",
     *                 description="Nueva contraseña",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="repeat_password",
     *                 description="Repetir nueva contraseña",
     *                 type="string"
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="La petición ha sido procesada exitosamente",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Mensaje de exito"
     *         ),
     *         @OA\Property(
     *             property="data",
     *             type="array",
     *             description="datos de token de nueva contraseña.",
     *             @OA\Items(
     *                 @OA\Property(
     *                     property="token",
     *                     type="string",
     *                     description="Bearer token"
     *                 ),
     *                 @OA\Property(
     *                     property="refresh_token",
     *                     type="string",
     *                     description="Refresh token"
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Se han encontrado errores en la petición.",
     *     @OA\JsonContent(
     *         type= "object",
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Mensaje de error"
     *         ),
     *         @OA\Property(
     *             property="errors",
     *             type="array",
     *             description="Detalle de errores.",
     *             @OA\Items(
     *                 @OA\Property(
     *                     property="propiedad",
     *                     type="string",
     *                     description="Propiedad asociada al error. Si es null aplica sobre el objeto completo."
     *                 ),
     *                 @OA\Property(
     *                     property="error",
     *                     type="string",
     *                     description="Descripción del error."
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=500,
     *     description="Error interno del servidor."
     * )
     *
     * @OA\Tag(name="Usuario")
     */
    public function resetPassword(Request $request, JsonSchema $jsonService, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager, UserPasswordEncoderInterface $encoder, RefreshTokenManagerInterface $refreshTokenManager) {
        // obteniendo el contenido del cuerpo en formato string
        $json = $request->getContent();

        // Convirtiendo el json a objeto
        try {
            $jsonObject = json_decode($json);
            $json       = json_decode($json, true);
        } catch(\Exception $ex) {
            // Error en la petición
            $response = [
                "message" => "El json proporcionado posee una estructura no válida",
                "data"    => []
            ];

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }

        try {
            // Obteniendo el JSON SCHEMA
            // Url del Json Schema original
            $schema = json_decode( file_get_contents( __DIR__.'/../JsonSchema/schemas/usuario_reset_password.json' ) );
        } catch (\Exception $ex) {
            // throw $ex;
            // Error en la petición
            $response = [
                "message" => "Se ha producido un error interno.",
                "data"    => []
            ];

            throw new HttpException( JsonResponse::HTTP_INTERNAL_SERVER_ERROR, json_encode($response) );
        }

        $errorArray = [];
        $validator  = new JsonValidator();

        // Validando el json con el schema
        $validator->validate( $jsonObject, $schema );

        if( $validator->isValid() === false ) {
            foreach ($validator->getErrors() as $error) {
                if( in_array( $error['property'], ['new_password', 'repeat_password'] ) ) {
                    $errorMsg = "El password debe de contener mínimo 6 y máximo 16 caracteres, al menos una mayúscula, una minúscula, al menos un número y al menos un carácter especial: $@$!%*?&-_";
                } else {
                    $errorMsg = $jsonService->getTranslateErrors( null, $error );
                }

                $errorArray[] = array( 'propiedad' => $error['property'], 'error' => $errorMsg );
            }

            $response = array(
                'message' => 'Se han encontrado errores en la peticion.',
                'errors'  => $errorArray
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }

        // Obteniendo el usuasrio
        $em      = $this->getDoctrine()->getManager();
        $payload = $jwtManager->decode( $tokenStorageInterface->getToken() );
        $email   = $payload['username'];
        $user    = $em->getRepository('App:User')->findOneBy(['email' => $email]);

        if (!$user) {
            $response = array(
                'message' => 'Se han encontrado errores en la peticion',
                'errors'  => [
                    [
                        "propiedad" => "email",
                        "error"     => sprintf('Usuario identificado por el correo "%s" no existe.', $email)
                    ]
                ]
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }

        $newPassword    = $json['new_password'];
        $repeatPassword = $json['repeat_password'];

        if( $newPassword != $repeatPassword ) {
            $response = array(
                'message' => 'Se han encontrado errores en la peticion',
                'errors'  => [
                    [
                        "propiedad" => "new_password",
                        "error"     => 'Las nuevas contraseñas no coinciden.'
                    ]
                ]
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }

        $now = new \DateTime();
        // actualizando la contraseña
        $user->setPlainPassword($newPassword);
        // reestableciendo el tiempo a partir del cual
        // los tokens son válidos
        $user->setTokenValidAfter($now);
        $em->persist($user);
        $em->flush();

        // generando nuevo token
        $token = $jwtManager->create( $user );

        // generando tiempo TTL de refresh token
        $valid = clone $now;
        $valid->modify('+'.$_ENV['JWT_REFRESHTTL'].' seconds');

        // invalidando los refresh token
        $invalidTokens = $em->createQueryBuilder()
            ->select('u')
            ->from(RefreshToken::class, 'u')
            ->where('u.valid < :datetime')
            ->andWhere('u.username = :username')
            ->setParameters([
                ':datetime' => $valid,
                ':username' => $user->getEmail()
            ])
            ->getQuery()
            ->getResult()
        ;

        if( count( $invalidTokens ) > 0 ) {
            foreach ($invalidTokens as $invalidToken) {
                $em->remove($invalidToken);
            }

            $em->flush();
        }

        // generando nuevo refresh token
        $refreshToken = $refreshTokenManager->create();
        $refreshToken->setUsername($user->getEmail());
        $refreshToken->setRefreshToken();
        $refreshToken->setValid($valid);

        $refreshTokenManager->save($refreshToken);

        $response = [
            "message" => "Contraseña acutalizada exitosamente",
            "data"    => [
                "token" => $token,
                "refresh_token" => $refreshToken->getRefreshToken()
            ]
        ];

        return new Response(json_encode( $response ), JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Post("/change/email", name="user_change_email")
     *
     *@OA\RequestBody(
     *     description="Json que contiene los datos de la actualizacion de email",
     *     required=true,
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *             type="object",
     *             required={"email", "new_email"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 nullable= false,
     *                 description="Email del usuario",
     *                 minLength= 1,
     *             ),
     *             @OA\Property(
     *                 property="new_email",
     *                 type="string",
     *                 nullable= false,
     *                 description="Nuevo Email del usuario",
     *                 minLength= 1,
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="La petición ha sido procesada exitosamente",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Mensaje de exito"
     *         ),
     *         @OA\Property(
     *             property="data",
     *             type="array",
     *             description="datos de nuevo token.",
     *             @OA\Items(
     *                 @OA\Property(
     *                     property="token",
     *                     type="string",
     *                     description="Bearer token"
     *                 ),
     *                 @OA\Property(
     *                     property="refresh_token",
     *                     type="string",
     *                     description="Refresh token"
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     *
     * @OA\Response(
     *     response=400,
     *     description="Se han encontrado errores en la petición.",
     *     @OA\JsonContent(
     *         type= "object",
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Mensaje de error"
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Recurso no encontrado."
     * )
     *
     * @OA\Tag(name="Usuario")
    */
    public function changeEmailAction(Request $request, JsonSchema $jsonSchemaService, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager, UserPasswordEncoderInterface $encoder, RefreshTokenManagerInterface $refreshTokenManager) {
        $content    = $request->getContent();

        try {
            $json = json_decode($content);
            $data = json_decode($content, true);

        } catch(\Exception $ex) {

            $response = array(
                "message" => "El json proporcionado posee una estructura no válida",
                "data"    => array()
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, $this->serializer->serialize($response, "json") );
        }

        if( empty($data) ){

            $response = array(
                "message" => "No se han encontrado datos en la peticion.",
                "data"    => array()
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, $this->serializer->serialize($response, "json") );
        }


        try {
            $jsonSchema = json_decode( file_get_contents( __DIR__.'/../JsonSchema/schemas/usuario_change_email.json' ) );
        } catch (\Exception $ex) {
            throw $ex;
            return new JsonResponse( '[ "Se ha producido un error interno" ]', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $validator  = new JsonValidator();
        $errorArray = array();

        $validator->validate( $json, $jsonSchema );

        if( $validator->isValid() === false ) {
            foreach ($validator->getErrors() as $error) {
                $errorArray[] = array( 'propiedad' => $error['property'], 'error' => $jsonSchemaService->getTranslateErrors( null, $error ) );
            }

            $response = array(
                'message' => 'Se han encontrado errores en la peticion.',
                'errors'  => $errorArray
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, $this->serializer->serialize($response, "json") );
        }

        $em   = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App:User')->findOneBy(['email' => $data["email"]]);
        if ( ! $user ) {
            $response = array(
                'message' => 'Se han encontrado errores en la peticion',
                'errors'  => [
                    [
                        "propiedad" => "email",
                        "error"     => sprintf('Usuario identificado por el correo "%s" no existe.', $data["email"])
                    ]
                ]
            );

            throw new HttpException( JsonResponse::HTTP_NOT_FOUND, json_encode($response) );
        }

        $payload  = $jwtManager->decode( $tokenStorageInterface->getToken() );
        $email    = $payload['username'];
        $oldEmail = $data['email'];
        $newEmail = $data['new_email'];

        if( $email != $oldEmail ) {
            $response = array(
                'message' => 'Se han encontrado errores en la peticion',
                'errors'  => [
                    [
                        "propiedad" => "email",
                        "error"     => 'El email de usuario no coincide.'
                    ]
                ]
            );

            throw new HttpException( JsonResponse::HTTP_BAD_REQUEST, json_encode($response) );
        }


        $now = new \DateTime();
        $user->setEmail($newEmail);
        // reestableciendo el tiempo a partir del cual
        // los tokens son válidos
        $user->setTokenValidAfter($now);
        $em->persist($user);
        $em->flush();

        // generando nuevo token
        $token = $jwtManager->create( $user );

        // generando tiempo TTL de refresh token
        $valid = clone $now;
        $valid->modify('+'.$_ENV['JWT_REFRESHTTL'].' seconds');

        // invalidando los refresh token
        $invalidTokens = $em->createQueryBuilder()
            ->select('u')
            ->from(RefreshToken::class, 'u')
            ->where('u.valid < :datetime')
            ->andWhere('u.username = :username')
            ->setParameters([
                ':datetime' => $valid,
                ':username' => $user->getEmail()
            ])
            ->getQuery()
            ->getResult()
        ;

        if( count( $invalidTokens ) > 0 ) {
            foreach ($invalidTokens as $invalidToken) {
                $em->remove($invalidToken);
            }

            $em->flush();
        }

        // generando nuevo refresh token
        $refreshToken = $refreshTokenManager->create();
        $refreshToken->setUsername($user->getEmail());
        $refreshToken->setRefreshToken();
        $refreshToken->setValid($valid);

        $refreshTokenManager->save($refreshToken);

        $response = [
            "message" => "Email acutalizado exitosamente",
            "data"    => [
                "token" => $token,
                "refresh_token" => $refreshToken->getRefreshToken()
            ]
        ];

        return new Response(json_encode( $response ), JsonResponse::HTTP_OK);
    }
}