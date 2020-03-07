<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use App\Entity\ProductosG;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlType;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\Type;

/**
 * @JMS\XmlRoot("Registro")
 * 
 */
class RegistroProductosG
{
    /**
     * @JMS\Type("ArrayCollection<App\Entity\ProductosG>")
     * @JMS\XmlList(entry="registro", inline=true)
     */
    public $Registro;

    
    public function __construct()
    {
        $this->registro = new ArrayCollection();        
    }


    
}
