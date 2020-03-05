<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="projects")
 */
class Project
{
    const OPEN = 1;
    const CLOSED = 2;

    /**
     * @ORM\GeneratedValue
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(length=3)
     */
    private $code;

    /**
     * @ORM\Column
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $hoursSpent = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled = true;

    /**
     * @ORM\Column(type="integer")
     */
    private $deadline = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $status = self::OPEN;

    /**
     * @ORM\ManyToOne(targetEntity="Language")
     * @ORM\JoinColumn(nullable=false)
     */
    private $language;

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setHoursSpent($hoursSpent)
    {
        $this->hoursSpent = $hoursSpent;
        return $this;
    }

    public function getHoursSpent()
    {
        return $this->hoursSpent;
    }

    public function setLanguage(Language $language)
    {
        $this->language = $language;
        return $this;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
        return $this;
    }

    public function getDeadline()
    {
        return $this->deadline;
    }

    public function isOverDeadline()
    {
        return $this->hoursSpent > $this->deadline;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
