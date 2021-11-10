<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Validator\ErrorElement;

final class RoleAdmin extends AbstractAdmin
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
            ->add('name')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('name')
            ->add('_action', null, [
                'actions' => [
                    'show' => ['label' => false],
                    'edit' => ['label' => false],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name')
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('name')
            ;
    }

    public function validate(ErrorElement $errorElement, $object): void
    {
        $action = $object->getId() ? 'edit' : 'create';

        $em  = $this->container->get('doctrine')->getManager();
        $id  = $action === 'edit' ? $object->getId() : null;
        $dql = "SELECT t01
                FROM App\Entity\Role t01
                WHERE t01.name = :name"
        ;

        if( $id ) {
            $dql .= ' AND t01.id != :id';
        }

        $query = $em->createQuery($dql)
                    ->setParameter('name', $object->getName());

        if( $id ) {
            $query->setParameter('id', $id );
        }

        $result = $query->getResult();
        if ( $result ) {
            $errorElement
                ->with('name')
                    ->addViolation('The role name alredy exists!')
                ->end()
            ;
        }
    }
}
