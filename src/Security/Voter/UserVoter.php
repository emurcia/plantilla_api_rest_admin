<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UserVoter extends Voter
{
    const VIEW = 'ROLE_ADMIN_USER_VIEW';
    const EDIT = 'ROLE_ADMIN_USER_EDIT';
    private $security;
    private $em;

    public function __construct(Security $security, EntityManagerInterface $em) {
        $this->em       = $em;
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $subject */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subject, $user);
            case self::EDIT:
                return $this->canEdit($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(User $object, UserInterface $user)
    {
        // se verifican la mismas restricciones para edit y show
        return $this->canEdit( $object, $user );
    }

    private function canEdit(User $object, UserInterface $user)
    {
        $vote = false;
        if ( $this->security->isGranted('ROLE_SUPER_ADMIN') ) {
            $vote = true;
        } else {
            // si el registro es de su propio ID
            if( $object->getId() === $user->getId() ) {
                $vote = true;
            } else {
                $em   = $this->em;
                $data = $em->getRepository(User::class)->getVoterData( $object );
                $vote = $data ? true : false;
            }

        }
        return $vote;
    }
}
