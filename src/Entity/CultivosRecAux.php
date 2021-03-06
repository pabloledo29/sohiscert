<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="gs_cultivosrec_aux")
 * @JMS\XmlRoot("Registro")
 */
class CultivosRecAux
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
     * @ORM\Column(name="$idOperator", type="integer", unique=true, length=10)
     */
    private $idOperator;

    /**
     * @ORM\Column(name="$nopOperator", type="string", length=40)
     */
    private $nopOperator;

    /**
     * @ORM\Column(name="$createCultivo", type="boolean", length=10, nullable=true)
     */
    private $createCultivo;

    /**
     * @ORM\Column(name="$updateCultivo", type="boolean", length=10, nullable=true)
     *
     */
    private $updateCultivo;

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


    public function __construct()
    {
        $this->idOperator = new ArrayCollection();
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
     * Set idOperator
     *
     * @param integer $idOperator
     * @return CultivosRecAux
     */
    public function setIdOperator($idOperator)
    {
        $this->idOperator = $idOperator;

        return $this;
    }

    /**
     * Get idOperator
     *
     * @return integer
     */
    public function getIdOperator()
    {
        return $this->idOperator;
    }

    /**
     * Set nopOperator
     *
     * @param string $nopOperator
     * @return CultivosRecAux
     */
    public function setNopOperator($nopOperator)
    {
        $this->nopOperator = $nopOperator;

        return $this;
    }

    /**
     * Get nopOperator
     *
     * @return string
     */
    public function getNopOperator()
    {
        return $this->nopOperator;
    }

    /**
     * Set createCultivo
     *
     * @param boolean $createCultivo
     * @return CultivosRecAux
     */
    public function setCreateCultivo($createCultivo)
    {
        $this->createCultivo = $createCultivo;

        return $this;
    }

    /**
     * Get createCultivo
     *
     * @return boolean
     */
    public function getCreateCultivo()
    {
        return $this->createCultivo;
    }

    /**
     * Set updateCultivo
     *
     * @param boolean $updateCultivo
     * @return CultivosRecAux
     */
    public function setUpdateCultivo($updateCultivo)
    {
        $this->updateCultivo = $updateCultivo;

        return $this;
    }

    /**
     * Get updateCultivo
     *
     * @return boolean
     */
    public function getUpdateCultivo()
    {
        return $this->updateCultivo;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return CultivosRecAux
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
     * @return CultivosRecAux
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
