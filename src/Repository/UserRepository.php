<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findByRole($role, $include = true, $queryBuilder = false)
    {
        // obteniendo el listado de usuarios que cumplen con el role que se busca
        $qb = $this->getIncludeRole( $role, $include );

        if( $queryBuilder ) {
            return $qb;
        } else {
            return $qb->getQuery()->getResult();
        }
    }

    private function getIncludeRole( $role, $include = true ) {
        // obteniendo el listado de usuarios que cumplen
        // que se les ha asignado el role ya sea directamente
        // o través de grupos
        $em  = $this->getEntityManager();
        $not = $include ? '' : 'NOT';
        $sql = "SELECT t01.id
            FROM mnt_user             t01
            INNER JOIN mnt_user_roles t02 ON (t01.id = t02.id_usuario)
            INNER JOIN role           t03 ON (t03.id = t02.id_rol)
            GROUP BY t01.id
            HAVING $not ( :role = ANY ( ARRAY_AGG( t03.name ) ) )
                AND NOT ( 'ROLE_SUPER_ADMIN' = ANY ( ARRAY_AGG( t03.name ) ) )"
        ;

        $stm = $em->getConnection()->prepare($sql);
        $stm->bindValue('role', $role);
        $stm->execute();
        $result = $stm->fetchAll();

        $ids = count( $result ) > 0 ? array_map(function( $row ) { return $row['id']; }, $result) : [ 0 ];

        $qb = $this->createQueryBuilder('u')
            ->where('u.id IN('.implode(',', $ids).')')
        ;

        return $qb;
    }

    public function getVoterData( $object ) {
        // encontrando los usuarios que cumplen con el role para descartarlos
        // el ROLE_ADMNISTRADOR, son los usuariso con privilegios para editar mas usuarios
        // esto para evitar que edite otros usuarios con los mismo privilegios.
        // puede usarse otro rol en vez de ROLE_ADMINISTRADOR.
        $qb = $this->getIncludeRole( 'ROLE_ADMINISTRADOR', false );
        $result = $qb->andWhere("u.id = :id")
            ->setParameter('id', $object->getId())
            ->getQuery()->getResult()
        ;

        return $result;
    }
}
