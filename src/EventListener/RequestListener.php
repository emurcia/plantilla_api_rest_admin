<?php
// src/EventListener/RequestListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;
use App\Service\Bitacora;

class RequestListener
{
    private $bitacora;
    private $security;

    public function __construct(Bitacora $bitacora, Security $security)
    {
        $this->bitacora = $bitacora;
        $this->security = $security;
    }

    public function onKernelRequest(RequestEvent $event) {
        $token = $this->security->getToken();
        $user  = $this->security->getUser();

        if( $event->isMasterRequest() === true
            && ( $user !== null && $token !== null && $token->isAuthenticated() === true )
            && preg_match("/^\/api/", $event->getRequest()->getRequestUri()) === 1
            && in_array($_ENV['ENABLE_API_LOGS'], ["true", true, 1], true)
        ) {
            $this->bitacora->saveLog();
        }

        return;
    }
}