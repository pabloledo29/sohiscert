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
 * @ORM\Table(name="gs_cultivosrec2")
 * @ORM\Entity(repositoryClass="App\Repository\CultivosRec2Repository")
 * @JMS\XmlRoot("Registro")
 */
class CultivosRec2
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
     * @ORM\Column(name="ru2Sit", type="string", length=15, nullable=true)
     * @JMS\Type("string")
     */
    private $ru2Sit;

    /**
     * @ORM\Column(name="ru2Pol", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $ru2Pol;

    /**
     * @ORM\Column(name="ru2Par", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $ru2Par;

    /**
     * @ORM\Column(name="ru2Cul", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     *
     */
    private $ru2Cul;

    /**
     * @ORM\ManyToOne(targetEntity="Cultivos")
     * @ORM\JoinColumn(name="cultivos_id", referencedColumnName="id", nullable=true)
     *
     */
    private $ru2Cultivos;

    /**
     * @ORM\Column(name="ru2Rec", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $ru2Rec;

    /**
     * @ORM\Column(name="ru2Nop", type="string", length=40, nullable=true)
     * @JMS\Type("string")
     *
     */
    private $ru2Nop;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="opCultivosRec2", cascade={"all"})
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id")
     */
    private $ru2Operator;


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

    public function __construct($codigo, $ru2Nop, $ru2Pol, $ru2Par, $ru2Cul, $ru2Rec)
    {
        $this->codigo = $codigo;
        $this->ru2Nop = $ru2Nop;
        $this->ru2Pol = $ru2Pol;
        $this->ru2Par = $ru2Par;
        $this->ru2Cul = $ru2Cul;
        $this->ru2Rec = $ru2Rec;
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
     * @return CultivosRec2
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
     * Set ru2Pol
     *
     * @param string $ru2Pol
     * @return CultivosRec2
     */
    public function setRu2Pol($ru2Pol)
    {
        $this->ru2Pol = $ru2Pol;

        return $this;
    }

    /**
     * Get ru2Pol
     *
     * @return string
     */
    public function getRu2Pol()
    {
        return $this->ru2Pol;
    }

    /**
     * Set ru2Par
     *
     * @param string $ru2Par
     * @return CultivosRec2
     */
    public function setRu2Par($ru2Par)
    {
        $this->ru2Par = $ru2Par;

        return $this;
    }

    /**
     * Get ru2Par
     *
     * @return string
     */
    public function getRu2Par()
    {
        return $this->ru2Par;
    }

    /**
     * Set ru2Cul
     *
     * @param string $ru2Cul
     * @return CultivosRec2
     */
    public function setRu2Cul($ru2Cul)
    {
        $this->ru2Cul = $ru2Cul;

        return $this;
    }

    /**
     * Get ru2Cul
     *
     * @return string
     */
    public function getRu2Cul()
    {
        return $this->ru2Cul;
    }

    /**
     * Set ru2Rec
     *
     * @param string $ru2Rec
     * @return CultivosRec2
     */
    public function setRu2Rec($ru2Rec)
    {
        $this->ru2Rec = $ru2Rec;

        return $this;
    }

    /**
     * Get ru2Rec
     *
     * @return string
     */
    public function getRu2Rec()
    {
        return $this->ru2Rec;
    }

    /**
     * Set ru2Nop
     *
     * @param string $ru2Nop
     * @return CultivosRec2
     */
    public function setRu2Nop($ru2Nop)
    {
        $this->ru2Nop = $ru2Nop;

        return $this;
    }

    /**
     * Get ru2Nop
     *
     * @return string
     */
    public function getRu2Nop()
    {
        return $this->ru2Nop;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return CultivosRec2
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
     * @return CultivosRec2
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
     * Set ru2Cultivos
     *
     * @param \App\Entity\Cultivos $ru2Cultivos
     * @return CultivosRec2
     */
    public function setRu2Cultivos(Cultivos $ru2Cultivos = null)
    {
        $this->ru2Cultivos = $ru2Cultivos;

        return $this;
    }

    /**
     * Get ru2Cultivos
     *
     * @return \App\Entity\Cultivos
     */
    public function getRu2Cultivos()
    {
        return $this->ru2Cultivos;
    }

    /**
     * Set ru2Operator
     *
     * @param \App\Entity\Operator $ru2Operator
     * @return CultivosRec2
     */
    public function setRu2Operator(Operator $ru2Operator = null)
    {
        $this->ru2Operator = $ru2Operator;

        return $this;
    }

    /**
     * Get ru2Operator
     *
     * @return \App\Entity\Operator
     */
    public function getRu2Operator()
    {
        return $this->ru2Operator;
    }

    /**
     * Set ru2Sit
     *
     * @param string $ru2Sit
     * @return CultivosRec2
     */
    public function setRu2Sit($ru2Sit)
    {
        $this->ru2Sit = $ru2Sit;

        return $this;
    }

    /**
     * Get ru2Sit
     *
     * @return string
     */
    public function getRu2Sit()
    {
        return $this->ru2Sit;
    }
}
