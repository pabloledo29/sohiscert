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
 * @ORM\Table(name="gs_cultivosrec")
 * @ORM\Entity(repositoryClass="App\Repository\CultivosRepository")
 * @JMS\XmlRoot("Registro")
 */
class CultivosRec
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
     * @ORM\Column(name="ruNop", type="string", length=40, nullable=true)
     * @JMS\Type("string")
     */
    private $ruNop;

    /**
     * @ORM\Column(name="ruSitr", type="string", length=3, nullable=true)
     * @JMS\Type("string")
     */
    private $ruSitr;

    /**
     *
     * @ORM\Column(name="ruTppr", type="string", length=10, nullable=true)
     * @JMS\Type("string")
     */
    private $ruTppr;

    /**
     * @ORM\ManyToOne(targetEntity="TiposProducto")
     * @ORM\JoinColumn(name="tiposproducto_id", referencedColumnName="id", nullable=true)
     *
     */
    private $ruTiposProducto;

    /**
     *
     * @ORM\Column(name="ruPro", type="string", length=10, nullable=true)
     * @JMS\Type("string")
     */
    private $ruPro;

    /**
     * @ORM\ManyToOne(targetEntity="Productos")
     * @ORM\JoinColumn(name="productos_id", referencedColumnName="id", nullable=true)
     *
     */
    private $ruProducto;

    /**
     * @ORM\Column(name="ruCul", type="string", length=10, nullable=true)
     * @JMS\Type("string")
     */
    private $ruCul;

    /**
     * @ORM\ManyToOne(targetEntity="Cultivos")
     * @ORM\JoinColumn(name="cultivos_id", referencedColumnName="id", nullable=true)
     *
     */
    private $ruCultivos;

    /**
     * @ORM\Column(name="ruAct", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $ruAct;

    /**
     * @ORM\Column(name="ruRc", type="string", length=20, nullable=true)
     * @JMS\Type("string")
     */
    private $ruRc;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="opCultivosRec", cascade={"all"})
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id")
     *
     */
    private $ruOperator;

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


    public function __construct($codigo, $ruNop, $ruTppr, $ruPro, $ruCul, $ruAct, $ruRc)
    {
        $this->codigo = $codigo;
        $this->ruNop = $ruNop;
        $this->ruTppr = $ruTppr;
        $this->ruPro = $ruPro;
        $this->ruCul = $ruCul;
        $this->ruAct = $ruAct;
        $this->ruRc = $ruRc;
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
     * @return CultivosRec
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
     * Set ruNop
     *
     * @param string $ruNop
     * @return CultivosRec
     */
    public function setRuNop($ruNop)
    {
        $this->ruNop = $ruNop;

        return $this;
    }

    /**
     * Get ruNop
     *
     * @return string
     */
    public function getRuNop()
    {
        return $this->ruNop;
    }

    /**
     * Set ruTppr
     *
     * @param string $ruTppr
     * @return CultivosRec
     */
    public function setRuTppr($ruTppr)
    {
        $this->ruTppr = $ruTppr;

        return $this;
    }

    /**
     * Get ruTppr
     *
     * @return string
     */
    public function getRuTppr()
    {
        return $this->ruTppr;
    }

    /**
     * Set ruPro
     *
     * @param string $ruPro
     * @return CultivosRec
     */
    public function setRuPro($ruPro)
    {
        $this->ruPro = $ruPro;

        return $this;
    }

    /**
     * Get ruPro
     *
     * @return string
     */
    public function getRuPro()
    {
        return $this->ruPro;
    }

    /**
     * Set ruCul
     *
     * @param string $ruCul
     * @return CultivosRec
     */
    public function setRuCul($ruCul)
    {
        $this->ruCul = $ruCul;

        return $this;
    }

    /**
     * Get ruCul
     *
     * @return string
     */
    public function getRuCul()
    {
        return $this->ruCul;
    }

    /**
     * Set ruAct
     *
     * @param string $ruAct
     * @return CultivosRec
     */
    public function setRuAct($ruAct)
    {
        $this->ruAct = $ruAct;

        return $this;
    }

    /**
     * Get ruAct
     *
     * @return string
     */
    public function getRuAct()
    {
        return $this->ruAct;
    }

    /**
     * Set ruRc
     *
     * @param string $ruRc
     * @return CultivosRec
     */
    public function setRuRc($ruRc)
    {
        $this->ruRc = $ruRc;

        return $this;
    }

    /**
     * Get ruRc
     *
     * @return string
     */
    public function getRuRc()
    {
        return $this->ruRc;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return CultivosRec
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
     * @return CultivosRec
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
     * Set ruTiposProducto
     *
     * @param \App\Entity\TiposProducto $ruTiposProducto
     * @return CultivosRec
     */
    public function setRuTiposProducto(TiposProducto $ruTiposProducto = null)
    {
        $this->ruTiposProducto = $ruTiposProducto;

        return $this;
    }

    /**
     * Get ruTiposProducto
     *
     * @return \App\Entity\TiposProducto
     */
    public function getRuTiposProducto()
    {
        return $this->ruTiposProducto;
    }

    /**
     * Set ruProducto
     *
     * @param \App\Entity\Productos $ruProducto
     * @return CultivosRec
     */
    public function setRuProducto(Productos $ruProducto = null)
    {
        $this->ruProducto = $ruProducto;

        return $this;
    }

    /**
     * Get ruProducto
     *
     * @return \App\Entity\Productos
     */
    public function getRuProducto()
    {
        return $this->ruProducto;
    }

    /**
     * Set ruCultivos
     *
     * @param \App\Entity\Cultivos $ruCultivos
     * @return CultivosRec
     */
    public function setRuCultivos(Cultivos $ruCultivos = null)
    {
        $this->ruCultivos = $ruCultivos;

        return $this;
    }

    /**
     * Get ruCultivos
     *
     * @return \App\Entity\Cultivos
     */
    public function getRuCultivos()
    {
        return $this->ruCultivos;
    }

    /**
     * Set ruOperator
     *
     * @param \App\Entity\Operator $ruOperator
     * @return CultivosRec
     */
    public function setRuOperator(Operator $ruOperator = null)
    {
        $this->ruOperator = $ruOperator;

        return $this;
    }

    /**
     * Get ruOperator
     *
     * @return \App\Entity\Operator
     */
    public function getRuOperator()
    {
        return $this->ruOperator;
    }

    /**
     * Set ruSitr
     *
     * @param string $ruSitr
     * @return CultivosRec
     */
    public function setRuSitr($ruSitr)
    {
        $this->ruSitr = $ruSitr;

        return $this;
    }

    /**
     * Get ruSitr
     *
     * @return string
     */
    public function getRuSitr()
    {
        return $this->ruSitr;
    }
}
