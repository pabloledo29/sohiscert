<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 5/02/16
 * Time: 11:45
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @JMS\XmlRoot("Registro")
 */
class DocAnaDoc
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
     * @ORM\Column(name="danFec", type="string", nullable=true)
     * @JMS\Type("string")
     */
    private $danFec;

    /**
     * @ORM\Column(name="danAna", type="string", nullable=true)
     * @JMS\Type("string")
     */
    private $danAna;

    /**
     * @ORM\Column(name="danTxt", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $danTxt;

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
     * @return DocAnaDoc
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
     * Set danFec
     *
     * @param string $danFec
     * @return DocAnaDoc
     */
    public function setDanFec($danFec)
    {
        $this->danFec = $danFec;

        return $this;
    }

    /**
     * Get danFec
     *
     * @return string
     */
    public function getDanFec()
    {
        return $this->danFec;
    }

    /**
     * Set danAna
     *
     * @param string $danAna
     * @return DocAnaDoc
     */
    public function setDanAna($danAna)
    {
        $this->danAna = $danAna;

        return $this;
    }

    /**
     * Get danAna
     *
     * @return string
     */
    public function getDanAna()
    {
        return $this->danAna;
    }

    /**
     * Set danTxt
     *
     * @param string $danTxt
     * @return DocAnaDoc
     */
    public function setDanTxt($danTxt)
    {
        $this->danTxt = $danTxt;

        return $this;
    }

    /**
     * Get danTxt
     *
     * @return string
     */
    public function getDanTxt()
    {
        return $this->danTxt;
    }
}
