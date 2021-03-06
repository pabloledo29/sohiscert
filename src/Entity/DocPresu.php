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
 * @ORM\Entity(repositoryClass="App\Repository\DocPresuRepository")
 * @JMS\XmlRoot("Registro")
 */
class DocPresu
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
     * @ORM\Column(name="ddcDeno", type="string")
     * @JMS\Type("string")
     */
    private $ddcDeno;

    /**
     * @ORM\Column(name="ddcCcl", type="integer", length=6, nullable=true)
     * @JMS\Type("string")
     */
    private $ddcCcl;

    /**
     * @ORM\Column(name="ddcEst", type="string", length=3, nullable=true)
     * @JMs\Type("string")
     */
    private $ddcEst;

    /**
     * @ORM\Column(name="ddcFec", type="string", nullable=true )
     * @JMs\Type("string")
     */
    private $ddcFec;

    /**
     * @ORM\Column(name="ddcTxt", type="text", nullable=true)
     * @JMs\Type("string")
     */
    private $ddcTxt;

    /**
     * @ORM\Column(name="ddcCif", type="text", nullable=true)
     * @JMs\Type("string")
     */
    private $ddcCif;

    /**
     * @ORM\Column(name="ddcPre0", type="text", nullable=true)
     * @JMs\Type("string")
     */
    private $ddcPre0;


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
     * @return DocPresu
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
     * Set ddcDeno
     *
     * @param string $ddcDeno
     * @return DocPresu
     */
    public function setDdcDeno($ddcDeno)
    {
        $this->ddcDeno = $ddcDeno;

        return $this;
    }

    /**
     * Get ddcDeno
     *
     * @return string
     */
    public function getDdcDeno()
    {
        return $this->ddcDeno;
    }

    /**
     * Set ddcCcl
     *
     * @param integer $ddcCcl
     * @return DocPresu
     */
    public function setDdcCcl($ddcCcl)
    {
        $this->ddcCcl = $ddcCcl;

        return $this;
    }

    /**
     * Get ddcCcl
     *
     * @return integer
     */
    public function getDdcCcl()
    {
        return $this->ddcCcl;
    }

    /**
     * Set ddcEst
     *
     * @param string $ddcEst
     * @return DocPresu
     */
    public function setDdcEst($ddcEst)
    {
        $this->ddcEst = $ddcEst;

        return $this;
    }

    /**
     * Get ddcEst
     *
     * @return string
     */
    public function getDdcEst()
    {
        return $this->ddcEst;
    }

    /**
     * Set ddcTxt
     *
     * @param string $ddcTxt
     * @return DocPresu
     */
    public function setDdcTxt($ddcTxt)
    {
        $this->ddcTxt = $ddcTxt;

        return $this;
    }

    /**
     * Get ddcTxt
     *
     * @return string
     */
    public function getDdcTxt()
    {
        return $this->ddcTxt;
    }

    /**
     * Set ddcFec
     *
     * @param string $ddcFec
     * @return DocPresu
     */
    public function setDdcFec($ddcFec)
    {
        $this->ddcFec = $ddcFec;

        return $this;
    }

    /**
     * Get ddcFec
     *
     * @return string
     */
    public function getDdcFec()
    {
        return $this->ddcFec;
    }

    /**
     * Set ddcCif
     *
     * @param string $ddcCif
     * @return DocPresu
     */
    public function setDdcCif($ddcCif)
    {
        $this->ddcCif = $ddcCif;

        return $this;
    }

    /**
     * Get ddcCif
     *
     * @return string
     */
    public function getDdcCif()
    {
        return $this->ddcCif;
    }

    /**
     * Set ddcPre0
     *
     * @param string $ddcPre0
     * @return DocPresu
     */
    public function setDdcPre0($ddcPre0)
    {
        $this->ddcPre0 = $ddcPre0;

        return $this;
    }

    /**
     * Get ddcPre0
     *
     * @return string
     */
    public function getDdcPre0()
    {
        return $this->ddcPre0;
    }
}
