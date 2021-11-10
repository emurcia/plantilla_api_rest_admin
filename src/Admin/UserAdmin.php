<?php

namespace App\Admin;

use App\Entity\MntPerfil;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Doctrine\ORM\EntityRepository;
use App\Filter\CaseInsensitiveStringFilter;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\Role;
use App\Entity\User;

class UserAdmin extends AbstractAdmin
{
    // Ejemplo de alias para usar checkAccess in CURDController
    // $this->admin->checkAccess('alias', $object);
    // Descomentar si se crea nuevos roles fuera de los predefinidos pro sonata
    // protected $accessMapping = [
    //     'alias'   => 'ROLE_ADMIN_ACTION'
    // ];

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

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('email', CaseInsensitiveStringFilter::class);
        $datagridMapper->add('isSuspended');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('email')
            ->add('lastLogin', TemplateRegistry::TYPE_DATETIME, [ 'format' => 'd/m/Y h:i A' ])
            ->add('isSuspended', null, [ 'editable' => true ])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [ 'label' => false ],
                    'edit' => [ 'label' => false ]
                ]
            ])
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $security = $this->container->get('security.authorization_checker');
        $user     = $this->container->get('security.token_storage')->getToken()->getUser();
        $object   = $this->getSubject();
        $action   = $object && $this->getSubject()->getId() ? ( $user->getId() === $object->getId() ? 'profile' : 'edit' ) : 'create';

        $disabled = ( $action === 'edit' && $object->isSuspended() );

        $btClass = $action !== 'profile' ? [ 'class' => 'col-md-6 col-sm-6' ] : [];
        $formMapper
            ->tab('Admin')
                ->with('User', $btClass)
                    ->add('email')
                ->end()
            ->end()
        ;

        if( $action === 'profile' ) {
            $formMapper
                ->tab('Admin')
                    ->with('User')
                        ->add('plainPassword', RepeatedType::class, [
                            'required'        => false,
                            'type'            => PasswordType::class,
                            'first_options'   => [ 'label' => 'New password' ],
                            'second_options'  => [ 'label' => 'Repeat new password' ],
                            'invalid_message' => 'Passwords do not match, please retype',
                        ])
                    ->end()
                    ->with('Profiles', [ 'class' => 'hidden' ])
                        ->add('perfiles', EntityType::class, [
                            'class'        => MntPerfil::class,
                            'choice_label' => 'nombre',
                            'multiple'     => true,
                            'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('t')
                                    ->orderBy('t.nombre', 'ASC')
                                ;
                            }
                        ])
                    ->end()
                ->end()
            ;
        } else {
            $formMapper
                ->tab('Admin')
                    ->with('User')
                        ->add('plainPassword', TextType::class, [
                            'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                        ])
                    ->end()
                    ->with('Profiles', $btClass)
                        ->add('perfiles', EntityType::class, [
                            'class'        => MntPerfil::class,
                            'choice_label' => 'nombre',
                            'multiple'     => true,
                            'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('t')
                                    ->orderBy('t.nombre', 'ASC')
                                ;
                            }
                        ])
                    ->end()
                ->end()
            ;
        }

        if( $action == 'edit' ) {
            $formMapper
                ->tab('Admin')
                    ->with('User')
                        ->add('isSuspended', null, [ 'required' => false ])
                    ->end()
                    ->with('Profiles', $btClass)
                        ->add('perfiles', EntityType::class, [
                            'class'        => MntPerfil::class,
                            'choice_label' => 'nombre',
                            'multiple'     => true,
                            'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('t')
                                    ->orderBy('t.nombre', 'ASC')
                                ;
                            }
                        ])
                    ->end()
                ->end()
            ;
        }

        if( $security->isGranted('ROLE_SUPER_ADMIN') && $action !== 'profile' ) {
            $formMapper
                ->tab('Security')
                    ->with('Roles', $btClass)
                        ->add('uroles', EntityType::class, [
                            'class'        => Role::class,
                            'choice_label' => 'name',
                            'multiple'     => true,
                            'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('t')
                                    ->orderBy('t.name', 'ASC')
                                ;
                            },

                        ])
                    ->end()
                ->end()
            ;
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $security = $this->container->get('security.authorization_checker');
        $user     = $this->container->get('security.token_storage')->getToken()->getUser();
        $object   = $this->getSubject();
        $action   = $user->getId() === $object->getId() ? 'profile' : 'show';

        $showMapper
            ->add('email')
            ->add('lastLogin', TemplateRegistry::TYPE_DATETIME, [ 'format' => 'd/m/Y h:i A' ])
            ->add('createdAt', TemplateRegistry::TYPE_DATETIME, [ 'format' => 'd/m/Y h:i A' ])
        ;

        if( $security->isGranted('ROLE_SUPER_ADMIN') && $action !== 'profile' ) {
            $showMapper
                ->add('isSuspended')
                ->add('uroles', TemplateRegistry::TYPE_ARRAY, [ 'inline' => false, 'display' => 'values' ])
            ;
        }
    }

    public function validate(ErrorElement $errorElement, $object): void
    {
        $action = $object->getId() ? 'edit' : 'create';

        // Forma alternativa, la validacion se encuentra en src\Entity\User.
        // $em  = $this->container->get('doctrine')->getManager();
        // $id  = $action === 'edit' ? $object->getId() : null;
        // $dql = "SELECT t01
        //         FROM App\Entity\User t01
        //         WHERE t01.email = :email"
        // ;

        // if( $id ) {
        //     $dql .= ' AND t01.id != :id';
        // }

        // $query = $em->createQuery($dql)
        //             ->setParameter('email', $object->getEmail());

        // if( $id ) {
        //     $query->setParameter('id', $id );
        // }

        // $result = $query->getResult();
        // if ( $result ) {
        //     $errorElement
        //         ->with('email')
        //             ->addViolation('The email alredy exists!')
        //         ->end()
        //     ;
        // }

        // Verificando que el correo electrónico sea válido
        $email = $object->getEmail();
        if( !preg_match('/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/', $email ) ) {
            $errorElement
                ->with('email')
                    ->addViolation('The email has an invalid format!')
                ->end()
            ;
        }

        // Descomentar si se implementará la verificacion de contraseñas seguras.
        // Validación para verificar que la contraseña sea segura.
        // 8 - 16 caracteres
        // Al menos una mayúsucla
        // Al menos una minúscula
        // Un número
        // Un caracter especial: $@$!%*?&-_
        // $password = $object->getPlainPassword();
        // if( $password !== null && !preg_match( '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&\-\_])([A-Za-z\d$@$!%*?&\-\_]|[^ ]){8,16}$/', $password) ) {
        //     $errorElement
        //         ->with('plainPassword')
        //             ->addViolation('The password must have between 8 and 16 characters, at least an uppercase, at least a lowercase, at least a number and at least an special character: $@$!%*?&-_')
        //         ->end()
        //     ;
        // }
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $security = $this->container->get('security.authorization_checker');

        if( $security->isGranted('ROLE_SUPER_ADMIN') === false ) {
            $em = $this->container->get('doctrine')->getManager();

            // obteniendo los usuarios que no se deben de mostrar
            // el ROLE_ADMNISTRADOR, son los usuariso con privilegios a generar mas usuarios
            // esto para evitar que edite otros usuarios con los mismo privilegios
            // puede usarse otro rol en vez de ROLE_ADMINISTRADOR.
            $users = $em->getRepository(User::class)->findByRole( 'ROLE_ADMINISTRADOR', false, true );
            $query = new ProxyQuery( $users );
        }

        return $query;
    }

    public function prePersist(object $object): void
    {
        /** @var MntPerfil $p */
        foreach ($object->getPerfiles() as $p) {
            foreach ($p->getPerfilRoles() as $rol) {
                if(!$object->hasRole($rol->getName()))
                    $object->addUrole($rol);
            }
        }  
    }

    public function preUpdate(object $object): void
    {
        $em  = $this->container->get('doctrine')->getManager();
        $dql = "SELECT DISTINCT(t02.id) AS id
                FROM App\Entity\User t01
                JOIN t01.perfiles t02
                WHERE t01.id = :id"
        ;

        $query = $em->createQuery($dql)
                    ->setParameter('id', $object->getId());

        $perfiles = $query->getScalarResult();


        $dql = "SELECT DISTINCT(t03.id) AS id
                FROM App\Entity\User t01
                JOIN t01.perfiles t02
                JOIN t02.perfilRoles t03
                WHERE t01.id = :id"
        ;

        $query = $em->createQuery($dql)
                    ->setParameter('id', $object->getId());

        $perfilRoles = $query->getScalarResult();
        
        $perfilesIds = array_column($perfiles, "id");
        $rolesIds = array_column($perfilRoles,      "id");

        /** @var MntPerfil $p */
        foreach ($object->getPerfiles() as $p) {
            if(!in_array($p->getId(), $perfilesIds))
                foreach ($p->getPerfilRoles() as $rol) {
                    if(!$object->hasRole($rol->getName()))
                        $object->addUrole($rol);
                }
        }

        foreach ($object->getUroles() as $u) {
            $found = false;
            foreach ($object->getPerfiles() as $p) {
                foreach ($p->getPerfilRoles() as $rol) {
                    if($rol->getId() == $u->getId())
                        $found = true;
                }
            }

            if(!$found && in_array($u->getId(), $rolesIds))
                $object->removeUrole($u);
        }
    }
}