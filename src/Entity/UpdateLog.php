<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 8/03/16
 * Time: 16:52
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="updateLog")
 * @ORM\Entity(repositoryClass="App\Repository\UpdateLogRepository")
 */
class UpdateLog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="timestamp", type="datetime")
     */
    private $timeStamp;

    public function __construct()
    {
        $this->timeStamp = new \DateTime('now', new \DateTimeZone('Europe/Madrid'));
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
     * Set timeStamp
     *
     * @param \DateTime $timeStamp
     * @return UpdateLog
     */
    public function setTimeStamp($timeStamp)
    {
        $this->timeStamp = $timeStamp;

        return $this;
    }

    /**
     * Get timeStamp
     *
     * @return \DateTime
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }
}
