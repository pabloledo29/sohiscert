<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 8/03/16
 * Time: 9:23
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="gs_contact")
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 * @JMS\XmlRoot("Registro")
 */
class Contact
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
     * @ORM\Column(name="cnCdcl", type="string", nullable=true)
     * @JMS\Type("string")
     */
    private $cnCdcl;

    /**
     * @ORM\Column(name="cnDeno", type="string", nullable=true)
     * @JMS\Type("string")
     */
    private $cnDeno;

    /**
     * @ORM\Column(name="cnApe1", type="string", nullable=true)
     * @JMS\Type("string")
     */
    private $cnApe1;

    /**
     * @ORM\Column(name="cnApe2", type="string", nullable=true)
     * @JMS\Type("string")
     */
    private $cnApe2;

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
     * @return Contact
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
     * Set cnDeno
     *
     * @param string $cnDeno
     * @return Contact
     */
    public function setCnDeno($cnDeno)
    {
        $this->cnDeno = $cnDeno;

        return $this;
    }

    /**
     * Get cnDeno
     *
     * @return string
     */
    public function getCnDeno()
    {
        return $this->cnDeno;
    }

    /**
     * Set cnApe1
     *
     * @param string $cnApe1
     * @return Contact
     */
    public function setCnApe1($cnApe1)
    {
        $this->cnApe1 = $cnApe1;

        return $this;
    }

    /**
     * Get cnApe1
     *
     * @return string
     */
    public function getCnApe1()
    {
        return $this->cnApe1;
    }

    /**
     * Set cnApe2
     *
     * @param string $cnApe2
     * @return Contact
     */
    public function setCnApe2($cnApe2)
    {
        $this->cnApe2 = $cnApe2;

        return $this;
    }

    /**
     * Get cnApe2
     *
     * @return string
     */
    public function getCnApe2()
    {
        return $this->cnApe2;
    }

    /**
     * @return mixed
     */
    public function getCnCdcl()
    {
        return $this->cnCdcl;
    }

    /**
     * @param mixed $cnCdcl
     */
    public function setCnCdcl($cnCdcl)
    {
        $this->cnCdcl = $cnCdcl;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Contact
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
     * @return Contact
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
