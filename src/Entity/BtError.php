<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BtError
 *
 * @ORM\Table(name="bt_error", indexes={@ORM\Index(name="IDX_89EC7DD9C05C126C", columns={"id_bitacora"})})
 * @ORM\Entity
 */
class BtError
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"comment"="Llave primaria de la tabla"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="bt_error_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="codigo", type="integer", nullable=true, options={"comment"="Campo que almacena el código HTTP o el código de la excepción que se produjo en la API"})
     */
    private $codigo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mensaje", type="text", nullable=true, options={"comment"="Campo que almacena el mensaje de la excepción que se produjo en la API"})
     */
    private $mensaje;

    /**
     * @var string|null
     *
     * @ORM\Column(name="trace", type="text", nullable=true, options={"comment"="Campo que almacena el trazo de la ruta de la excepción que se produjo en la API"})
     */
    private $trace;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hora_reg", type="datetime", nullable=false, options={"default"="seconds","comment"="Campo que almacena el fecha y hora de la excepción que se produjo en la API"})
     */
    private $fechaHoraReg = 'seconds';

    /**
     * @var \BtBitacora
     *
     * @ORM\ManyToOne(targetEntity="BtBitacora")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_bitacora", referencedColumnName="id")
     * })
     */
    private $idBitacora;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?int
    {
        return $this->codigo;
    }

    public function setCodigo(?int $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(?string $mensaje): self
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    public function getTrace(): ?string
    {
        return $this->trace;
    }

    public function setTrace(?string $trace): self
    {
        $this->trace = $trace;

        return $this;
    }

    public function getFechaHoraReg(): ?\DateTimeInterface
    {
        return $this->fechaHoraReg;
    }

    public function setFechaHoraReg(\DateTimeInterface $fechaHoraReg): self
    {
        $this->fechaHoraReg = $fechaHoraReg;

        return $this;
    }

    public function getIdBitacora(): ?BtBitacora
    {
        return $this->idBitacora;
    }

    public function setIdBitacora(?BtBitacora $idBitacora): self
    {
        $this->idBitacora = $idBitacora;

        return $this;
    }


}
