<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

/**
 * Created by PhpStorm.
 * User: eduardo
 * Date: 5/02/16
 * Time: 11:52
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\XmlRoot("registro")
 */
class RegistroDocAnaDoc
{
    /**
     * @JMS\Type("ArrayCollection<App\Entity\DocAnaDoc>")
     * @JMS\XmlList(entry="registro")
     */
    public $Registro;

    public function __construct()
    {
        $this->registro = new ArrayCollection();
    }
}
