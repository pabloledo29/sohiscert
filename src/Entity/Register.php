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
 * @ORM\Table(name="gs_register")
 * @ORM\Entity(repositoryClass="App\Repository\RegisterRepository")
 * @JMS\XmlRoot("Registro")
 */
class Register
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
     * @ORM\Column(name="codigo", type="string", length=10, nullable=true)
     * @JMS\XmlAttribute
     * @JMS\Type("string")
     */
    private $codigo;

    /**
     * @ORM\Column(name="reDeno", type="string", length=255)
     * @JMS\Type("string")
     */
    private $reDeno;

    /**
     * @ORM\Column(name="rePad", type="string", length=6, nullable=true)
     * @JMS\Type("string")
     */
    private $rePad;

    /**
     * @ORM\Column(name="reTipo", type="integer", length=3)
     * @JMS\Type("integer")
     */
    private $reTipo;


    /**
     * @ORM\Column(name="reAct", type="string", length=50, nullable=true)
     * @JMS\Type("string")
     */
    private $reAct;

    /**
     * @ORM\Column(name="rlInfo", type="string", length=20, nullable=true)
     */
    private $rlInfo;


    /**
     * @ORM\Column(name="rlTable", type="string", length=20, nullable=true)
     */
    private $rlTable;


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


    public function __construct($codigo, $reDeno, $reTipo, $rePad, $reAct)
    {
        $this->codigo = utf8_encode(utf8_encode($codigo));
        $this->reDeno = utf8_encode(utf8_encode($reDeno));
        $this->reTipo = utf8_encode(utf8_encode($reTipo));
        $this->rePad = utf8_encode(utf8_encode($rePad));
        $this->reAct = utf8_encode(utf8_encode($reAct));
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
     * @return Register
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
     * Set reDeno
     *
     * @param string $reDeno
     * @return Register
     */
    public function setReDeno($reDeno)
    {
        $this->reDeno = $reDeno;

        return $this;
    }

    /**
     * Get reDeno
     *
     * @return string
     */
    public function getReDeno()
    {
        return $this->reDeno;
    }

    /**
     * Set reTipo
     *
     * @param integer $reTipo
     * @return Register
     */
    public function setReTipo($reTipo)
    {
        $this->reTipo = $reTipo;

        return $this;
    }

    /**
     * Get reTipo
     *
     * @return integer
     */
    public function getReTipo()
    {
        return $this->reTipo;
    }

    /**
     * Set rePad
     *
     * @param string $rePad
     * @return Register
     */
    public function setRePad($rePad)
    {
        $this->rePad = $rePad;

        return $this;
    }

    /**
     * Get rePad
     *
     * @return string
     */
    public function getRePad()
    {
        return $this->rePad;
    }

    /**
     * Set reAct
     *
     * @param string $reAct
     * @return Register
     */
    public function setReAct($reAct)
    {
        $this->reAct = $reAct;

        return $this;
    }

    /**
     * Get reAct
     *
     * @return string
     */
    public function getReAct()
    {
        return $this->reAct;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Register
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
     * @return Register
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
     * Set rlInfo
     *
     * @param string $rlInfo
     * @return Register
     */
    public function setRlInfo($rlInfo)
    {
        $this->rlInfo = $rlInfo;

        return $this;
    }

    /**
     * Get rlInfo
     *
     * @return string
     */
    public function getRlInfo()
    {
        return $this->rlInfo;
    }

    /**
     * Set rlTable
     *
     * @param string $rlTable
     * @return Register
     */
    public function setRlTable($rlTable)
    {
        $this->rlTable = $rlTable;

        return $this;
    }

    /**
     * Get rlTable
     *
     * @return string
     */
    public function getRlTable()
    {
        return $this->rlTable;
    }
}
