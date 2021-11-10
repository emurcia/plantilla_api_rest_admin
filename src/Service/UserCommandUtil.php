<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use App\Entity\Role;

class UserCommandUtil
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create($email, $password, $role = null): void
    {
        $user = new User();

        $user->setEmail($email)
            ->setPlainPassword($password)
            ->addUrole( $this->getRole( $role ) );

        $this->saveUser($user);
    }

    private function saveUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function promote(string $email, string $role): void
    {
        $user = $this->findUserByEmail($email);

        $user->addUrole( $this->getRole( $role ) );

        $this->saveUser($user);
    }

    private function findUserByEmail(string $email): User
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            throw new InvalidArgumentException(sprintf('User identified by "%s" email does not exist.', $email));
        }

        return $user;
    }

    public function demote(string $email, string $role): void
    {
        $user = $this->findUserByEmail($email);

        $user->removeUrole( $this->getRole( $role ) );

        $this->saveUser($user);
    }

    public function changePassword($email, $password): void
    {
        $user = $this->findUserByEmail($email);

        $user->setPlainPassword($password);

        $this->saveUser($user);
    }

    private function getRole( $role ) {
        $Role = $this->entityManager->getRepository(Role::class)->findOneByName($role);

        if( !$Role ) {
            $Role = $this->entityManager->getRepository(Role::class)->findOneByName('ROLE_ADMIN');
        }

        return $Role;
    }
}
