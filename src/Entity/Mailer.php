<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MailerRepository")
 */
class Mailer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mailer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $twig;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email_from_address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email_from_name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMailer(): ?string
    {
        return $this->mailer;
    }

    public function setMailer(string $mailer): self
    {
        $this->mailer = $mailer;

        return $this;
    }

    public function getTwig(): ?string
    {
        return $this->twig;
    }

    public function setTwig(string $twig): self
    {
        $this->twig = $twig;

        return $this;
    }

    public function getEmailFromAddress(): ?string
    {
        return $this->email_from_address;
    }

    public function setEmailFromAddress(string $email_from_address): self
    {
        $this->email_from_address = $email_from_address;

        return $this;
    }

    public function getEmailFromName(): ?string
    {
        return $this->email_from_name;
    }

    public function setEmailFromName(string $email_from_name): self
    {
        $this->email_from_name = $email_from_name;

        return $this;
    }
}
