<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MntRutaRol
 *
 * @ORM\Table(name="mnt_ruta_rol", indexes={@ORM\Index(name="IDX_9B7D3C68488EEC8E", columns={"id_ruta"}), @ORM\Index(name="IDX_9B7D3C6890F1D76D", columns={"id_rol"})})
 * @ORM\Entity
 */
class MntRutaRol
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="mnt_ruta_rol_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \MntRuta
     *
     * @ORM\ManyToOne(targetEntity="MntRuta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_ruta", referencedColumnName="id")
     * })
     */
    private $idRuta;

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

    public function getIdRuta(): ?MntRuta
    {
        return $this->idRuta;
    }

    public function setIdRuta(?MntRuta $idRuta): self
    {
        $this->idRuta = $idRuta;

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
