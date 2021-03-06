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
 * @ORM\Table(name="gs_client")
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 * @JMS\XmlRoot("Registro")
 */
class Client
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
     * @ORM\Column(name="clDeno", type="string", length=255)
     * @JMS\Type("string")
     */
    protected $clDeno;

    /**
     * @ORM\Column(name="opCif", type="string", length=20)
     * @JMS\Type("string")
     */
    private $clCif;

    /**
     * @ORM\Column(name="clCdp", type="integer", length=6, nullable=true)
     * @JMS\Type("integer")
     */
    private $clCdp;

    /**
     * @ORM\Column(name="clDom", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $clDom;

    /**
     * @ORM\Column(name="clProv", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $clProv;

    /**
     * @ORM\Column(name="clPob", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $clPob;

    /**
     * @ORM\Column(name="clPais", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $clPais;

    /**
     * @ORM\Column(name="clTel", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $clTel;

    /**
     * @ORM\Column(name="clFax", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $clFax;

    /**
     * @ORM\Column(name="clEma", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $clEma;

    /**
     * @ORM\Column(name="clActi", type="text", nullable=true)
     * @JMS\Type("string")
     */
    private $clActi;

    /**
     * @ORM\OneToOne(targetEntity="Contact")
     * @ORM\JoinColumn(name="contact", referencedColumnName="id", nullable=true)
     */
    private $contact;

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
        $clDeno,
        $clCif,
        $clCdp,
        $clDom,
        $clProv,
        $clPob,
        $clPais,
        $clTel,
        $clFax,
        $clEma,
        $clActi
    ) {

        $this->$codigo = $codigo;
        $this->$clDeno = $clDeno;
        $this->$clCif = $clCif;
        $this->$clCdp = $clCdp;
        $this->$clDom = $clDom;
        $this->$clProv = $clProv;
        $this->$clPob = $clPob;
        $this->$clPais = $clPais;
        $this->$clTel = $clTel;
        $this->$clFax = $clFax;
        $this->$clEma = $clEma;
        $this->$clActi = $clActi;
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
     * @return Client
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
     * Set clDeno
     *
     * @param string $clDeno
     * @return Client
     */
    public function setClDeno($clDeno)
    {
        $this->clDeno = $clDeno;

        return $this;
    }

    /**
     * Get clDeno
     *
     * @return string
     */
    public function getClDeno()
    {
        return $this->clDeno;
    }

    /**
     * Set clCif
     *
     * @param string $clCif
     * @return Client
     */
    public function setClCif($clCif)
    {
        $this->clCif = $clCif;

        return $this;
    }

    /**
     * Get clCif
     *
     * @return string
     */
    public function getClCif()
    {
        return $this->clCif;
    }

    /**
     * Set clCdp
     *
     * @param integer $clCdp
     * @return Client
     */
    public function setClCdp($clCdp)
    {
        $this->clCdp = $clCdp;

        return $this;
    }

    /**
     * Get clCdp
     *
     * @return integer
     */
    public function getClCdp()
    {
        return $this->clCdp;
    }

    /**
     * Set clDom
     *
     * @param string $clDom
     * @return Client
     */
    public function setClDom($clDom)
    {
        $this->clDom = $clDom;

        return $this;
    }

    /**
     * Get clDom
     *
     * @return string
     */
    public function getClDom()
    {
        return $this->clDom;
    }

    /**
     * Set clProv
     *
     * @param string $clProv
     * @return Client
     */
    public function setClProv($clProv)
    {
        $this->clProv = $clProv;

        return $this;
    }

    /**
     * Get clProv
     *
     * @return string
     */
    public function getClProv()
    {
        return $this->clProv;
    }

    /**
     * Set clPob
     *
     * @param string $clPob
     * @return Client
     */
    public function setClPob($clPob)
    {
        $this->clPob = $clPob;

        return $this;
    }

    /**
     * Get clPob
     *
     * @return string
     */
    public function getClPob()
    {
        return $this->clPob;
    }

    /**
     * Set clPais
     *
     * @param string $clPais
     * @return Client
     */
    public function setClPais($clPais)
    {
        $this->clPais = $clPais;

        return $this;
    }

    /**
     * Get clPais
     *
     * @return string
     */
    public function getClPais()
    {
        return $this->clPais;
    }

    /**
     * Set clTel
     *
     * @param string $clTel
     * @return Client
     */
    public function setClTel($clTel)
    {
        $this->clTel = $clTel;

        return $this;
    }

    /**
     * Get clTel
     *
     * @return string
     */
    public function getClTel()
    {
        return $this->clTel;
    }

    /**
     * Set clFax
     *
     * @param string $clFax
     * @return Client
     */
    public function setClFax($clFax)
    {
        $this->clFax = $clFax;

        return $this;
    }

    /**
     * Get clFax
     *
     * @return string
     */
    public function getClFax()
    {
        return $this->clFax;
    }

    /**
     * Set clEma
     *
     * @param string $clEma
     * @return Client
     */
    public function setClEma($clEma)
    {
        $this->clEma = $clEma;

        return $this;
    }

    /**
     * Get clEma
     *
     * @return string
     */
    public function getClEma()
    {
        return $this->clEma;
    }

    /**
     * Set clActi
     *
     * @param string $clActi
     * @return Client
     */
    public function setClActi($clActi)
    {
        $this->clActi = $clActi;

        return $this;
    }

    /**
     * Get clActi
     *
     * @return string
     */
    public function getClActi()
    {
        return $this->clActi;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Client
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
     * @return Client
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
     * Add contact
     *
     * @param \App\Entity\Contact $contact
     * @return Client
     */
    public function addContact(Contact $contact)
    {
        $this->contact[] = $contact;

        return $this;
    }

    /**
     * Remove contact
     *
     * @param \App\Entity\Contact $contact
     */
    public function removeContact(Contact $contact)
    {
        $this->contact->removeElement($contact);
    }

    /**
     * Get contact
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set contact
     *
     * @param \App\Entity\Contact $contact
     * @return Client
     */
    public function setContact(Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    public function getUserAdmin(): ?UserOperator
    {
        return $this->userOperator;
    }

    public function setUserAdmin(?UserOperator $userOperator): self
    {
        $this->userOperator = $userOperator;

        // set (or unset) the owning side of the relation if necessary
        $newClient_id = null === $userOperator ? null : $this;
        if ($userOperator->getClientId() !== $newClient_id) {
            $userOperator->setClientId($newClient_id);
        }

        return $this;
    }
}
