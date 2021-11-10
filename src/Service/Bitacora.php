<?php
// src/Service/Bitacora.php
namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use App\Entity\BtBitacora;

class Bitacora {
    private $doctrine;
    private $session;
    private $request;
    private $security;

    public function __construct(ManagerRegistry $doctrine, SessionInterface $session, RequestStack $requestStack, Security $security) {
        $this->doctrine = $doctrine;
        $this->session  = $session;
        $this->request  = $requestStack->getCurrentRequest();
        $this->security = $security;
    }

    public function saveLog() {
        $request = $this->request;
        $headers = $request->headers;
        $session = $this->session;
        $server  = $request->server;
        $now     = new \DateTime();
        $user    = $this->security->getToken()->getUser();
        $em      = $this->doctrine->getManager();

        // removiendo si existe bitacora previa
        $session->remove('idBitacora');

        try {
            $arrHeaders = $headers->all();
            unset($arrHeaders['authorization']);
            unset($arrHeaders['host']);
            unset($arrHeaders['cookie']);

            $metodoHttp        = $request->getMethod();
            $requestUri        = $request->getRequestUri();
            $parameters        = $metodoHttp === 'GET' ? $request->query->all() : NULL;
            $requestHeaders    = $arrHeaders ? json_encode( $arrHeaders ) : NULL;
            $requestParameters = $parameters ? json_encode( $parameters ) : NULL;
            $requestContent    = $request->getContent() !== '' ? json_encode( json_decode( $request->getContent(), true) ) : NULL;
            $xForwardedFor     = $headers->get('x-forwarded-for') ? explode( ',', $headers->get('x-forwarded-for') ) : null;

            // Datos Proporcionados por XROAD
            $xrdUserid    = $headers->get('x-xrd-userid');
            $xrdMessageid = $headers->get('x-xrd-messageid');
            $xrdClient    = $headers->get('x-xrd-client');
            $xrdService   = $headers->get('x-xrd-service');
            $ipCliente    = $xForwardedFor ? array_pop( $xForwardedFor ) : $server->get('REMOTE_ADDR');
            $ipServidor   = $xForwardedFor ? $xForwardedFor[0] : $server->get('REMOTE_ADDR');

            $Bitacora = new BtBitacora();


            $Bitacora->setIdUsuario($user);
            $Bitacora->setIpCliente($ipCliente);
            $Bitacora->setIpServidor($ipServidor);
            $Bitacora->setMetodoHttp($metodoHttp);
            $Bitacora->setRequestHeaders($requestHeaders);
            $Bitacora->setRequestUri($requestUri);
            $Bitacora->setRequestParameters($requestParameters);
            $Bitacora->setRequestContent($requestContent);
            $Bitacora->setXrdUserid($xrdUserid);
            $Bitacora->setXrdMessageid($xrdMessageid);
            $Bitacora->setXrdClient($xrdClient);
            $Bitacora->setXrdService($xrdService);
            $Bitacora->setFechaHoraReg($now);

            $em->persist($Bitacora);
            $em->flush();

            $session->set('idBitacora', $Bitacora->getId());
        } catch (\Exception $e) {
            throw new \Exception('Error no se pudo generar el registro de la bitácora', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return;
    }
}