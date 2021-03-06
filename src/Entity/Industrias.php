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
 * @ORM\Table(name="gs_industrias")
 * @ORM\Entity(repositoryClass="App\Repository\IndustriasRepository")
 * @JMS\XmlRoot("Registro")
 */
class Industrias
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
     * @ORM\Column(name="codigo", type="string", length=10)
     * @JMS\XmlAttribute
     * @JMS\Type("string")
     */
    private $codigo;

    /**
     * @ORM\Column(name="inSit", type="string", length=3, nullable=true)
     * @JMS\Type("string")
     */
    private $inSit;

    /**
     * @ORM\Column(name="inCcl", type="integer", length=6, nullable=true)
     * @JMS\Type("integer")
     */
    private $inCcl;

    /**
     * @ORM\Column(name="inAct", type="string", length=5, nullable=true)
     * @JMS\Type("string")
     */
    private $inAct;

    /**
     * @ORM\ManyToOne(targetEntity="ActividadesI")
     * @ORM\JoinColumn(name="actividadesi_id", referencedColumnName="id", nullable=true)
     *
     */
    private $inActividadI;

    /**
     * @ORM\Column(name="inNop", type="string", length=40, nullable=true)
     * @JMS\Type("string")
     */
    private $inNop;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="opIndustrias", cascade={"all"})
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id")
     *
     */
    private $inOperator;


    /**
     * @ORM\Column(name="inCdp", type="integer", length=6, nullable=true, options={"default" = 0})
     * @JMS\Type("integer")
     */
    private $inCdp;

    /**
     * @ORM\Column(name="inDom", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $inDom;

    /**
     * @ORM\Column(name="inTel", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $inTel;

    /**
     * @ORM\Column(name="inPob", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $inPob;

    /**
     * @ORM\Column(name="inProv", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $inProv;


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
     * @return Industrias
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
     * Set inAct
     *
     * @param string $inAct
     * @return Industrias
     */
    public function setInAct($inAct)
    {
        $this->inAct = $inAct;

        return $this;
    }

    /**
     * Get inAct
     *
     * @return string
     */
    public function getInAct()
    {
        return $this->inAct;
    }

    /**
     * Set inNop
     *
     * @param string $inNop
     * @return Industrias
     */
    public function setInNop($inNop)
    {
        $this->inNop = $inNop;

        return $this;
    }

    /**
     * Get inNop
     *
     * @return string
     */
    public function getInNop()
    {
        return $this->inNop;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Industrias
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
     * @return Industrias
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
     * Set inActividadI
     *
     * @param \App\Entity\ActividadesI $inActividadI
     * @return Industrias
     */
    public function setInActividadI(ActividadesI $inActividadI = null)
    {
        $this->inActividadI = $inActividadI;

        return $this;
    }

    /**
     * Get inActividadI
     *
     * @return \App\Entity\ActividadesI
     */
    public function getInActividadI()
    {
        return $this->inActividadI;
    }

    /**
     * Set inOperator
     *
     * @param \App\Entity\Operator $inOperator
     * @return Industrias
     */
    public function setInOperator(Operator $inOperator = null)
    {
        $this->inOperator = $inOperator;

        return $this;
    }

    /**
     * Get inOperator
     *
     * @return \App\Entity\Operator
     */
    public function getInOperator()
    {
        return $this->inOperator;
    }

    /**
     * Set inCdp
     *
     * @param integer $inCdp
     * @return Industrias
     */
    public function setInCdp($inCdp)
    {
        $this->inCdp = $inCdp;

        return $this;
    }

    /**
     * Get inCdp
     *
     * @return integer
     */
    public function getInCdp()
    {
        return $this->inCdp;
    }

    /**
     * Set inDom
     *
     * @param string $inDom
     * @return Industrias
     */
    public function setInDom($inDom)
    {
        $this->inDom = $inDom;

        return $this;
    }

    /**
     * Get inDom
     *
     * @return string
     */
    public function getInDom()
    {
        return $this->inDom;
    }

    /**
     * Set inTel
     *
     * @param string $inTel
     * @return Industrias
     */
    public function setInTel($inTel)
    {
        $this->inTel = $inTel;

        return $this;
    }

    /**
     * Get inTel
     *
     * @return string
     */
    public function getInTel()
    {
        return $this->inTel;
    }

    /**
     * Set inPob
     *
     * @param string $inPob
     * @return Industrias
     */
    public function setInPob($inPob)
    {
        $this->inPob = $inPob;

        return $this;
    }

    /**
     * Get inPob
     *
     * @return string
     */
    public function getInPob()
    {
        return $this->inPob;
    }

    /**
     * Set inProv
     *
     * @param string $inProv
     * @return Industrias
     */
    public function setInProv($inProv)
    {
        $this->inProv = $inProv;

        return $this;
    }

    /**
     * Get inProv
     *
     * @return string
     */
    public function getInProv()
    {
        return $this->inProv;
    }

    /**
     * Set inCcl
     *
     * @param integer $inCcl
     * @return Industrias
     */
    public function setInCcl($inCcl)
    {
        $this->inCcl = $inCcl;

        return $this;
    }

    /**
     * Get inCcl
     *
     * @return integer
     */
    public function getInCcl()
    {
        return $this->inCcl;
    }

    /**
     * Set inSit
     *
     * @param string $inSit
     * @return Industrias
     */
    public function setInSit($inSit)
    {
        $this->inSit = $inSit;

        return $this;
    }

    /**
     * Get inSit
     *
     * @return string
     */
    public function getInSit()
    {
        return $this->inSit;
    }
}
