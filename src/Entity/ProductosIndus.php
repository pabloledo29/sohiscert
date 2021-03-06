<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="gs_productosindus")
 * @ORM\Entity(repositoryClass="App\Repository\ProductosIndusRepository")
 * @JMS\XmlRoot("Registro")
 */
class ProductosIndus
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="codigo", type="string", unique=true, length=10)
     * @JMS\XmlAttribute
     * @JMS\Type("string")
     */
    private $codigo;

    /**
     * @ORM\Column(name="piNop", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $piNop;

    /**
     * @ORM\Column(name="piDpro", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $piDpro;

    /**
     * @ORM\Column(name="piCpro", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $piCpro;


    /**
     * @ORM\ManyToOne(targetEntity="Productos")
     * @ORM\JoinColumn(name="productos_id", referencedColumnName="id", nullable=true)
     */
    private $piProductos;

    /**
     * @ORM\Column(name="piPro", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $piPro;

    /**
     * @ORM\Column(name="piMarca", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $piMarca;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="opProductosIndus", cascade={"all"})
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id")
     */
    private $piOperator;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate",type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedDate",type="datetime", nullable=true)
     */
    private $updatedDate;


    public function __construct($codigo, $piPro, $piMarca, $piNop, $piDpro, $piCpro)
    {
        $this->codigo = $codigo;
        $this->piPro = $piPro;
        $this->piMarca = $piMarca;
        $this->piNop = $piNop;
        $this->piDpro = $piDpro;
        $this->piCpro = $piCpro;
    }


    /**
     * @ORM\PrePersist
     */
    public function setCreatedDateValue()
    {
        $this->createdDate = new \DateTime(date('Y-m-d H:i:s'));
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PostUpdate
     */
    public function setUpdatedDateValue()
    {
        $this->updatedDate = new \DateTime(date('Y-m-d H:i:s'));
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return ProductosIndus
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set piNop
     *
     * @param string $piNop
     * @return ProductosIndus
     */
    public function setPiNop($piNop)
    {
        $this->piNop = $piNop;

        return $this;
    }

    /**
     * Get piNop
     *
     * @return string
     */
    public function getPiNop()
    {
        return $this->piNop;
    }

    /**
     * Set piDpro
     *
     * @param string $piDpro
     * @return ProductosIndus
     */
    public function setPiDpro($piDpro)
    {
        $this->piDpro = $piDpro;

        return $this;
    }

    /**
     * Get piDpro
     *
     * @return string
     */
    public function getPiDpro()
    {
        return $this->piDpro;
    }

    /**
     * Set piPro
     *
     * @param string $piPro
     * @return ProductosIndus
     */
    public function setPiPro($piPro)
    {
        $this->piPro = $piPro;

        return $this;
    }

    /**
     * Get piPro
     *
     * @return string
     */
    public function getPiPro()
    {
        return $this->piPro;
    }

    /**
     * Set piMarca
     *
     * @param string $piMarca
     * @return ProductosIndus
     */
    public function setPiMarca($piMarca)
    {
        $this->piMarca = $piMarca;

        return $this;
    }

    /**
     * Get piMarca
     *
     * @return string
     */
    public function getPiMarca()
    {
        return $this->piMarca;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return ProductosIndus
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set updatedDate
     *
     * @param \DateTime $updatedDate
     * @return ProductosIndus
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Set piProductos
     *
     * @param \App\Entity\Productos $piProductos
     * @return ProductosIndus
     */
    public function setPiProductos(Productos $piProductos = null)
    {
        $this->piProductos = $piProductos;

        return $this;
    }

    /**
     * Get piProductos
     *
     * @return \App\Entity\Productos
     */
    public function getPiProductos()
    {
        return $this->piProductos;
    }

    /**
     * Set piOperator
     *
     * @param \App\Entity\Operator $piOperator
     * @return ProductosIndus
     */
    public function setPiOperator(Operator $piOperator = null)
    {
        $this->piOperator = $piOperator;

        return $this;
    }

    /**
     * Get piOperator
     *
     * @return \App\Entity\Operator
     */
    public function getPiOperator()
    {
        return $this->piOperator;
    }

    /**
     * Set piCpro
     *
     * @param string $piCpro
     * @return ProductosIndus
     */
    public function setPiCpro($piCpro)
    {
        $this->piCpro = $piCpro;

        return $this;
    }

    /**
     * Get piCpro
     *
     * @return string
     */
    public function getPiCpro()
    {
        return $this->piCpro;
    }
}
