<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * MntPerfil
 *
 * @ORM\Table(name="mnt_perfil")
 * @ORM\Entity
 */
class MntPerfil
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mnt_perfil_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=30, nullable=false)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", length=5, nullable=true)
     */
    private $codigo;

    /**
     * @var Collection|Role[]
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(
     *      name="mnt_perfil_rol",
     *      joinColumns={@ORM\JoinColumn(name="id_perfil", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_rol", referencedColumnName="id")}
     * )
     */
    private $perfilRoles;

    /**
     * @var Collection|User[]
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(
     *      name="mnt_usuario_perfil",
     *      joinColumns={@ORM\JoinColumn(name="id_perfil", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_usuario", referencedColumnName="id")}
     * )
     */
    private $perfilUsuarios;

    public function __construct()
    {
        $this->perfilUsuarios = new ArrayCollection();
        $this->perfilRoles = new ArrayCollection();
    }

    /**
     * @return Collection|Role[]
     */
    public function getPerfilRoles(): Collection
    {
        return $this->perfilRoles;
    }

    public function addPerfilRole(Role $perfilRole): self
    {
        if (!$this->perfilRoles->contains($perfilRole)) {
            $this->perfilRoles[] = $perfilRole;
        }

        return $this;
    }

    public function removePerfilRole(Role $perfilRole): self
    {
        $this->perfilRoles->removeElement($perfilRole);

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getPerfilUsuarios(): Collection
    {
        return $this->perfilUsuarios;
    }

    public function addPerfilUsuario(User $perfilUsuario): self
    {
        if (!$this->perfilUsuarios->contains($perfilUsuario)) {
            $this->perfilUsuarios[] = $perfilUsuario;
        }

        return $this;
    }

    public function removePerfilUsuario(User $perfilUsuario): self
    {
        $this->perfilUsuarios->removeElement($perfilUsuario);

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

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function __toString()
    {
        return $this->nombre ? $this->nombre : '';
    }
}
