<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

/**
 * Created by PhpStorm.
 * User: Eduardo.Facenda
 * Date: 14/12/2015
 * Time: 9:55
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="comunicationsendregistry")
 * @ORM\Entity(repositoryClass="App\Repository\ComunicationSendRegistryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ComunicationSendRegistry
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserOperator")
     * @ORM\JoinColumn(name="useroperator_id", referencedColumnName="id")
     */
    protected $userOperator;

    /**
     * @var string
     * @ORM\Column(name="opnop", type="string")
     */
    private $opNop;

    /**
     * @var string
     * @ORM\Column(name="sendtype", type="string")
     */
    private $sendtype;

    /**
     * @var string
     * @ORM\Column(name="subject", type="string")
     */
    private $subject;

    /**
     * @var string
     * @ORM\Column(name="cuerpo", type="text", nullable=true)
     */
    private $cuerpo;

    /**
     * @var string
     * @ORM\Column(name="destino", type="string")
     */
    private $destino;

    /**
     * @var int
     * @ORM\Column(name="visitas", type="integer", nullable=false, options={"unsigned":true, "default":0})
     */
    private $visitas;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate",type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedDateValue()
    {
        $this->createdDate = new \DateTime(date('Y-m-d H:i:s'));

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
     * Set opNop
     *
     * @param string $opNop
     * @return ComunicationSendRegistry
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
     * Set sendtype
     *
     * @param string $sendtype
     * @return ComunicationSendRegistry
     */
    public function setSendtype($sendtype)
    {
        $this->sendtype = $sendtype;

        return $this;
    }

    /**
     * Get sendtype
     *
     * @return string
     */
    public function getSendtype()
    {
        return $this->sendtype;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return ComunicationSendRegistry
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }
    
    /**
     * Set cuerpo
     *
     * @param string $cuerpo
     * @return ComunicationSendRegistry
     */
    public function setCuerpo($cuerpo)
    {
        $this->cuerpo = $cuerpo;

        return $this;
    }

    /**
     * Get cuerpo
     *
     * @return string
     */
    public function getCuerpo()
    {
        return $this->cuerpo;
    }

  
    /**
     * Get destino
     *
     * @return string
     */
    public function getDestino()
    {
        return $this->destino;
    }

    /**
     * Set destino
     *
     * @param string $destino
     * @return ComunicationSendRegistry
     */
    public function setDestino($destino)
    {
        $this->destino = $destino;

        return $this;
    }



    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return ComunicationSendRegistry
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }


    /**
     * Get visitas
     *
     * @return int
     */
    public function getVisitas()
    {
        return $this->visitas;
    }

    /**
     * Set createdDate
     *
     * @param int $visitas
     * @return ComunicationSendRegistry
     */
    public function setVisitas($visitas)
    {
        $this->visitas = $visitas;

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
     * Set userOperator
     *
     * @param \App\Entity\UserOperator $userOperator
     * @return ComunicationSendRegistry
     */
    public function setUserOperator(UserOperator $userOperator = null)
    {
        $this->userOperator = $userOperator;

        return $this;
    }

    /**
     * Get userOperator
     *
     * @return \App\Entity\UserOperator
     */
    public function getUserOperator()
    {
        return $this->userOperator;
    }


}
