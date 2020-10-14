<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NoResultException;
use App\Entity\OpNopTransform;
/**
 * OperatorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OpNopTransformRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
       
        parent::__construct($registry,OpNopTransform::class);
    }

}