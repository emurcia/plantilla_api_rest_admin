<?php
// src/Service/Mail.php
namespace App\Service;

use App\Service\Constraints;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class Mail {
    private $mailer;
    private $constraint;

    public function __construct(MailerInterface $mailer, Constraints $constraint) {
        $this->mailer     = $mailer;
        $this->constraint = $constraint;
    }

    public function send( $parameters = [] ) {
        $constraint = $this->constraint;

        // Obteniendo variables iniciales
        $from               = array_key_exists('from', $parameters) ? $parameters['from'] : null;
        $to                 = array_key_exists('to', $parameters) ? $parameters['to'] : null;
        $cc                 = array_key_exists('cc', $parameters) ? $parameters['cc'] : null;
        $bcc                = array_key_exists('bcc', $parameters) ? $parameters['bcc'] : null;
        $replyTo            = array_key_exists('replyTo', $parameters) ? $parameters['replyTo'] : null;
        $subject            = array_key_exists('subject', $parameters) ? $parameters['subject'] : null;
        $template           = array_key_exists('template', $parameters) ? $parameters['template'] : null;
        $mailParameters     = array_key_exists('parameters', $parameters) ? $parameters['parameters'] : array();

        if( $constraint->isBlank( $from ) || $constraint->isBlank( $to ) || $constraint->isBlank( $subject ) || $constraint->isBlank( $template ) ) {
            throw new \Exception("Error, no se puede enviar el correo debido a que alguno de los parametros from, to, subject, template no ha sido proporcionado.", 999);
        }

        $email = new TemplatedEmail();

        $email->from($from)
            ->to(...explode( ',', $to) )
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($mailParameters)
        ;

        if( $constraint->isBlank( $cc ) === false ) {
            $email->cc( explode( ',', $cc ) );
        }

        if( $constraint->isBlank( $bcc ) === false ) {
            $email->bcc( explode( ',', $bcc ) );
        }

        if( $constraint->isBlank( $replyTo ) === false ) {
            $email->replyTo( explode( ',', $replyTo ) );
        }

        try {
            $result = $this->mailer->send($email);
        } catch(\Exception $ex) {
            throw new \Exception($ex->getMessage(), 999, $ex);
        }

        return $result;
    }
}