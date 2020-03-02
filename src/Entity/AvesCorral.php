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
 * @ORM\Table(name="gs_avescorral")
 * @ORM\Entity(repositoryClass="App\Repository\AvesCorralRepository")
 * @JMS\XmlRoot("Registro")
 */
class AvesCorral
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
     * @ORM\Column(name="avcEsp", type="string", length=255)
     * @JMS\Type("string")
     */
    private $avcEsp;

    /**
     * @ORM\Column(name="avcTpn", type="string", length=255)
     * @JMS\Type("string")
     */
    private $avcTpn;

    /**
     * @ORM\Column(name="avcNop", type="string", length=255)
     * @JMS\Type("string")
     */
    private $avcNop;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="opAvesCorral", cascade={"all"})
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id")
     */
    private $avcOperator;

    /**
     * @var \DateTime
     * @ORM\Column(name="createdDate",type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="updatedDate",type="datetime", nullable=true)
     */
    private $updatedDate;

    public function __construct($codigo, $avcEsp, $avcTpn, $avcNop)
    {
        $this->codigo = $codigo;
        $this->avcEsp = $avcEsp;
        $this->avcTpn = $avcTpn;
        $this->avcNop = $avcNop;
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
     * @return AvesCorral
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
     * Set avcEsp
     *
     * @param string $avcEsp
     * @return AvesCorral
     */
    public function setAvcEsp($avcEsp)
    {
        $this->avcEsp = $avcEsp;

        return $this;
    }

    /**
     * Get avcEsp
     *
     * @return string
     */
    public function getAvcEsp()
    {
        return $this->avcEsp;
    }

    /**
     * Set avcTpn
     *
     * @param string $avcTpn
     * @return AvesCorral
     */
    public function setAvcTpn($avcTpn)
    {
        $this->avcTpn = $avcTpn;

        return $this;
    }

    /**
     * Get avcTpn
     *
     * @return string
     */
    public function getAvcTpn()
    {
        return $this->avcTpn;
    }

    /**
     * Set avcNop
     *
     * @param string $avcNop
     * @return AvesCorral
     */
    public function setAvcNop($avcNop)
    {
        $this->avcNop = $avcNop;

        return $this;
    }

    /**
     * Get avcNop
     *
     * @return string
     */
    public function getAvcNop()
    {
        return $this->avcNop;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return AvesCorral
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
     * @return AvesCorral
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
     * Set avcOperator
     *
     * @param \App\Entity\Operator $avcOperator
     * @return AvesCorral
     */
    public function setAvcOperator(Operator $avcOperator = null)
    {
        $this->avcOperator = $avcOperator;

        return $this;
    }

    /**
     * Get avcOperator
     *
     * @return \App\Entity\Operator
     */
    public function getAvcOperator()
    {
        return $this->avcOperator;
    }
}
