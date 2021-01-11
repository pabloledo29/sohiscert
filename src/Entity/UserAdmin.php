<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="useradmin")
 * @UniqueEntity("username", entityClass = "App\Entity\User", message="username.already_used")
 * @ORM\Entity(repositoryClass="App\Repository\UserAdminRepository")
 */
class UserAdmin extends User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

}
