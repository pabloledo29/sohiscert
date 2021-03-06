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
 * @ORM\Table(name="gs_ganaderias")
 * @ORM\Entity(repositoryClass="App\Repository\GanaderiasRepository")
 * @JMS\XmlRoot("Registro")
 */
class Ganaderias
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
     * @ORM\Column(name="gnRc", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $gnRc;

    /**
     * @ORM\Column(name="gnEsp", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $gnEsp;

    /**
     * @ORM\Column(name="gnTpn", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     *
     */
    private $gnTpn;

    /**
     * @ORM\Column(name="gnPro", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $gnPro;

    /**
     * @ORM\ManyToOne(targetEntity="Especies")
     * @ORM\JoinColumn(name="especies_id", referencedColumnName="id", nullable=true)
     */
    private $gnEspecies;

    /**
     * @ORM\ManyToOne(targetEntity="TiposProducc")
     * @ORM\JoinColumn(name="tproducc_id", referencedColumnName="id", nullable=true)
     *
     */
    private $gnTiposProducc;

    /**
     * @ORM\ManyToOne(targetEntity="ProductosG")
     * @ORM\JoinColumn(name="productosG_id", referencedColumnName="id", nullable=true)
     */
    private $gnProductosG;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="opGanaderias", cascade={"all"})
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id")
     *
     */
    private $gnOperator;

    /**
     * @ORM\Column(name="gnTpcu", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $gnTpcu;

    /**
     * @ORM\Column(name="gnNop", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $gnNop;

    /**
     * @ORM\Column(name="gnRaza", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     *
     */
    private $gnRaza;

    /**
     * @ORM\Column(name="gnUcap", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $gnUcap;


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

    public function __construct($codigo, $gnNop, $gnRc, $gnEsp, $gnTpn, $gnPro, $gnTpcu, $gnRaza, $gnUcap)
    {
        $this->codigo = $codigo;
        $this->gnNop = $gnNop;
        $this->gnRc = $gnRc;
        $this->gnEsp = $gnEsp;
        $this->gnTpn = $gnTpn;
        $this->gnPro = $gnPro;
        $this->gnTpcu = $gnTpcu;
        $this->gnRaza = $gnRaza;
        $this->gnUcap = $gnUcap;
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
     * @return Ganaderias
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
     * Set gnRc
     *
     * @param string $gnRc
     * @return Ganaderias
     */
    public function setGnRc($gnRc)
    {
        $this->gnRc = $gnRc;

        return $this;
    }

    /**
     * Get gnRc
     *
     * @return string
     */
    public function getGnRc()
    {
        return $this->gnRc;
    }

    /**
     * Set gnEsp
     *
     * @param string $gnEsp
     * @return Ganaderias
     */
    public function setGnEsp($gnEsp)
    {
        $this->gnEsp = $gnEsp;

        return $this;
    }

    /**
     * Get gnEsp
     *
     * @return string
     */
    public function getGnEsp()
    {
        return $this->gnEsp;
    }

    /**
     * Set gnTpn
     *
     * @param string $gnTpn
     * @return Ganaderias
     */
    public function setGnTpn($gnTpn)
    {
        $this->gnTpn = $gnTpn;

        return $this;
    }

    /**
     * Get gnTpn
     *
     * @return string
     */
    public function getGnTpn()
    {
        return $this->gnTpn;
    }

    /**
     * Set gnPro
     *
     * @param string $gnPro
     * @return Ganaderias
     */
    public function setGnPro($gnPro)
    {
        $this->gnPro = $gnPro;

        return $this;
    }

    /**
     * Get gnPro
     *
     * @return string
     */
    public function getGnPro()
    {
        return $this->gnPro;
    }

    /**
     * Set gnTpcu
     *
     * @param string $gnTpcu
     * @return Ganaderias
     */
    public function setGnTpcu($gnTpcu)
    {
        $this->gnTpcu = $gnTpcu;

        return $this;
    }

    /**
     * Get gnTpcu
     *
     * @return string
     */
    public function getGnTpcu()
    {
        return $this->gnTpcu;
    }

    /**
     * Set gnNop
     *
     * @param string $gnNop
     * @return Ganaderias
     */
    public function setGnNop($gnNop)
    {
        $this->gnNop = $gnNop;

        return $this;
    }

    /**
     * Get gnNop
     *
     * @return string
     */
    public function getGnNop()
    {
        return $this->gnNop;
    }

    /**
     * Set gnRaza
     *
     * @param string $gnRaza
     * @return Ganaderias
     */
    public function setGnRaza($gnRaza)
    {
        $this->gnRaza = $gnRaza;

        return $this;
    }

    /**
     * Get gnRaza
     *
     * @return string
     */
    public function getGnRaza()
    {
        return $this->gnRaza;
    }

    /**
     * Set gnUcap
     *
     * @param string $gnUcap
     * @return Ganaderias
     */
    public function setGnUcap($gnUcap)
    {
        $this->gnUcap = $gnUcap;

        return $this;
    }

    /**
     * Get gnUcap
     *
     * @return string
     */
    public function getGnUcap()
    {
        return $this->gnUcap;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Ganaderias
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
     * @return Ganaderias
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
     * Set gnEspecies
     *
     * @param \App\Entity\Especies $gnEspecies
     * @return Ganaderias
     */
    public function setGnEspecies(\App\Entity\Especies $gnEspecies = null)
    {
        $this->gnEspecies = $gnEspecies;

        return $this;
    }

    /**
     * Get gnEspecies
     *
     * @return \App\Entity\Especies
     */
    public function getGnEspecies()
    {
        return $this->gnEspecies;
    }

    /**
     * Set gnTiposProducc
     *
     * @param \App\Entity\TiposProducc $gnTiposProducc
     * @return Ganaderias
     */
    public function setGnTiposProducc(\App\Entity\TiposProducc $gnTiposProducc = null)
    {
        $this->gnTiposProducc = $gnTiposProducc;

        return $this;
    }

    /**
     * Get gnTiposProducc
     *
     * @return \App\Entity\TiposProducc
     */
    public function getGnTiposProducc()
    {
        return $this->gnTiposProducc;
    }

    /**
     * Set gnProductosG
     *
     * @param \App\Entity\ProductosG $gnProductosG
     * @return Ganaderias
     */
    public function setGnProductosG(\App\Entity\ProductosG $gnProductosG = null)
    {
        $this->gnProductosG = $gnProductosG;

        return $this;
    }

    /**
     * Get gnProductosG
     *
     * @return \App\Entity\ProductosG
     */
    public function getGnProductosG()
    {
        return $this->gnProductosG;
    }

    /**
     * Set gnOperator
     *
     * @param \App\Entity\Operator $gnOperator
     * @return Ganaderias
     */
    public function setGnOperator(Operator $gnOperator = null)
    {
        $this->gnOperator = $gnOperator;

        return $this;
    }

    /**
     * Get gnOperator
     *
     * @return \App\Entity\Operator
     */
    public function getGnOperator()
    {
        return $this->gnOperator;
    }
}
