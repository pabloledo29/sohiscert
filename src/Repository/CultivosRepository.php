<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Cultivos;
/**
 * CultivosRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CultivosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
       
        parent::__construct($registry,Cultivos::class);
    }
    
    /**
     * Function that compares all atributes of two instances of and Object to determine when to update the oldest one.
     *
     * @param Cultivos $cultivosxml
     * @param Cultivos $cultivosddbb
     * @return bool
     */
    public function compareEntities(Cultivos $cultivosxml, Cultivos $cultivosddbb)
    {
        $res = false;

        if ($cultivosxml->getCuDeno() != $cultivosddbb->getCuDeno()) {
            $res = true;
        }
        if ($cultivosxml->getCuRoae() != $cultivosddbb->getCuRoae()) {
            $res = true;
        }

        return $res;
    }
}
