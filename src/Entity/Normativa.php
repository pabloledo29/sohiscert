<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="normativa")
 */
class Normativa
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
     * @ORM\Column(name="normaReg", type="string", length=10, nullable=true)
     */
    private $normaReg;

    /**
     * @ORM\Column(name="normaSreg", type="string", length=10, nullable=true)
     */
    private $normaSreg;

    /**
     * @ORM\Column(name="normaRegDeno", type="string", length=255, nullable=true)
     */
    private $normaRegDeno;

    /**
     * @ORM\Column(name="normaSregDeno", type="string", length=255, nullable=true)
     */
    private $normaSregDeno;

    /**
     * @ORM\Column(name="normaCa", type="string", length=3, nullable=true)
     */
    private $normaCa;

    /**
     * @ORM\Column(name="normaCaDeno", type="string", length=30, nullable=true)
     */
    private $normaCaDeno;

    /**
     * @ORM\Column(name="normaNormativa", type="text", nullable=true)
     */
    private $normaNormativa;

    /**
     *
     * @ORM\Column(name="normaProducto", type="string", length=50, nullable=true)
     */
    private $normaProducto;

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
     * Set normaReg
     *
     * @param string $normaReg
     * @return Normativa
     */
    public function setNormaReg($normaReg)
    {
        $this->normaReg = $normaReg;

        return $this;
    }

    /**
     * Get normaReg
     *
     * @return string
     */
    public function getNormaReg()
    {
        return $this->normaReg;
    }

    /**
     * Set normaSreg
     *
     * @param string $normaSreg
     * @return Normativa
     */
    public function setNormaSreg($normaSreg)
    {
        $this->normaSreg = $normaSreg;

        return $this;
    }

    /**
     * Get normaSreg
     *
     * @return string
     */
    public function getNormaSreg()
    {
        return $this->normaSreg;
    }

    /**
     * Set normaRegDeno
     *
     * @param string $normaRegDeno
     * @return Normativa
     */
    public function setNormaRegDeno($normaRegDeno)
    {
        $this->normaRegDeno = $normaRegDeno;

        return $this;
    }

    /**
     * Get normaRegDeno
     *
     * @return string
     */
    public function getNormaRegDeno()
    {
        return $this->normaRegDeno;
    }

    /**
     * Set normaSregDeno
     *
     * @param string $normaSregDeno
     * @return Normativa
     */
    public function setNormaSregDeno($normaSregDeno)
    {
        $this->normaSregDeno = $normaSregDeno;

        return $this;
    }

    /**
     * Get normaSregDeno
     *
     * @return string
     */
    public function getNormaSregDeno()
    {
        return $this->normaSregDeno;
    }

    /**
     * Set normaCa
     *
     * @param string $normaCa
     * @return Normativa
     */
    public function setNormaCa($normaCa)
    {
        $this->normaCa = $normaCa;

        return $this;
    }

    /**
     * Get normaCa
     *
     * @return string
     */
    public function getNormaCa()
    {
        return $this->normaCa;
    }

    /**
     * Set normaCaDeno
     *
     * @param string $normaCaDeno
     * @return Normativa
     */
    public function setNormaCaDeno($normaCaDeno)
    {
        $this->normaCaDeno = $normaCaDeno;

        return $this;
    }

    /**
     * Get normaCaDeno
     *
     * @return string
     */
    public function getNormaCaDeno()
    {
        return $this->normaCaDeno;
    }

    /**
     * Set normaNormativa
     *
     * @param string $normaNormativa
     * @return Normativa
     */
    public function setNormaNormativa($normaNormativa)
    {
        $this->normaNormativa = $normaNormativa;

        return $this;
    }

    /**
     * Get normaNormativa
     *
     * @return string
     */
    public function getNormaNormativa()
    {
        return $this->normaNormativa;
    }

    /**
     * Set normaProducto
     *
     * @param string $normaProducto
     * @return Normativa
     */
    public function setNormaProducto($normaProducto)
    {
        $this->normaProducto = $normaProducto;

        return $this;
    }

    /**
     * Get normaProducto
     *
     * @return string
     */
    public function getNormaProducto()
    {
        return $this->normaProducto;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Normativa
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
}
