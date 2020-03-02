<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\ActividadesI;

/**
 * ActividadesIRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ActividadesIRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
       
        parent::__construct($registry,ActividadesI::class);
    }

    public function compareEntities(ActividadesI $actividadesIxml, ActividadesI $actividadesIddbb)
    {
        $res = false;

        if ($actividadesIxml->getAinDeno() != $actividadesIddbb->getAinDeno()) {
            $res = true;
        }

        return $res;

    }
}