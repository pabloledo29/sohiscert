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
 * @JMS\XmlRoot("registro")
 */
class RegistroIAvesCorral
{
    /**
     * @JMS\Type("ArrayCollection<App\Entity\IAvesCorral>")
     * @JMS\XmlList(entry="registro")
     */
    public $Registro;

    public function __construct()
    {
        $this->registro = new ArrayCollection();
    }
}
