<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MntPerfilRol
 *
 * @ORM\Table(name="mnt_perfil_rol", indexes={@ORM\Index(name="IDX_256E507CB052C3AA", columns={"id_perfil"}), @ORM\Index(name="IDX_256E507C90F1D76D", columns={"id_rol"})})
 * @ORM\Entity
 */
class MntPerfilRol
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mnt_perfil_rol_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \MntPerfil
     *
     * @ORM\ManyToOne(targetEntity="MntPerfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_perfil", referencedColumnName="id")
     * })
     */
    private $idPerfil;

    /**
     * @var \Role
     *
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_rol", referencedColumnName="id")
     * })
     */
    private $idRol;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPerfil(): ?MntPerfil
    {
        return $this->idPerfil;
    }

    public function setIdPerfil(?MntPerfil $idPerfil): self
    {
        $this->idPerfil = $idPerfil;

        return $this;
    }

    public function getIdRol(): ?Role
    {
        return $this->idRol;
    }

    public function setIdRol(?Role $idRol): self
    {
        $this->idRol = $idRol;

        return $this;
    }


}
