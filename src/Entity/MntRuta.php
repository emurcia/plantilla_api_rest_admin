<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * MntRuta
 *
 * @ORM\Table(name="mnt_ruta")
 * @ORM\Entity
 */
class MntRuta
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mnt_ruta_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=50, nullable=false)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="uri", type="text", nullable=true)
     */
    private $uri;

    /**
     * @var bool
     *
     * @ORM\Column(name="mostrar", type="boolean", nullable=false)
     */
    private $mostrar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="icono", type="string", nullable=true)
     */
    private $icono;

    /**
     * @var int|null
     *
     * @ORM\Column(name="orden", type="integer", nullable=true)
     */
    private $orden;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="publico", type="boolean", nullable=true)
     */
    private $publico;

    /**
     * @var Collection|Role[]
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(
     *      name="mnt_ruta_rol",
     *      joinColumns={@ORM\JoinColumn(name="id_ruta", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_rol", referencedColumnName="id")}
     * )
     */
    private $uroles;

    /**
     * @var \MntRuta
     *
     * @ORM\ManyToOne(targetEntity="MntRuta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_ruta_padre", referencedColumnName="id")
     * })
     */
    private $idRutaPadre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre_uri", type="string", nullable=true)
     */
    private $nombreUri;

    /**
     * @var Collection|MntRuta[]
     * @ORM\OneToMany(targetEntity="MntRuta", mappedBy="idRutaPadre")
     */
    private $children;

    public function __construct()
    {
        $this->uroles = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * @return Collection|Role[]
     */
    public function getUroles(): Collection
    {
        return $this->uroles;
    }

    public function addUrole(Role $urole): self
    {
        if (!$this->uroles->contains($urole)) {
            $this->uroles[] = $urole;
        }

        return $this;
    }

    public function removeUrole(Role $urole): self
    {
        $this->uroles->removeElement($urole);

        return $this;
    }

    /**
     * @return Collection|MntRuta[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChildren(MntRuta $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
        }

        return $this;
    }

    public function removeChildren(MntRuta $child): self
    {
        $this->children->removeElement($child);

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(?string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getMostrar(): ?bool
    {
        return $this->mostrar;
    }

    public function setMostrar(bool $mostrar): self
    {
        $this->mostrar = $mostrar;

        return $this;
    }

    public function getIcono(): ?string
    {
        return $this->icono;
    }

    public function setIcono(?string $icono): self
    {
        $this->icono = $icono;

        return $this;
    }

    public function getOrden(): ?int
    {
        return $this->orden;
    }

    public function setOrden(?int $orden): self
    {
        $this->orden = $orden;

        return $this;
    }

    public function getPublico(): ?bool
    {
        return $this->publico;
    }

    public function setPublico(?bool $publico): self
    {
        $this->publico = $publico;

        return $this;
    }

    public function getIdRutaPadre(): ?MntRuta
    {
        return $this->idRutaPadre;
    }

    public function setIdRutaPadre(?MntRuta $idRutaPadre): self
    {
        $this->idRutaPadre = $idRutaPadre;

        return $this;
    }

    public function getNombreUri(): ?string
    {
       return $this->nombreUri;
    }

   public function setNombreUri(?string $nombreUri): self
   {
       $this->nombreUri = $nombreUri;
       return $this;
   }

    public function __toString()
    {
        return $this->nombre ?: '';
    }

    public function __toJson()
    {
        return json_encode(
            [
                "name"  => $this->nombre,
                "uri"   => $this->uri,
                "nombre_uri"   => $this->nombreUri,
                "icon"  => $this->icono,
                "orden" => $this->orden,
                "mostrar"=> $this->mostrar
                //"children" => $this->children->map( fn($c) => json_decode($c->__toJson(), true))->toArray()
            ]
        );
    }
}
