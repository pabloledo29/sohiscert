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
 * @ORM\Table(name="gs_tiposcultivos")
 * @ORM\Entity(repositoryClass="App\Repository\TiposCultivosRepository")
 * @JMS\XmlRoot("Registro")
 */
class TiposCultivos
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
     * @ORM\Column(name="tcDeno", type="string", length=255)
     * @JMS\Type("string")
     */
    private $tcDeno;

    /**
     * @ORM\Column(name="tcRoae", type="string", length=6, nullable=true)
     * @JMS\Type("string")
     */
    private $tcRoae;

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
     * @return TiposCultivos
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
     * Set tcDeno
     *
     * @param string $tcDeno
     * @return TiposCultivos
     */
    public function setTcDeno($tcDeno)
    {
        $this->tcDeno = $tcDeno;

        return $this;
    }

    /**
     * Get tcDeno
     *
     * @return string
     */
    public function getTcDeno()
    {
        return $this->tcDeno;
    }

    /**
     * Set tcRoae
     *
     * @param string $tcRoae
     * @return TiposCultivos
     */
    public function setTcRoae($tcRoae)
    {
        $this->tcRoae = $tcRoae;

        return $this;
    }

    /**
     * Get tcRoae
     *
     * @return string
     */
    public function getTcRoae()
    {
        return $this->tcRoae;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return TiposCultivos
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
     * @return TiposCultivos
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
}
