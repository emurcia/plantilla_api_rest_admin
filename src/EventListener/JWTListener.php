<?php
// src/App/EventListener/JWTCreatedListener.php
namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;

class JWTListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack  = $requestStack;
        $this->entityManager = $entityManager;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $payload = $event->getData();

        // modificando expiration time for password recovery
        if($request->get('_route') == 'user_recovery_password') {
            unset( $payload['roles'] );
            $expiration        = new \DateTime('+'.$_ENV['JWT_MAILTTL'].' seconds');
            $payload['exp']    = $expiration->getTimestamp();
            $payload['origin'] = 'recovery-password';
        }

        $event->setData($payload);
    }

    /**
     * @param JWTDecodedEvent $event
     *
     * @return void
     */
    public function onJWTDecoded(JWTDecodedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $payload = $event->getPayload();
        $user    = $this->entityManager->getRepository('App:User')->findOneBy([ 'email' => $payload['username'] ]);

        if (
            $user &&
            $user->getTokenValidAfter() instanceof \DateTime &&
            $payload['iat'] < $user->getTokenValidAfter()->getTimestamp()
        ) {
            $event->markAsInvalid();
        }

        // validando que el token de recovery sea usado solamente para la ruta reset password
        if( array_key_exists('origin', $payload) && $payload['origin'] === 'recovery-password' && in_array( $request->get('_route'),  ['user_recovery_checktoken', 'user_reset_password' ] ) == false ) {
            $event->markAsInvalid();
        }
    }
}