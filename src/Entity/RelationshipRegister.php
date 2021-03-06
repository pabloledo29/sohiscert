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
 * @ORM\Entity(repositoryClass="App\Repository\RelationshipRegisterRepository")
 * @ORM\Table(name="relationshipregister")
 */
class RelationshipRegister
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
     * @ORM\Column(name="rlReg", type="string", length=10, nullable=true)
     */
    private $rlReg;

    /**
     * @ORM\Column(name="rlSubreg", type="string", length=10, nullable=true)
     */
    private $rlSreg;

    /**
     * @ORM\Column(name="rlSregDeno", type="string", length=255, nullable=true)
     */
    private $rlSregDeno;

    /**
     * @ORM\Column(name="rlInfo", type="string", length=20, nullable=true)
     */
    private $rlInfo;

    /**
     * @ORM\Column(name="rlEntity", type="string", length=20, nullable=true)
     */
    private $rlEntity;

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

    public function __construct($rlSreg, $rlSregDeno, $rlReg, $rlInfo, $rlEntity)
    {
        $this->rlSreg = $rlSreg;
        $this->rlSregDeno = $rlSregDeno;
        $this->rlReg = $rlReg;
        $this->rlSregDeno = $rlSregDeno;
        $this->rlInfo = $rlInfo;
        $this->rlEntity = $rlEntity;
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
     * Set rlReg
     *
     * @param string $rlReg
     * @return RelationshipRegister
     */
    public function setRlReg($rlReg)
    {
        $this->rlReg = $rlReg;

        return $this;
    }

    /**
     * Get rlReg
     *
     * @return string
     */
    public function getRlReg()
    {
        return $this->rlReg;
    }

    /**
     * Set rlSreg
     *
     * @param string $rlSreg
     * @return RelationshipRegister
     */
    public function setRlSreg($rlSreg)
    {
        $this->rlSreg = $rlSreg;

        return $this;
    }

    /**
     * Get rlSreg
     *
     * @return string
     */
    public function getRlSreg()
    {
        return $this->rlSreg;
    }

    /**
     * Set rlSregDeno
     *
     * @param string $rlSregDeno
     * @return RelationshipRegister
     */
    public function setRlSregDeno($rlSregDeno)
    {
        $this->rlSregDeno = $rlSregDeno;

        return $this;
    }

    /**
     * Get rlSregDeno
     *
     * @return string
     */
    public function getRlSregDeno()
    {
        return $this->rlSregDeno;
    }

    /**
     * Set rlInfo
     *
     * @param string $rlInfo
     * @return RelationshipRegister
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
     * Set rlEntity
     *
     * @param string $rlEntity
     * @return RelationshipRegister
     */
    public function setRlEntity($rlEntity)
    {
        $this->rlEntity = $rlEntity;

        return $this;
    }

    /**
     * Get rlEntity
     *
     * @return string
     */
    public function getRlEntity()
    {
        return $this->rlEntity;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return RelationshipRegister
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
     * @return RelationshipRegister
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
