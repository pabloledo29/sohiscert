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
 * @ORM\Table(name="gs_productosg")
 * @ORM\Entity(repositoryClass="App\Repository\ProductosGRepository")
 * @JMS\XmlRoot("Registro")
 */
class ProductosG
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
     * @ORM\Column(name="pnDeno", type="string", length=255)
     * @JMS\Type("string")
     */
    private $pnDeno;

    /**
     * @ORM\Column(name="pnEst", type="string", length=3, nullable=true)
     */
    private $pnEst;

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
     * @return ProductosG
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
     * Set pnDeno
     *
     * @param string $pnDeno
     * @return ProductosG
     */
    public function setPnDeno($pnDeno)
    {
        $this->pnDeno = $pnDeno;

        return $this;
    }

    /**
     * Get pnDeno
     *
     * @return string
     */
    public function getPnDeno()
    {
        return $this->pnDeno;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return ProductosG
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
     * @return ProductosG
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
     * Set pnEst
     *
     * @param string $pnEst
     * @return ProductosG
     */
    public function setPnEst($pnEst)
    {
        $this->pnEst = $pnEst;

        return $this;
    }

    /**
     * Get pnEst
     *
     * @return string
     */
    public function getPnEst()
    {
        return $this->pnEst;
    }
}
