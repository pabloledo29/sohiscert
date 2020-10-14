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
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="gs_operators_transformation")
 * @ORM\Entity(repositoryClass="App\Repository\OpNopTransformRepository")
 * @JMS\XmlRoot("Registro")
 */
class OpNopTransform
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

     /**
     * @ORM\Column(name="opNop", type="string", length=12, nullable=true, options={"default" = "-"})
     * @JMS\Type("string")
     */
    private $opNop;

     /**
     * @ORM\Column(name="opNopTransform", type="string", length=12, nullable=true, options={"default" = "-"})
     * @JMS\Type("string")
     */
    private $opNopTransform;


  /* public function __construct(
		String $opNop,
		String $opNopTransform
    )
    {
        $this->opNop = $opNop;
        $this->opNopTransform = $opNopTransform;
    }*/



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
     * @return Operator
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
     * Set opNop
     *
     * @param string $opNopTransform
     * @return Operator
     */
    public function setopNopTransform($opNopTransform)
    {
        $this->opNopTransform = $opNopTransform;

        return $this;
    }

    /**
     * Get opNop
     *
     * @return string
     */
    public function getopNopTransform()
    {
        return $this->opNopTransform;
    }

}
