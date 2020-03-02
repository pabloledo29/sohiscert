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
 * @ORM\Table(name="gs_productos")
 * @ORM\Entity(repositoryClass="App\Repository\ProductosRepository")
 * @JMS\XmlRoot("Registro")
 */
class Productos
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
     * @ORM\Column(name="ptDeno", type="string", length=255)
     * @JMS\Type("string")
     */
    private $ptDeno;

    /**
     * @ORM\Column(name="ptEst", type="string", length=3, nullable=true)
     */
    private $ptEst;

    /**
     * @ORM\Column(name="ptTc", type="string", length=10, nullable=true)
     * @JMS\Type("string")
     */
    private $ptTc;

    /**
     * @ORM\ManyToOne(targetEntity="TiposCultivos")
     * @ORM\JoinColumn(name="tiposcultivos_id", referencedColumnName="id", nullable=true)
     */
    private $ptTipoCultivo;

    /**
     * @ORM\Column(name="ptCu", type="string", length=10, nullable=true)
     * @JMS\Type("string")
     */
    private $ptCu;

    /**
     * @ORM\ManyToOne(targetEntity="Cultivos")
     * @ORM\JoinColumn(name="cultivos_id", referencedColumnName="id", nullable=true)
     */
    private $ptCultivos;


    /**
     * @ORM\Column(name="ptTi", type="string", length=10, nullable=true)
     * @JMS\Type("string")
     */
    private $ptTi;

    /**
     * @ORM\ManyToOne(targetEntity="TiposProducto")
     * @ORM\JoinColumn(name="tiposproducto_id", referencedColumnName="id", nullable=true)
     */
    private $ptTiposProducto;

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
     * @return Productos
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
     * Set ptDeno
     *
     * @param string $ptDeno
     * @return Productos
     */
    public function setPtDeno($ptDeno)
    {
        $this->ptDeno = $ptDeno;

        return $this;
    }

    /**
     * Get ptDeno
     *
     * @return string
     */
    public function getPtDeno()
    {
        return $this->ptDeno;
    }

    /**
     * Set ptTc
     *
     * @param string $ptTc
     * @return Productos
     */
    public function setPtTc($ptTc)
    {
        $this->ptTc = $ptTc;

        return $this;
    }

    /**
     * Get ptTc
     *
     * @return string
     */
    public function getPtTc()
    {
        return $this->ptTc;
    }

    /**
     * Set ptCu
     *
     * @param string $ptCu
     * @return Productos
     */
    public function setPtCu($ptCu)
    {
        $this->ptCu = $ptCu;

        return $this;
    }

    /**
     * Get ptCu
     *
     * @return string
     */
    public function getPtCu()
    {
        return $this->ptCu;
    }

    /**
     * Set ptTi
     *
     * @param string $ptTi
     * @return Productos
     */
    public function setPtTi($ptTi)
    {
        $this->ptTi = $ptTi;

        return $this;
    }

    /**
     * Get ptTi
     *
     * @return string
     */
    public function getPtTi()
    {
        return $this->ptTi;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Productos
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
     * @return Productos
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
     * Set ptTipoCultivo
     *
     * @param \App\Entity\TiposCultivos $ptTipoCultivo
     * @return Productos
     */
    public function setPtTipoCultivo(TiposCultivos $ptTipoCultivo = null)
    {
        $this->ptTipoCultivo = $ptTipoCultivo;

        return $this;
    }

    /**
     * Get ptTipoCultivo
     *
     * @return \App\Entity\TiposCultivos
     */
    public function getPtTipoCultivo()
    {
        return $this->ptTipoCultivo;
    }

    /**
     * Set ptCultivos
     *
     * @param \App\Entity\Cultivos $ptCultivos
     * @return Productos
     */
    public function setPtCultivos(Cultivos $ptCultivos = null)
    {
        $this->ptCultivos = $ptCultivos;

        return $this;
    }

    /**
     * Get ptCultivos
     *
     * @return \App\Entity\Cultivos
     */
    public function getPtCultivos()
    {
        return $this->ptCultivos;
    }

    /**
     * Set ptTiposProducto
     *
     * @param \App\Entity\TiposProducto $ptTiposProducto
     * @return Productos
     */
    public function setPtTiposProducto(TiposProducto $ptTiposProducto = null)
    {
        $this->ptTiposProducto = $ptTiposProducto;

        return $this;
    }

    /**
     * Get ptTiposProducto
     *
     * @return \App\Entity\TiposProducto
     */
    public function getPtTiposProducto()
    {
        return $this->ptTiposProducto;
    }

    /**
     * Set ptEst
     *
     * @param string $ptEst
     * @return Productos
     */
    public function setPtEst($ptEst)
    {
        $this->ptEst = $ptEst;

        return $this;
    }

    /**
     * Get ptEst
     *
     * @return string
     */
    public function getPtEst()
    {
        return $this->ptEst;
    }
}
