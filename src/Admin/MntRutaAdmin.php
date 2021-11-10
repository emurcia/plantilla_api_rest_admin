<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Role;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Sonata\Form\Validator\ErrorElement;

final class MntRutaAdmin extends AbstractAdmin
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
            ->add('nombre')
            ->add('uri')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('nombre')
            ->add('uri')
            ->add('icono')
            ->add('mostrar', null, ['editable' => true])
            ->add('publico', null, ['editable' => true])
            ->add('orden')
            ->add('uroles', TemplateRegistry::TYPE_ARRAY, [ 'inline' => false, 'display' => 'values' ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $btClass = [ 'class' => 'col-md-6 col-sm-6' ];
        $formMapper
            ->with('Ruta Item', $btClass)
                ->add('nombre')
                ->add('uri')
                ->add('icono')
                ->add('mostrar')
                ->add('publico')
                ->add('orden')
            ->end()
            ->with('Security', $btClass)
                ->add('uroles', EntityType::class, [
                    'class'        => Role::class,
                    'choice_label' => 'name',
                    'multiple'     => true,
                    'required'     => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('t')
                            ->where('t.name LIKE :role')
                            ->setParameter('role', 'ROLE_FRONT_%')
                            ->orderBy('t.name', 'ASC')
                        ;
                    },
                    'attr' => array(
                        'data-select2-placeholder' => 'Seleccionar roles...'
                    )
                ])
            ->end()
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('nombre')
            ->add('uri')
            ->add('icono')
            ->add('mostrar')
            ->add('publico')
            ->add('orden')
            ->add('uroles', TemplateRegistry::TYPE_ARRAY, [ 'inline' => false, 'display' => 'values' ])
        ;
    }

    public function validate(ErrorElement $errorElement, $object): void
    {
        $action = $object->getId() ? 'edit' : 'create';

        $em  = $this->container->get('doctrine')->getManager();
        $id  = $action === 'edit' ? $object->getId() : null;
        $dql = "SELECT t01
                FROM App\Entity\MntRuta t01
                WHERE t01.nombre = UNACCENT( :nombre )"
        ;

        if( $id ) {
            $dql .= ' AND t01.id != :id';
        }

        $newNombre = preg_replace( '/\s\s+/',' ', ( trim( $object->getNombre() ) ) );

        $query = $em->createQuery($dql)
                    ->setParameter(':nombre', $newNombre);

        if( $id ) {
            $query->setParameter('id', $id );
        }

        $result = $query->getResult();
        if ( $result ) {
            $errorElement
                ->with('nombre')
                    ->addViolation('La ruta ' . $object->getNombre() . ' ya existe')
                ->end()
            ;
        }
    }

    public function prePersist(object $object): void
    {
        $newNombre = preg_replace( '/\s\s+/',' ', ( trim( $object->getNombre() ) ) );
        $object->setNombre($newNombre);
    }
}
