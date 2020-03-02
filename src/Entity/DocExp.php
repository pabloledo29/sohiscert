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
 * @ORM\Entity(repositoryClass="App\Repository\DocExpRepository")
 * @JMS\XmlRoot("Registro")
 */
class DocExp
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
     * @ORM\Column(name="deDeno", type="string")
     * @JMS\Type("string")
     */
    private $deDeno;

    /**
     * @ORM\Column(name="deCcl", type="integer", length=6, nullable=true)
     * @JMS\Type("string")
     */
    private $deCcl;


    /**
     * @ORM\Column(name="dePla", type="string", length=3, nullable=true)
     * @JMs\Type("string")
     */
    private $dePla;

    /**
     * @ORM\Column(name="deFec", type="string", nullable=true )
     * @JMs\Type("string")
     */
    private $deFec;

    /**
     * @ORM\Column(name="deTxt", type="text", nullable=true)
     * @JMs\Type("string")
     */
    private $deTxt;

    /**
     * @ORM\Column(name="deOpe", type="text", nullable=true)
     * @JMs\Type("string")
     */
    private $deOpe;

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
     * @return DocExp
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
     * Set deDeno
     *
     * @param string $deDeno
     * @return DocExp
     */
    public function setDeDeno($deDeno)
    {
        $this->deDeno = $deDeno;

        return $this;
    }

    /**
     * Get deDeno
     *
     * @return string
     */
    public function getDeDeno()
    {
        return $this->deDeno;
    }

    /**
     * Set deCcl
     *
     * @param integer $deCcl
     * @return DocExp
     */
    public function setDeCcl($deCcl)
    {
        $this->deCcl = $deCcl;

        return $this;
    }

    /**
     * Get deCcl
     *
     * @return integer
     */
    public function getDeCcl()
    {
        return $this->deCcl;
    }

    /**
     * Set dePla
     *
     * @param string $dePla
     * @return DocExp
     */
    public function setDePla($dePla)
    {
        $this->dePla = $dePla;

        return $this;
    }

    /**
     * Get dePla
     *
     * @return string
     */
    public function getDePla()
    {
        return $this->dePla;
    }

    /**
     * Set deFec
     *
     * @param string $deFec
     * @return DocExp
     */
    public function setDeFec($deFec)
    {
        $this->deFec = $deFec;

        return $this;
    }

    /**
     * Get deFec
     *
     * @return string
     */
    public function getDeFec()
    {
        return $this->deFec;
    }

    /**
     * Set deTxt
     *
     * @param string $deTxt
     * @return DocExp
     */
    public function setDeTxt($deTxt)
    {
        $this->deTxt = $deTxt;

        return $this;
    }

    /**
     * Get deTxt
     *
     * @return string
     */
    public function getDeTxt()
    {
        return $this->deTxt;
    }

    /**
     * Set deOpe
     *
     * @param string $deOpe
     * @return DocExp
     */
    public function setDeOpe($deOpe)
    {
        $this->deOpe = $deOpe;

        return $this;
    }

    /**
     * Get deOpe
     *
     * @return string
     */
    public function getDeOpe()
    {
        return $this->deOpe;
    }
}
