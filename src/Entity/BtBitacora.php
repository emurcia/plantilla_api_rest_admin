<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BtBitacora
 *
 * @ORM\Table(name="bt_bitacora", indexes={@ORM\Index(name="IDX_C4D314C0FCF8192D", columns={"id_usuario"})})
 * @ORM\Entity
 */
class BtBitacora
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"comment"="Llave primaria de la tabla"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="bt_bitacora_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_hora_reg", type="datetime", nullable=false, options={"default"="seconds","comment"="Campo que almacena la fecha y hora en que se realiza una acción en la API"})
     */
    private $fechaHoraReg = 'seconds';

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip_cliente", type="string", length=15, nullable=true, options={"comment"="Campo que almacena la IP del cliente Externo del Sistema Consultante del cuál el usuario externo esta realizando la acción"})
     */
    private $ipCliente;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip_servidor", type="string", length=15, nullable=true, options={"comment"="Campo que almacena la IP del servidor Externo del Sistema Consultante"})
     */
    private $ipServidor;

    /**
     * @var string
     *
     * @ORM\Column(name="metodo_http", type="string", length=10, nullable=false, options={"comment"="Campo que almacena el método HTTP del Servicio REST ejecutado"})
     */
    private $metodoHttp;

    /**
     * @var string|null
     *
     * @ORM\Column(name="request_headers", type="text", nullable=true, options={"comment"="Campo que almacena los parámetros proporcionados en el encabezado cuando se ejecutó la acción"})
     */
    private $requestHeaders;

    /**
     * @var string|null
     *
     * @ORM\Column(name="request_uri", type="text", nullable=true, options={"comment"="Campo que almacena la ruta que fue ejecutada para realizar la acción"})
     */
    private $requestUri;

    /**
     * @var string|null
     *
     * @ORM\Column(name="request_parameters", type="text", nullable=true, options={"comment"="Campo que almacena en formato json como string los parámetros ejecuados"})
     */
    private $requestParameters;

    /**
     * @var string|null
     *
     * @ORM\Column(name="request_content", type="text", nullable=true, options={"comment"="Campo que almacena contenido de la petición POST | PUT | DELETE"})
     */
    private $requestContent;

    /**
     * @var string|null
     *
     * @ORM\Column(name="xrd_userid", type="string", length=255, nullable=true, options={"comment"="Campo que almacena el id de usaurio según la pasarela TENOLI (XROAD)"})
     */
    private $xrdUserid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="xrd_messageid", type="string", length=255, nullable=true, options={"comment"="Campo que almacena el id del mensaje según la pasarela TENOLI (XROAD)"})
     */
    private $xrdMessageid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="xrd_client", type="string", length=255, nullable=true, options={"comment"="Campo que almacena cliente según la pasarela TENOLI (XROAD)"})
     */
    private $xrdClient;

    /**
     * @var string|null
     *
     * @ORM\Column(name="xrd_service", type="string", length=255, nullable=true, options={"comment"="Campo que almacena nombre del servicio según la pasarela TENOLI (XROAD)"})
     */
    private $xrdService;

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

    public function getFechaHoraReg(): ?\DateTimeInterface
    {
        return $this->fechaHoraReg;
    }

    public function setFechaHoraReg(\DateTimeInterface $fechaHoraReg): self
    {
        $this->fechaHoraReg = $fechaHoraReg;

        return $this;
    }

    public function getIpCliente(): ?string
    {
        return $this->ipCliente;
    }

    public function setIpCliente(?string $ipCliente): self
    {
        $this->ipCliente = $ipCliente;

        return $this;
    }

    public function getIpServidor(): ?string
    {
        return $this->ipServidor;
    }

    public function setIpServidor(?string $ipServidor): self
    {
        $this->ipServidor = $ipServidor;

        return $this;
    }

    public function getMetodoHttp(): ?string
    {
        return $this->metodoHttp;
    }

    public function setMetodoHttp(string $metodoHttp): self
    {
        $this->metodoHttp = $metodoHttp;

        return $this;
    }

    public function getRequestHeaders(): ?string
    {
        return $this->requestHeaders;
    }

    public function setRequestHeaders(?string $requestHeaders): self
    {
        $this->requestHeaders = $requestHeaders;

        return $this;
    }

    public function getRequestUri(): ?string
    {
        return $this->requestUri;
    }

    public function setRequestUri(?string $requestUri): self
    {
        $this->requestUri = $requestUri;

        return $this;
    }

    public function getRequestParameters(): ?string
    {
        return $this->requestParameters;
    }

    public function setRequestParameters(?string $requestParameters): self
    {
        $this->requestParameters = $requestParameters;

        return $this;
    }

    public function getRequestContent(): ?string
    {
        return $this->requestContent;
    }

    public function setRequestContent(?string $requestContent): self
    {
        $this->requestContent = $requestContent;

        return $this;
    }

    public function getXrdUserid(): ?string
    {
        return $this->xrdUserid;
    }

    public function setXrdUserid(?string $xrdUserid): self
    {
        $this->xrdUserid = $xrdUserid;

        return $this;
    }

    public function getXrdMessageid(): ?string
    {
        return $this->xrdMessageid;
    }

    public function setXrdMessageid(?string $xrdMessageid): self
    {
        $this->xrdMessageid = $xrdMessageid;

        return $this;
    }

    public function getXrdClient(): ?string
    {
        return $this->xrdClient;
    }

    public function setXrdClient(?string $xrdClient): self
    {
        $this->xrdClient = $xrdClient;

        return $this;
    }

    public function getXrdService(): ?string
    {
        return $this->xrdService;
    }

    public function setXrdService(?string $xrdService): self
    {
        $this->xrdService = $xrdService;

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
