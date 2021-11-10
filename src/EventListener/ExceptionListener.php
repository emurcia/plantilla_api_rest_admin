<?php
// src/EventListener/ExceptionListener.php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Service\Error AS ErrorService;
use App\Service\Constraints;
use App\Service\Mail;

class ExceptionListener
{
    private $errorService;
    private $session;
    private $constraint;
    private $mailer;
    private $container;

    public function __construct(ErrorService $errorService, SessionInterface $session, Constraints $constraint, Mail $mailer, ContainerInterface $container)
    {
        $this->errorService  = $errorService;
        $this->session       = $session;
        $this->constraint    = $constraint;
        $this->mailer        = $mailer;
        $this->container     = $container;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        // Verificando si se ha habilitado los logs de la API
        if( in_array($_ENV['ENABLE_API_LOGS'], ["true", true, 1], true) == false ) {
            return;
        }

        $env       = $this->container->getParameter('kernel.environment');
        $session   = $this->session;
        $exception = $event->getThrowable();
        $isHttpExc = $exception instanceof HttpException;

        // Modificado para poder visualizar HttpExceptions como Responses aun en entorno DEV
        // Si la Exception no es de tipo HttpException y se utiliza entorno DEV devuelve el Exception
        if( (!$env || $env === 'dev') && $isHttpExc === false ) {
            return;
        }

        // Se almacena el error solo si es entorno PROD
        if( !$env || $env === 'prod' )
            $this->errorService->save($exception);

        $response = new JsonResponse();

        $message = $session->has('_exceptionMsg') ? $session->get('_exceptionMsg') : ( $exception instanceof HttpException ? $exception->getMessage() : "Upss, hubo un error interno, por favor intente nuevamente, de persistir el error contacte al administrador" );
        $message = $this->constraint->isJson($message) ? $message : json_encode(  $message );
        $session->remove('_exceptionMsg');  // removiendo mensaje

        if ( $exception instanceof HttpException ) {
            $code    = $exception->getStatusCode();
            $headers = $exception->getHeaders();
            $headers['Content-Type'] = 'application/json';
        } else {
            $code    = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
            $headers = array('Content-Type' => 'application/json');
        }

        // Se envía email de notificación solo si es entorno PROD
        if( ( !$env || $env === 'prod' ) && $code !== 999 && ( $code < 200 || $code > 499) && in_array($_ENV['ENABLE_MAIL_REPORT'], ["true", true, 1], true) ) {
            $parameters = array(
                'from'       => $_ENV['MAILER_EXCEPTION_FROM'],
                'to'         => $_ENV['MAILER_EXCEPTION_TO'],
                'subject'    => "Error $code en la API SISTEMA",
                'template'   => 'email/exception/error.html.twig',
                'parameters' => array(
                    'code'       => $code,
                    'file'       => $exception->getFile(),
                    'message'    => $exception->getMessage(),
                    'line'       => $exception->getLine(),
                    'trace'      => $exception->getTraceAsString(),
                    'idBitacora' => $this->session->get('idBitacora')
                )
            );

            $this->mailer->send($parameters);
        }

        $response->setStatusCode($code);
        $response->headers->replace($headers);
        $response->setContent($message);

        $event->setResponse($response);

        return;
    }
}