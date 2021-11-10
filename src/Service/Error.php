<?php
// src/Service/Error.php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Entity\BtError;
use App\Entity\BtBitacora;

class Error {
    private $em;
    private $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session) {
        $this->em      = $em;
        $this->session = $session;
    }

    public function save($exception) {
        $em  = $this->em;
        $now = new \DateTime();

        $Bitacora = ( $this->session->get('idBitacora') !== null ) ? $em->getRepository(BtBitacora::class)->findOneBy( array( 'id' => $this->session->get('idBitacora') ) ) : null;
        $code     = $exception instanceof HttpException ? $exception->getStatusCode() : $exception->getCode();
        $trace    = array(
            'file'          => $exception->getFile(),
            'line'          => $exception->getLine(),
            'traceAsString' => $exception->getTraceAsString()
        );

        $Error = new BtError();
        $Error->setCodigo($code);
        $Error->setMensaje($exception->getMessage());
        $Error->setTrace( json_encode( $trace ) );
        $Error->setFechaHoraReg($now);
        $Error->setIdBitacora($Bitacora);

        $em->persist($Error);
        $em->flush();

        return;
    }
}