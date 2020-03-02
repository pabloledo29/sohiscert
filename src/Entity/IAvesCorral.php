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
 * @ORM\Table(name="gs_iavescorral")
 * @ORM\Entity(repositoryClass="App\Repository\IAvesCorralRepository")
 * @JMS\XmlRoot("Registro")
 */
class IAvesCorral
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
     * @ORM\Column(name="aviPro", type="string", length=255)
     * @JMS\Type("string")
     */
    private $aviPro;

    /**
     * @ORM\Column(name="aviPrd", type="string", length=255)
     * @JMS\Type("string")
     */
    private $aviPrd;

    /**
     * @ORM\Column(name="aviNop", type="string", length=255)
     * @JMS\Type("string")
     */
    private $aviNop;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="opIAvesCorral", cascade={"all"})
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id")
     */
    private $aviOperator;

    /**
     * @ORM\Column(name="aviVar", type="string", length=255)
     * @JMS\Type("string")
     */
    private $aviVar;

    /**
     * @ORM\Column(name="aviInd", type="string", length=255)
     * @JMS\Type("string")
     */
    private $aviInd;

    /**
     * @ORM\Column(name="aviMar", type="string", length=255)
     * @JMS\Type("string")
     */
    private $aviMar;

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

    public function __construct($codigo, $aviPrd, $aviVar, $aviInd, $aviMar, $aviNop)
    {
        $this->codigo = $codigo;
        $this->aviPrd = $aviPrd;
        $this->aviVar = $aviVar;
        $this->aviInd = $aviInd;
        $this->aviMar = $aviMar;
        $this->aviNop = $aviNop;
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
     * @return IAvesCorral
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
     * Set aviPro
     *
     * @param string $aviPro
     * @return IAvesCorral
     */
    public function setAviPro($aviPro)
    {
        $this->aviPro = $aviPro;

        return $this;
    }

    /**
     * Get aviPro
     *
     * @return string
     */
    public function getAviPro()
    {
        return $this->aviPro;
    }

    /**
     * Set aviPrd
     *
     * @param string $aviPrd
     * @return IAvesCorral
     */
    public function setAviPrd($aviPrd)
    {
        $this->aviPrd = $aviPrd;

        return $this;
    }

    /**
     * Get aviPrd
     *
     * @return string
     */
    public function getAviPrd()
    {
        return $this->aviPrd;
    }

    /**
     * Set aviNop
     *
     * @param string $aviNop
     * @return IAvesCorral
     */
    public function setAviNop($aviNop)
    {
        $this->aviNop = $aviNop;

        return $this;
    }

    /**
     * Get aviNop
     *
     * @return string
     */
    public function getAviNop()
    {
        return $this->aviNop;
    }

    /**
     * Set aviVar
     *
     * @param string $aviVar
     * @return IAvesCorral
     */
    public function setAviVar($aviVar)
    {
        $this->aviVar = $aviVar;

        return $this;
    }

    /**
     * Get aviVar
     *
     * @return string
     */
    public function getAviVar()
    {
        return $this->aviVar;
    }

    /**
     * Set aviInd
     *
     * @param string $aviInd
     * @return IAvesCorral
     */
    public function setAviInd($aviInd)
    {
        $this->aviInd = $aviInd;

        return $this;
    }

    /**
     * Get aviInd
     *
     * @return string
     */
    public function getAviInd()
    {
        return $this->aviInd;
    }

    /**
     * Set aviMar
     *
     * @param string $aviMar
     * @return IAvesCorral
     */
    public function setAviMar($aviMar)
    {
        $this->aviMar = $aviMar;

        return $this;
    }

    /**
     * Get aviMar
     *
     * @return string
     */
    public function getAviMar()
    {
        return $this->aviMar;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return IAvesCorral
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
     * @return IAvesCorral
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
     * Set aviOperator
     *
     * @param \App\Entity\Operator $aviOperator
     * @return IAvesCorral
     */
    public function setAviOperator(Operator $aviOperator = null)
    {
        $this->aviOperator = $aviOperator;

        return $this;
    }

    /**
     * Get aviOperator
     *
     * @return \App\Entity\Operator
     */
    public function getAviOperator()
    {
        return $this->aviOperator;
    }
}
