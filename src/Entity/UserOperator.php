<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity
 * @ORM\Table(name="useroperator")
 * @UniqueEntity("username", entityClass = "App\Entity\User", message="username.already_used")
 * @ORM\Entity(repositoryClass="App\Repository\UserOperatorRepository")
 */
class UserOperator extends User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
     */
    protected $client_id;

    /**
     * @ORM\ManyToMany(targetEntity="Operator")
     * @ORM\JoinTable(name="client_operators",
     *      joinColumns={@ORM\JoinColumn(name="useroperator_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="operator_id", referencedColumnName="id")}
     * )
     */
    protected $operators;

    
    protected $current_password = "";
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->operators = new ArrayCollection();
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
     * Add operators
     *
     * @param \App\Entity\Operator $operators
     * @return UserOperator
     */
    public function addOperator(Operator $operators)
    {
        $this->operators[] = $operators;

        return $this;
    }

    /**
     * Remove operators
     *
     * @param \App\Entity\Operator $operators
     */
    public function removeOperator(Operator $operators)
    {
        $this->operators->removeElement($operators);
    }

    public function getCurrentPassword(): string
    {
        return $this->current_password;
    }
    public function setCurrentPassword($current_password): self
    {
        $this->setPassword($current_password);
        return $this;
    }


    /**
     * Get operators
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOperators()
    {
        return $this->operators;
    }

    public function getClientId()
    {
        return $this->client_id;
    }
    public function setClientId(int $client_id ):self
    {
        $this->client_id = $client_id;
        return $this->client_id;
    }
}
