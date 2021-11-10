<?php

namespace App\Controller;

use App\Entity\MntRuta;
use App\Entity\MntRutaRol;
use App\Entity\Role;
use App\Entity\User;
use App\Service\Constraints;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class MntRutaController
 *
 * @Route("/api/v1")
 */
class MntRutaController extends AbstractController
{
    private $doctrine;
    private $security;

    public function __construct(ManagerRegistry $doctrine, Security $security)
    {
        $this->doctrine = $doctrine;
        $this->security = $security;
    }

    /**
     * @Rest\Get("/rutas", name="get_rutas")
     *
     * @OA\Response(
     *     response=200,
     *     description="La petición ha sido procesada exitosamente",
     *     @OA\MediaType(
     *         mediaType="application/json",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string", 
     *                      description="Nombre menu"
     *                  ),
     *                  @OA\Property(
     *                      property="uri",
     *                      type="string", 
     *                      description="URL menu."
     *                  ),
     *                  @OA\Property(
     *                      property="icon",
     *                      type="string", 
     *                      description="Ícono del menú."
     *                  ),
     *                  @OA\Property(
     *                      property="children",
     *                      type="array", 
     *                      @OA\Items(
     *                          @OA\Property(
     *                              property="name",
     *                              type="string", 
     *                              description="Nombre menu"
     *                          ),
     *                          @OA\Property(
     *                              property="uri",
     *                              type="string", 
     *                              description="URL menu."
     *                          ),
     *                          @OA\Property(
     *                              property="icon",
     *                              type="string", 
     *                              description="Ícono del menú."
     *                          ),
     *                          @OA\Property(
     *                              property="children",
     *                              description="..."
     *                          )
     *                      )
     *                  ) 
     *              )
     *         )
     *     )
     * )
     * * @OA\Response(
     *     response=400,
     *     description="Se han encontrado errores en la petición."
     * )
     *
     * @OA\Response(
     *     response=403,
     *     description="Acceso no autorizado.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="code",
     *             type="integer",
     *             description="HTTP Status Code."
     *         ),
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Descripción del error."
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Recurso no encontrado",
     *     @OA\JsonContent(
     *         type= "object",
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             description="Descripción del error."
     *         )
     *     )
     * )
     *
     * @OA\Response(
     *     response=500,
     *     description="Se ha producido un error interno."
     * )
     *
     * @OA\Tag(name="Rutas")
     */
    public function getRutas()
    {
        /** @var User $user */
        $user    = $this->getUser();
        $em      = $this->doctrine->getManager();

        $rutas = $em->getRepository(MntRuta::class)->findBy(['publico' => true, 'idRutaPadre' => null]);
        $menus = $em->getRepository(MntRutaRol::class)->findBy([ 'idRuta' => $rutas]);

        if ($user) {
            $rutasPadre = $em->getRepository(MntRuta::class)->findBy(['idRutaPadre' => null]);
            $rolesId = $user->getUroles()->map( fn($r) => $r->getId() )->getValues();
            $menus = array_merge( $menus, $em->getRepository(MntRutaRol::class)->findBy( [ 'idRol' => $rolesId, 'idRuta' => $rutasPadre ] ) );
        }
        
        $datos = array_map(
            function ($item) use ($rolesId){
                /** @var MntRutaRol $item */
                $returnItem = json_decode($item->getIdRuta()->__toJson(), true);
                $returnItem['children'] = $this->getRutasChildren($item->getIdRuta()->getChildren()->filter(
                    function($r) use ($rolesId){
                        return $r->getPublico() || $this->getDoctrine()->getManager()->getRepository(MntRutaRol::class)->count(['idRuta' => $r->getId(), 'idRol' => $rolesId]);
                    }
                )->toArray(), $rolesId);

                return $returnItem;
        }, $menus);

        $orden = array_column($datos, 'orden');
        array_multisort($orden, SORT_ASC, $datos);
        
        return new Response(json_encode($datos,JSON_UNESCAPED_SLASHES), Response::HTTP_OK, ['Content-type' => 'application/json']);

    }

    /** 
     * @param MntRuta $ruta
     * @param int[] $rolesId
     * 
     * Obtiene un array con los datos de las rutas de forma jerárquica
    */
    private function getRutasChildren($rutas, $rolesId)
    {
        /** @var MntRuta[] $rutas */
        return array_values( array_filter( array_map(function($r) use ($rolesId) {
            $returnItem = json_decode($r->__toJson(), true);
            $returnItem['children'] = $this->getRutasChildren($r->getChildren()->filter(
                function($r) use ($rolesId){
                    return $r->getPublico() || $this->getDoctrine()->getManager()->getRepository(MntRutaRol::class)->count(['idRuta' => $r->getId(), 'idRol' => $rolesId]);
                }
            )->toArray(), $rolesId);

            return $returnItem;
        }, $rutas) ) );
    }
}