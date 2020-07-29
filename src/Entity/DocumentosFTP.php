<?php
/**
 * Copyright (c) 2018.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\DocumentFTPRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="documentosFTP")
 */
class DocumentosFTP
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
     * @var string
     *
     * @ORM\Column(name="opCdp", type="string", length=6, nullable=true)
     * @JMS\Type("string")
     */
    private $opCdp;

    /**
     * @var string
     *
     * @ORM\Column(name="opNop", type="string", length=40, nullable=true, options={"default" = "-"})
     * @JMS\Type("string")
     */
    private $opNop;

    /**
     * @var string
     *
     * @ORM\Column(name="tipoDoc", type="string", length=255, nullable=true)
     * @JMS\Type("string")
     */
    private $tipoDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="nbDoc", type="string", length=255, nullable=true)
     * @JMS\Type("string"))
     */
    private $nbDoc;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaDoc", type="datetime", nullable=true)
     */
    private $fechaDoc;

    /**
     * @var int
     *
     * @ORM\Column(name="visitas", type="integer", nullable=false, options={"unsigned":true, "default":0})
     */
    private $visitas = 0;
    /*
    public function __construct(
        $opCdp,
        $opNop,
        $tipoDoc,
        $nbDoc,
        $fechaDoc
    )
    {
        $this->opCdp = $opCdp;
        $this->opNop = $opNop;
        $this->tipoDoc = $tipoDoc;
        $this->nbDoc = $nbDoc;
        $this->fechaDoc = $fechaDoc;
    }
    */

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
     * Set opCdp
     *
     * @param string $opCdp
     * @return DocumentosFTP
     */
    public function setOpCdp($opCdp)
    {
        $this->opCdp = $opCdp;

        return $this;
    }

    /**
     * Get opCdp
     *
     * @return string 
     */
    public function getOpCdp()
    {
        return $this->opCdp;
    }

    /**
     * Set opNop
     *
     * @param string $opNop
     * @return DocumentosFTP
     */
    public function setOpNop($opNop)
    {
        $this->opNop = $opNop;

        return $this;
    }

    /**
     * Get opNop
     *
     * @return string 
     */
    public function getOpNop()
    {
        return $this->opNop;
    }

    /**
     * Set tipoDoc
     *
     * @param string $tipoDoc
     * @return DocumentosFTP
     */
    public function setTipoDoc($tipoDoc)
    {
        $this->tipoDoc = $tipoDoc;

        return $this;
    }

    /**
     * Get tipoDoc
     *
     * @return string 
     */
    public function getTipoDoc()
    {
        return $this->tipoDoc;
    }

    /**
     * Set nbDoc
     *
     * @param string $nbDoc
     * @return DocumentosFTP
     */
    public function setNbDoc($nbDoc)
    {
        $this->nbDoc = $nbDoc;

        return $this;
    }

    /**
     * Get nbDoc
     *
     * @return string 
     */
    public function getNbDoc()
    {
        return $this->nbDoc;
    }

    /**
     * Set fechaDoc
     *
     * @param \DateTime $fechaDoc
     * @return DocumentosFTP
     */
    public function setFechaDoc($fechaDoc)
    {
        $this->fechaDoc = $fechaDoc;

        return $this;
    }

     /**
     * Set fechaDoc
     *
     * @param \DateTime $fechaDoc
     * @return DocumentosFTP
     */
    public function getFechaDoc()
    {
        return $this->fechaDoc;

    }


    /**
     * Set visitas
     *
     * @param int $visitas
     * @return DocumentosFTP
     */
    public function setVisitas(int $visitas)
    {
        $this->visitas = $visitas;

        return $this;
    }

    /**
     * Get fechaDoc
     *
     * @return int
     */
    public function getVisitas()
    {
        return $this->visitas;
    }
}
