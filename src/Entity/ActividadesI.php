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
 * @ORM\Table(name="gs_actividades_i")
 * @ORM\Entity(repositoryClass="App\Repository\ActividadesIRepository")
 * @JMS\XmlRoot("Registro")
 */
class ActividadesI
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
     * @ORM\Column(name="ainDeno", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $ainDeno;


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
     * @return ActividadesI
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
     * Set ainDeno
     *
     * @param string $ainDeno
     * @return ActividadesI
     */
    public function setAinDeno($ainDeno)
    {
        $this->ainDeno = $ainDeno;

        return $this;
    }

    /**
     * Get ainDeno
     *
     * @return string
     */
    public function getAinDeno()
    {
        return $this->ainDeno;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return ActividadesI
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
     * @return ActividadesI
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
}
