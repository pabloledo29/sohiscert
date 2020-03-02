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
 * @ORM\Table(name="gs_operator")
 * @ORM\Entity(repositoryClass="App\Repository\OperatorRepository")
 * @JMS\XmlRoot("Registro")
 */
class Operator
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
     * @ORM\Column(name="opDenoop", type="string", length=255)
     * @JMS\Type("string")
     */
    private $opDenoop;

    /**
     * @ORM\Column(name="opCif", type="string", length=20)
     * @JMS\Type("string")
     */
    private $opCif;

    /**
     * @ORM\Column(name="opCdp", type="string", length=6, nullable=true)
     * @JMS\Type("string")
     */
    private $opCdp;

    /**
     * @ORM\Column(name="opDomop", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $opDomop;

    /**
     * @ORM\Column(name="opTel", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $opTel;

    /**
     * @ORM\Column(name="opCcl", type="string", length=5, nullable=true)
     * @JMS\Type("string")
     */
    private $opCcl;

    /**
     * @ORM\Column(name="opEst", type="string", length=3, nullable=true)
     * @JMS\Type("string")
     */
    private $opEst;

    /**
     * @ORM\Column(name="opTpex", type="string", length=3, nullable=true)
     * @JMS\Type("string")
     */
    private $opTpex;

    /**
     *
     * @ORM\Column(name="opReg", type="string", length=10, nullable=true)
     * @JMS\Type("string")
     */
    private $opReg;

    /**
     * @ORM\ManyToOne(targetEntity="Register")
     * @ORM\JoinColumn(name="register_id", referencedColumnName="id", nullable=true)
     *
     */
    private $opRegistro;

    /**
     *
     * @ORM\Column(name="opSreg", type="string", length=10, nullable=true)
     * @JMS\Type("string")
     */
    private $opSreg;

    /**
     * @ORM\ManyToOne(targetEntity="Register")
     * @ORM\JoinColumn(name="subregister_id", referencedColumnName="id", nullable=true)
     *
     */
    private $opSubregistro;

    /**
     * @ORM\Column(name="opNam", type="integer", length=2, nullable=true, options={"default" = 1})
     * @JMS\Type("integer")
     */
    private $opNam;
    /**
     * @ORM\Column(name="opNop", type="string", length=40, nullable=true, options={"default" = "-"})
     * @JMS\Type("string")
     */
    private $opNop;

    /**
     * @ORM\Column(name="opAct", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $opAct;

    /**
     * @ORM\Column(name="opGgn", type="bigint", nullable=true, length=35)
     * @JMS\Type("integer")
     */
    private $opGgn;

    /**
     * @ORM\Column(name="opNrgap", type="bigint", nullable=true, length=35)
     * @JMS\Type("integer")
     */
    private $opNrgap;

    /**
     * @ORM\Column(name="opPvcl", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $opPvcl;

    /**
     * @ORM\Column(name="opPbcl", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $opPbcl;

    /**
     * @ORM\Column(name="opTecdeno", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $opTecdeno;

    /**
     * @ORM\Column(name="opFaud", type="string", nullable=true)
     * @JMS\Type("string")
     */
    private $opFaud;

    /**
     * @ORM\Column(name="opEma", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $opEma;

    /**
     * @ORM\Column(name="opTecema", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $opTecema;

    /**
     * @ORM\Column(name="opClp", type="string", length=5, nullable=true)
     * @JMS\Type("string")
     */
    private $opClp;

    /**
     * @ORM\Column(name="opEntity", type="string", length=20, nullable=true)
     */
    private $opEntity;

    /**
     * @ORM\OneToMany(targetEntity="CultivosRec", mappedBy="ruOperator", cascade={"all"})
     */
    private $opCultivosRec;

    /**
     * @ORM\OneToMany(targetEntity="CultivosRec2", mappedBy="ru2Operator", cascade={"all"})
     */
    private $opCultivosRec2;

    /**
     * @ORM\OneToMany(targetEntity="ProductosPae", mappedBy="pipOperator", cascade={"all"})
     *
     */
    private $opProductosPae;

    /**
     * @ORM\OneToMany(targetEntity="ProductosIndus", mappedBy="piOperator", cascade={"all"})
     */
    private $opProductosIndus;

    /**
     * @ORM\OneToMany(targetEntity="Ganaderias", mappedBy="gnOperator", cascade={"all"})
     */
    private $opGanaderias;

    /**
     * @ORM\OneToMany(targetEntity="AvesCorral", mappedBy="avcOperator", cascade={"all"})
     */
    private $opAvesCorral;

    /**
     * @ORM\OneToMany(targetEntity="IAvesCorral", mappedBy="aviOperator", cascade={"all"})
     *
     */
    private $opIAvesCorral;

    /**
     * @ORM\OneToMany(targetEntity="Industrias", mappedBy="inOperator", cascade={"all"})
     */
    private $opIndustrias;

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

    public function __construct(
        $codigo,
        $opDenoop,
        $opCif,
        $opCdp,
        $opCcl,
        $opReg,
        $opSreg,
        $opNop,
        $opNam,
        $opAct,
        $opPvcl,
        $opPbcl,
        $opTecdeno,
        $opFaud,
        $opEma
    )
    {
        $this->codigo = $codigo;
        $this->opDenoop = $opDenoop;
        $this->opCif = $opCif;
        $this->opCdp = $opCdp;
        $this->opCcl = $opCcl;
        $this->opReg = $opReg;
        $this->opSreg = $opSreg;
        $this->opNop = $opNop;
        $this->opNam = $opNam;
        $this->opAct = $opAct;
        $this->opPvcl = $opPvcl;
        $this->opPbcl = $opPbcl;
        $this->opTecdeno = $opTecdeno;
        $this->opFaud = $opFaud;
        $this->opEma = $opEma;
        $this->opProductosPae = new ArrayCollection();
        $this->opProductosIndus = new ArrayCollection();
        $this->opCultivosRec = new ArrayCollection();
        $this->opCultivosRec2 = new ArrayCollection();
        $this->opAvesCorral = new ArrayCollection();
        $this->opGanaderias = new ArrayCollection();
        $this->opIAvesCorral = new ArrayCollection();
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
     * @return Operator
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
     * Set opDenoop
     *
     * @param string $opDenoop
     * @return Operator
     */
    public function setOpDenoop($opDenoop)
    {
        $this->opDenoop = $opDenoop;

        return $this;
    }

    /**
     * Get opDenoop
     *
     * @return string
     */
    public function getOpDenoop()
    {
        return $this->opDenoop;
    }

    /**
     * Set opCif
     *
     * @param string $opCif
     * @return Operator
     */
    public function setOpCif($opCif)
    {
        $this->opCif = $opCif;

        return $this;
    }

    /**
     * Get opCif
     *
     * @return string
     */
    public function getOpCif()
    {
        return $this->opCif;
    }

    /**
     * Set opCdp
     *
     * @param string $opCdp
     * @return Operator
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
     * Set opDomop
     *
     * @param string $opDomop
     * @return Operator
     */
    public function setOpDomop($opDomop)
    {
        $this->opDomop = $opDomop;

        return $this;
    }

    /**
     * Get opDomop
     *
     * @return string
     */
    public function getOpDomop()
    {
        return $this->opDomop;
    }

    /**
     * Set opTel
     *
     * @param string $opTel
     * @return Operator
     */
    public function setOpTel($opTel)
    {
        $this->opTel = $opTel;

        return $this;
    }

    /**
     * Get opTel
     *
     * @return string
     */
    public function getOpTel()
    {
        return $this->opTel;
    }

    /**
     * Set opCcl
     *
     * @param string $opCcl
     * @return Operator
     */
    public function setOpCcl($opCcl)
    {
        $this->opCcl = $opCcl;

        return $this;
    }

    /**
     * Get opCcl
     *
     * @return string
     */
    public function getOpCcl()
    {
        return $this->opCcl;
    }

    /**
     * Set opEst
     *
     * @param string $opEst
     * @return Operator
     */
    public function setOpEst($opEst)
    {
        $this->opEst = $opEst;

        return $this;
    }

    /**
     * Get opEst
     *
     * @return string
     */
    public function getOpEst()
    {
        return $this->opEst;
    }

    /**
     * Set opTpex
     *
     * @param string $opTpex
     * @return Operator
     */
    public function setOpTpex($opTpex)
    {
        $this->opTpex = $opTpex;

        return $this;
    }

    /**
     * Get opTpex
     *
     * @return string
     */
    public function getOpTpex()
    {
        return $this->opTpex;
    }

    /**
     * Set opReg
     *
     * @param string $opReg
     * @return Operator
     */
    public function setOpReg($opReg)
    {
        $this->opReg = $opReg;

        return $this;
    }

    /**
     * Get opReg
     *
     * @return string
     */
    public function getOpReg()
    {
        return $this->opReg;
    }

    /**
     * Set opSreg
     *
     * @param string $opSreg
     * @return Operator
     */
    public function setOpSreg($opSreg)
    {
        $this->opSreg = $opSreg;

        return $this;
    }

    /**
     * Get opSreg
     *
     * @return string
     */
    public function getOpSreg()
    {
        return $this->opSreg;
    }

    /**
     * Set opNam
     *
     * @param integer $opNam
     * @return Operator
     */
    public function setOpNam($opNam)
    {
        $this->opNam = $opNam;

        return $this;
    }

    /**
     * Get opNam
     *
     * @return integer
     */
    public function getOpNam()
    {
        return $this->opNam;
    }

    /**
     * Set opNop
     *
     * @param string $opNop
     * @return Operator
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
     * Set opAct
     *
     * @param string $opAct
     * @return Operator
     */
    public function setOpAct($opAct)
    {
        $this->opAct = $opAct;

        return $this;
    }

    /**
     * Get opAct
     *
     * @return string
     */
    public function getOpAct()
    {
        return $this->opAct;
    }

    /**
     * Set opGgn
     *
     * @param integer $opGgn
     * @return Operator
     */
    public function setOpGgn($opGgn)
    {
        $this->opGgn = $opGgn;

        return $this;
    }

    /**
     * Get opGgn
     *
     * @return integer
     */
    public function getOpGgn()
    {
        return $this->opGgn;
    }

    /**
     * Set opNrgap
     *
     * @param integer $opNrgap
     * @return Operator
     */
    public function setOpNrgap($opNrgap)
    {
        $this->opNrgap = $opNrgap;

        return $this;
    }

    /**
     * Get opNrgap
     *
     * @return integer
     */
    public function getOpNrgap()
    {
        return $this->opNrgap;
    }

    /**
     * Set opPvcl
     *
     * @param string $opPvcl
     * @return Operator
     */
    public function setOpPvcl($opPvcl)
    {
        $this->opPvcl = $opPvcl;

        return $this;
    }

    /**
     * Get opPvcl
     *
     * @return string
     */
    public function getOpPvcl()
    {
        return $this->opPvcl;
    }

    /**
     * Set opPbcl
     *
     * @param string $opPbcl
     * @return Operator
     */
    public function setOpPbcl($opPbcl)
    {
        $this->opPbcl = $opPbcl;

        return $this;
    }

    /**
     * Get opPbcl
     *
     * @return string
     */
    public function getOpPbcl()
    {
        return $this->opPbcl;
    }

    /**
     * Set opEntity
     *
     * @param string $opEntity
     * @return Operator
     */
    public function setOpEntity($opEntity)
    {
        $this->opEntity = $opEntity;

        return $this;
    }

    /**
     * Get opEntity
     *
     * @return string
     */
    public function getOpEntity()
    {
        return $this->opEntity;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Operator
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
     * @return Operator
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
     * Set opRegistro
     *
     * @param \App\Entity\Register $opRegistro
     * @return Operator
     */
    public function setOpRegistro(Register $opRegistro = null)
    {
        $this->opRegistro = $opRegistro;

        return $this;
    }

    /**
     * Get opRegistro
     *
     * @return \App\Entity\Register
     */
    public function getOpRegistro()
    {
        return $this->opRegistro;
    }

    /**
     * Set opSubregistro
     *
     * @param \App\Entity\Register $opSubregistro
     * @return Operator
     */
    public function setOpSubregistro(Register $opSubregistro = null)
    {
        $this->opSubregistro = $opSubregistro;

        return $this;
    }

    /**
     * Get opSubregistro
     *
     * @return \App\Entity\Register
     */
    public function getOpSubregistro()
    {
        return $this->opSubregistro;
    }

    /**
     * Add opCultivosRec
     *
     * @param \App\Entity\CultivosRec $opCultivosRec
     * @return Operator
     */
    public function addOpCultivosRec(CultivosRec $opCultivosRec)
    {
        $this->opCultivosRec[] = $opCultivosRec;

        return $this;
    }

    /**
     * Remove opCultivosRec
     *
     * @param \App\Entity\CultivosRec $opCultivosRec
     */
    public function removeOpCultivosRec(CultivosRec $opCultivosRec)
    {
        $this->opCultivosRec->removeElement($opCultivosRec);
    }

    /**
     * Get opCultivosRec
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpCultivosRec()
    {
        return $this->opCultivosRec;
    }

    /**
     * Add opCultivosRec2
     *
     * @param \App\Entity\CultivosRec2 $opCultivosRec2
     * @return Operator
     */
    public function addOpCultivosRec2(CultivosRec2 $opCultivosRec2)
    {
        $this->opCultivosRec2[] = $opCultivosRec2;

        return $this;
    }

    /**
     * Remove opCultivosRec2
     *
     * @param \App\Entity\CultivosRec2 $opCultivosRec2
     */
    public function removeOpCultivosRec2(CultivosRec2 $opCultivosRec2)
    {
        $this->opCultivosRec2->removeElement($opCultivosRec2);
    }

    /**
     * Get opCultivosRec2
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpCultivosRec2()
    {
        return $this->opCultivosRec2;
    }

    /**
     * Add opProductosPae
     *
     * @param \App\Entity\ProductosPae $opProductosPae
     * @return Operator
     */
    public function addOpProductosPae(ProductosPae $opProductosPae)
    {
        $this->opProductosPae[] = $opProductosPae;

        return $this;
    }

    /**
     * Remove opProductosPae
     *
     * @param \App\Entity\ProductosPae $opProductosPae
     */
    public function removeOpProductosPae(ProductosPae $opProductosPae)
    {
        $this->opProductosPae->removeElement($opProductosPae);
    }

    /**
     * Get opProductosPae
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpProductosPae()
    {
        return $this->opProductosPae;
    }

    /**
     * Add opProductosIndus
     *
     * @param \App\Entity\ProductosIndus $opProductosIndus
     * @return Operator
     */
    public function addOpProductosIndus(ProductosIndus $opProductosIndus)
    {
        $this->opProductosIndus[] = $opProductosIndus;

        return $this;
    }

    /**
     * Remove opProductosIndus
     *
     * @param \App\Entity\ProductosIndus $opProductosIndus
     */
    public function removeOpProductosIndus(ProductosIndus $opProductosIndus)
    {
        $this->opProductosIndus->removeElement($opProductosIndus);
    }

    /**
     * Get opProductosIndus
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpProductosIndus()
    {
        return $this->opProductosIndus;
    }

    /**
     * Add opGanaderias
     *
     * @param \App\Entity\Ganaderias $opGanaderias
     * @return Operator
     */
    public function addOpGanaderia(Ganaderias $opGanaderias)
    {
        $this->opGanaderias[] = $opGanaderias;

        return $this;
    }

    /**
     * Remove opGanaderias
     *
     * @param \App\Entity\Ganaderias $opGanaderias
     */
    public function removeOpGanaderia(Ganaderias $opGanaderias)
    {
        $this->opGanaderias->removeElement($opGanaderias);
    }

    /**
     * Get opGanaderias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpGanaderias()
    {
        return $this->opGanaderias;
    }

    /**
     * Add opAvesCorral
     *
     * @param \App\Entity\AvesCorral $opAvesCorral
     * @return Operator
     */
    public function addOpAvesCorral(AvesCorral $opAvesCorral)
    {
        $this->opAvesCorral[] = $opAvesCorral;

        return $this;
    }

    /**
     * Remove opAvesCorral
     *
     * @param \App\Entity\AvesCorral $opAvesCorral
     */
    public function removeOpAvesCorral(AvesCorral $opAvesCorral)
    {
        $this->opAvesCorral->removeElement($opAvesCorral);
    }

    /**
     * Get opAvesCorral
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpAvesCorral()
    {
        return $this->opAvesCorral;
    }

    /**
     * Add opIAvesCorral
     *
     * @param \App\Entity\IAvesCorral $opIAvesCorral
     * @return Operator
     */
    public function addOpIAvesCorral(IAvesCorral $opIAvesCorral)
    {
        $this->opIAvesCorral[] = $opIAvesCorral;

        return $this;
    }

    /**
     * Remove opIAvesCorral
     *
     * @param \App\Entity\IAvesCorral $opIAvesCorral
     */
    public function removeOpIAvesCorral(IAvesCorral $opIAvesCorral)
    {
        $this->opIAvesCorral->removeElement($opIAvesCorral);
    }

    /**
     * Get opIAvesCorral
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpIAvesCorral()
    {
        return $this->opIAvesCorral;
    }

    /**
     * Add opIndustrias
     *
     * @param \App\Entity\Industrias $opIndustrias
     * @return Operator
     */
    public function addOpIndustria(Industrias $opIndustrias)
    {
        $this->opIndustrias[] = $opIndustrias;

        return $this;
    }

    /**
     * Remove opIndustrias
     *
     * @param \App\Entity\Industrias $opIndustrias
     */
    public function removeOpIndustria(Industrias $opIndustrias)
    {
        $this->opIndustrias->removeElement($opIndustrias);
    }

    /**
     * Get opIndustrias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOpIndustrias()
    {
        return $this->opIndustrias;
    }

    /**
     * Set opTecdeno
     *
     * @param string $opTecdeno
     * @return Operator
     */
    public function setOpTecdeno($opTecdeno)
    {
        $this->opTecdeno = $opTecdeno;

        return $this;
    }

    /**
     * Get opTecdeno
     *
     * @return string
     */
    public function getOpTecdeno()
    {
        return $this->opTecdeno;
    }

    /**
     * Set opFaud
     *
     * @param string $opFaud
     * @return Operator
     */
    public function setOpFaud($opFaud)
    {
        $this->opFaud = $opFaud;

        return $this;
    }

    /**
     * Get opFaud
     *
     * @return string
     */
    public function getOpFaud()
    {
        return $this->opFaud;
    }

    /**
     * Set opEma
     *
     * @param string $opEma
     * @return Operator
     */
    public function setOpEma($opEma)
    {
        $this->opEma = $opEma;

        return $this;
    }

    /**
     * Get opEma
     *
     * @return string
     */
    public function getOpEma()
    {
        return $this->opEma;
    }

    /**
     * Set opTecema
     *
     * @param string $opTecema
     * @return Operator
     */
    public function setOpTecema($opTecema)
    {
        $this->opTecema = $opTecema;

        return $this;
    }

    /**
     * Get opTecema
     *
     * @return string
     */
    public function getOpTecema()
    {
        return $this->opTecema;
    }

    /**
     * Set opClp
     *
     * @param string $opClp
     * @return Operator
     */
    public function setOpClp($opClp)
    {
        $this->opClp = $opClp;

        return $this;
    }

    /**
     * Get opClp
     *
     * @return string
     */
    public function getOpClp()
    {
        return $this->opClp;
    }
}
