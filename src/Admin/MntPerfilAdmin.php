<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class MntPerfilAdmin extends AbstractAdmin
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     */
    public function __construct($code, $class, $baseControllerName, $container = null)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $container;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('nombre');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('nombre')
            ->add('codigo')
            ->add('perfilRoles', TemplateRegistry::TYPE_ARRAY, ['inline' => false, 'display' => 'values'])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $btClass = ['class' => 'col-md-6 col-sm-6'];
        $formMapper
            ->tab('Admin')
                ->with('Profile', $btClass)
                ->add('nombre')
                ->add('codigo')
                ->end()
            ->end()
            ->tab('Admin')
            ->with('Security', $btClass)
            ->add('perfilRoles', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'attr' => array(
                    'required' => 'true',
                    'data-select2-placeholder' => 'Seleccionar rol...'
                )
            ])
            ->end()
            ->end();
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('nombre')
            ->add('codigo')
            ->add('perfilRoles', TemplateRegistry::TYPE_ARRAY, ['inline' => false, 'display' => 'values']);
    }

    public function prePersist(object $object): void
    {
        $nombre = preg_replace('/\s\s+/', ' ', trim($object->getNombre()));
        $object->setNombre($nombre);

        $codigo = preg_replace('/\s\s+/', ' ', trim($object->getCodigo()));
        $object->setCodigo($codigo);
    }

    public function preRemove(object $object): void
    {
        $em = $this->container->get('doctrine')->getManager();
        $idPerfil = $object->getId();
        // al momento de borrar el objeto hay que ver que roles estan asociados a el y eliminarselos al usuario sino los tienes desde otro perfil
        foreach ($object->getPerfilUsuarios() as $user) {
            $idUser = $user->getId();
            foreach ($user->getUroles() as $roles) {
                $idRol = $roles->getId();
                $found = false;
                foreach ($object->getPerfilRoles() as $rolObject) {
                    if ($rolObject->getId() == $roles->getId()) {
                        $found = true;
                    }
                }
                // si el usuario tiene el rol y el rol tambien está en el objeto
                if ($found) {
                    // antes de remover el rol verificamos que no este en otro perfil asociado al usuario
                    $dql = "SELECT t01
                                FROM App\Entity\MntUsuarioPerfil t01
                                JOIN App\Entity\MntPerfilRol  t02 WITH t02.idPerfil  = t01.idPerfil
                                WHERE t01.idUsuario = :idUser AND t01.idPerfil <> :idPerfil AND t02.idRol = :idRol";
                    $query = $em->createQuery($dql)->setParameter(':idUser', $idUser)->setParameter(':idPerfil', $idPerfil)
                        ->setParameter(':idRol', $idRol);
                    if (!$query->getResult()) {
                        $user->removeUrole($roles);
                    }
                }
            }
        }

    }

    public function preUpdate(object $object): void
    {
        $em     = $this->container->get('doctrine')->getManager();
        $nombre = preg_replace('/\s\s+/', ' ', trim($object->getNombre()));
        $object->setNombre($nombre);

        foreach ($object->getPerfilUsuarios()->toArray() as $user) {
            // recorrer los roles que vienen en el objetos
            foreach ($object->getPerfilRoles() as $rolObject) {
                // verificar los roles que vienen en el objeto y que no los tiene el usuario
                if (!$user->hasrole($rolObject->getname())) {
                    $user->addUrole($rolObject);
                }
            }
        }
        $idPerfil = $object->getId();
        foreach ($object->getPerfilUsuarios()->toArray() as $user) {
            $idUser = $user->getId();
            foreach ($user->getUroles() as $roles) {
                $idRol = $roles->getId();
                $found = false;
                foreach ($object->getPerfilRoles() as $rolObject) {
                    if ($rolObject->getId() == $roles->getId()) {
                        $found = true;
                    }
                }
                if (!$found) {
                    // antes de remover el rol verificamos que no este en otro perfil asociado al usuario
                    $dql = "SELECT t01
                                FROM App\Entity\MntUsuarioPerfil t01
                                JOIN App\Entity\MntPerfilRol  t02 WITH t02.idPerfil  = t01.idPerfil
                                WHERE t01.idUsuario = :idUser AND t01.idPerfil <> :idPerfil AND t02.idRol = :idRol";
                    $query = $em->createQuery($dql)->setParameter(':idUser', $idUser)->setParameter(':idPerfil', $idPerfil)
                        ->setParameter(':idRol', $idRol);
                    if (!$query->getResult()) {
                        $user->removeUrole($roles);
                    }
                }
            }
        }
    }

    public function validate(ErrorElement $errorElement, $object): void
    {
        $action = $object->getId() ? 'edit' : 'create';
        $nombre = strtolower($object->getNombre());
        $codigo = strtolower($object->getCodigo());
        $em     = $this->container->get('doctrine')->getManager();
        $id     = $action === 'edit' ? $object->getId() : null;
        $dql    = "SELECT t01
                     FROM App\Entity\MntPerfil t01
                     WHERE lower(unaccent(t01.nombre)) = unaccent(:nombre)";
        if ($id) {
            $dql .= ' AND t01.id != :id';
        }
        $query = $em->createQuery($dql)
            ->setParameter('nombre', $nombre);

        if ($id) {
            $query->setParameter('id', $id);
        }
        $result = $query->getResult();
        if ($result) {
            $errorElement
                ->with('nombre')
                ->addViolation('The profile alredy exists!')
                ->end();
        }

        $dql2    = "SELECT t01
                     FROM App\Entity\MntPerfil t01
                     WHERE lower(unaccent(t01.codigo)) = unaccent(:codigo)";
        if ($id) {
            $dql2 .= ' AND t01.id != :id';
        }
        $query = $em->createQuery($dql2)
            ->setParameter('codigo', $codigo);

        if ($id) {
            $query->setParameter('id', $id);
        }
        $result = $query->getResult();
        if ($result) {
            $errorElement
                ->with('codigo')
                ->addViolation('This code is already in use!')
                ->end();
        }
    }

}
