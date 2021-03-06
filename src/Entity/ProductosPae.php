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
 * @ORM\Table(name="gs_productospae")
 * @ORM\Entity(repositoryClass="App\Repository\ProductosPaeRepository")
 * @JMS\XmlRoot("Registro")
 */
class ProductosPae
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
     * @ORM\Column(name="pipPro", type="string", length=255)
     * @JMS\Type("string")
     */
    private $pipPro;

    /**
     * @ORM\Column(name="pipEst", type="string", length=10, nullable=true)
     * @JMS\Type("string")
     */
    private $pipEst;

    /**
     * @ORM\Column(name="pipDsc", type="string", length=255)
     * @JMS\Type("string")
     */
    private $pipDsc;

    /**
     * @ORM\Column(name="pipTpp", type="string", length=255)
     * @JMS\Type("string")
     */
    private $pipTpp;

    /**
     * @ORM\Column(name="pipNop", type="string", length=255)
     * @JMs\Type("string")
     */
    private $pipNop;

    /**
     * @ORM\ManyToOne(targetEntity="Operator", inversedBy="opProductosPae", cascade={"all"})
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id")
     */
    private $pipOperator;


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

    public function __construct($codigo, $pipPro, $pipDsc, $pipTpp, $pipNop)
    {
        $this->codigo = $codigo;
        $this->pipPro = $pipPro;
        $this->pipDsc = $pipDsc;
        $this->pipTpp = $pipTpp;
        $this->pipNop = $pipNop;
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
     * @return ProductosPae
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
     * Set pipPro
     *
     * @param string $pipPro
     * @return ProductosPae
     */
    public function setPipPro($pipPro)
    {
        $this->pipPro = $pipPro;

        return $this;
    }

    /**
     * Get pipPro
     *
     * @return string
     */
    public function getPipPro()
    {
        return $this->pipPro;
    }

    /**
     * Set pipDsc
     *
     * @param string $pipDsc
     * @return ProductosPae
     */
    public function setPipDsc($pipDsc)
    {
        $this->pipDsc = $pipDsc;

        return $this;
    }

    /**
     * Get pipDsc
     *
     * @return string
     */
    public function getPipDsc()
    {
        return $this->pipDsc;
    }

    /**
     * Set pipTpp
     *
     * @param string $pipTpp
     * @return ProductosPae
     */
    public function setPipTpp($pipTpp)
    {
        $this->pipTpp = $pipTpp;

        return $this;
    }

    /**
     * Get pipTpp
     *
     * @return string
     */
    public function getPipTpp()
    {
        return $this->pipTpp;
    }

    /**
     * Set pipNop
     *
     * @param string $pipNop
     * @return ProductosPae
     */
    public function setPipNop($pipNop)
    {
        $this->pipNop = $pipNop;

        return $this;
    }

    /**
     * Get pipNop
     *
     * @return string
     */
    public function getPipNop()
    {
        return $this->pipNop;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return ProductosPae
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
     * @return ProductosPae
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
     * Set pipOperator
     *
     * @param \App\Entity\Operator $pipOperator
     * @return ProductosPae
     */
    public function setPipOperator(Operator $pipOperator = null)
    {
        $this->pipOperator = $pipOperator;

        return $this;
    }

    /**
     * Get pipOperator
     *
     * @return \App\Entity\Operator
     */
    public function getPipOperator()
    {
        return $this->pipOperator;
    }

    /**
     * Set pipEst
     *
     * @param string $pipEst
     * @return ProductosPae
     */
    public function setPipEst($pipEst)
    {
        $this->pipEst = $pipEst;

        return $this;
    }

    /**
     * Get pipEst
     *
     * @return string
     */
    public function getPipEst()
    {
        return $this->pipEst;
    }
}
