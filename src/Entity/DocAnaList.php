<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 4/02/16
 * Time: 13:18
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @JMS\XmlRoot("Registro")
 */
class DocAnaList
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
     * @ORM\Column(name="anFec", type="datetime", nullable=true)
     * @JMS\Type("DateTime<'d/m/Y', 'Europe/Madrid'>")
     */
    private $anFec;

    /**
     * @ORM\Column(name="anOpe", type="string", nullable=true)
     * @JMS\Type("string")
     */
    private $anOpe;
    /**
     * @ORM\Column(name="anEst", type="string", nullable=true)
     * @JMS\Type("string")
     */
    private $anEst;

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
     * @return DocAnaList
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
     * Set anFec
     *
     * @param \DateTime $anFec
     * @return DocAnaList
     */
    public function setAnFec($anFec)
    {
        $this->anFec = $anFec;

        return $this;
    }

    /**
     * Get anFec
     *
     * @return \DateTime
     */
    public function getAnFec()
    {
        return $this->anFec;
    }

    /**
     * Set anOpe
     *
     * @param string $anOpe
     * @return DocAnaList
     */
    public function setAnOpe($anOpe)
    {
        $this->anOpe = $anOpe;

        return $this;
    }

    /**
     * Get anOpe
     *
     * @return string
     */
    public function getAnOpe()
    {
        return $this->anOpe;
    }

    /**
     * Set anEst
     *
     * @param string $anEst
     * @return DocAnaList
     */
    public function setAnEst($anEst)
    {
        $this->anEst = $anEst;

        return $this;
    }

    /**
     * Get anEst
     *
     * @return string
     */
    public function getAnEst()
    {
        return $this->anEst;
    }
}
