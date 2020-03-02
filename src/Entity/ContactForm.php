<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

/**
 * Created by PhpStorm.
 * User: Eduardo.Facenda
 * Date: 15/10/2015
 * Time: 16:49
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class ContactForm
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="contactName", type="string", length=250)
     * @Assert\NotBlank(
     *     message="El nombre de la persona de contacto es obligatorio"
     * )
     * @Assert\Length(
     *     min=3,
     *     max=250,
     *     minMessage="El nombre de contacto es muy corto.",
     *     maxMessage="El nombre de contacto es muy largo."
     * )
     */
    private $contactName;
    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=250)
     * @Assert\NotBlank(
     *     message="La descripción debe rellenarse"
     * )
     * @Assert\Length(
     *     min=10,
     *     max=250,
     *     minMessage="La descripción es demasiado corta.",
     *     maxMessage="La descripción es demasiado larga."
     * )
     */
    private $description;
    /**
     * @var
     * @ORM\Column(name="message", type="text", length=250)
     * @Assert\NotBlank(
     *     message="El mensaje no puede quedar en blanco"
     * )
     * @Assert\Length(
     *     min=20,
     *     max=3000,
     *     minMessage="El mensaje es demasiado corto.",
     *     maxMessage="El mensaje es demasiado largo."
     * )
     */
    private $message;

    /**
     * Set contactName
     *
     * @param string $contactName
     * @return ContactForm
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * Get contactName
     *
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ContactForm
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return ContactForm
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
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
}
