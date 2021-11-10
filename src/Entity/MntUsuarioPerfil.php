<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserPerfil
 *
 * @ORM\Table(name="mnt_usuario_perfil", indexes={@ORM\Index(name="IDX_2D3053D6B052C3AA", columns={"id_perfil"}), @ORM\Index(name="IDX_2D3053D6FCF8192D", columns={"id_usuario"})})
 * @ORM\Entity
 */
class UserPerfil
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mnt_usuario_perfil_id_seq", allocationSize=1, initialValue=1)
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
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     * })
     */
    private $idUsuario;

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

    public function getIdUsuario(): ?User
    {
        return $this->idUsuario;
    }

    public function setIdUsuario(?User $idUsuario): self
    {
        $this->idUsuario = $idUsuario;

        return $this;
    }


}
