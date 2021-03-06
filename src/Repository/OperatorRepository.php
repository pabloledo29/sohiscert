<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NoResultException;
use App\Entity\Operator;
/**
 * OperatorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OperatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
       
        parent::__construct($registry,Operator::class);
    }

    /**
     * Function that compares all atributes of two instances of and Object to determine when to update the oldest one.
     *
     * @param Operator $operatorxml
     * @param Operator $operatorddbb
     * @return bool
     */
    public function compareEntities(Operator $operatorxml, Operator $operatorddbb)
    {
        $res = false;

        if ($operatorxml->getOpDenoop() != $operatorddbb->getOpDenoop()) {
            $res = true;
        }
        if ($operatorxml->getOpCif() != $operatorddbb->getOpCif()) {
            $res = true;
        }
        if ($operatorxml->getOpCdp() != $operatorddbb->getOpCdp()) {
            $res = true;
        }
        if ($operatorxml->getOpDomop() != $operatorddbb->getOpDomop()) {
            $res = true;
        }
        if ($operatorxml->getOpTel() != $operatorddbb->getOpTel()) {
            $res = true;
        }
        if ($operatorxml->getOpCcl() != $operatorddbb->getOpCcl()) {
            $res = true;
        }
        if ($operatorxml->getOpEst() != $operatorddbb->getOpEst()) {
            $res = true;
        }
        if ($operatorxml->getOpTpex() != $operatorddbb->getOpTpex()) {
            $res = true;
        }
        if ($operatorxml->getOpNop() != $operatorddbb->getOpNop()) {
            $res = true;
        }
        if ($operatorxml->getOpAct() != $operatorddbb->getOpAct()) {
            $res = true;
        }
        if ($operatorxml->getOpPvcl() != $operatorddbb->getOpPvcl()) {
            $res = true;
        }
        if ($operatorxml->getOpPbcl() != $operatorddbb->getOpPbcl()) {
            $res = true;
        }
        if ($operatorxml->getOpReg() != $operatorddbb->getOpReg()) {
            $res = true;
        }
        if ($operatorxml->getOpSreg() != $operatorddbb->getOpSreg()) {
            $res = true;
        }
        if ($operatorxml->getOpGgn() != $operatorddbb->getOpGgn()) {
            $res = true;
        }
        if ($operatorxml->getOpNrgap() != $operatorddbb->getOpNrgap()) {
            $res = true;
        }
        if ($operatorxml->getOpTecdeno() != $operatorddbb->getOpTecdeno()) {
            $res = true;
        }
        if ($operatorxml->getOpFaud() != $operatorddbb->getOpFaud()) {
            $res = true;
        }
        if ($operatorxml->getOpEma() != $operatorddbb->getOpEma()) {
            $res = true;
        }
        if ($operatorxml->getOpTecema() != $operatorddbb->getOpTecema()) {
            $res = true;
        }
        if ($operatorxml->getOpClp() != $operatorddbb->getOpClp()) {
            $res = true;
        }

        return $res;
    }

    /**
     * @return array|null
     */
    public function getLastOperatorId()
    {
        $query = $this->getDoctrine()->getManager()
            ->createQuery(
                'SELECT e.id FROM App\Entity\Operator e
	            order by e.id DESC'
            )->setMaxResults(1);
        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @param $opCif
     * @param $opDenoop
     * @param $opReg
     * @return array|null
     */
    public function findOperator($opCif, $opDenoop, $opReg)
    {
//        $query = $this->getEntityManager()
//            ->createQuery(
//                "
//			SELECT e.id, e.opNop, e.codigo, e.opDenoop, e.opCif, r.reDeno, e.opEst, e.opTpex, e.opTel
//			FROM App\Entity\Operator e INNER JOIN App\Entity\Register r WITH e.opRegistro = r.id
//			WHERE e.opCif like :cif and e.opDenoop like :denoop AND e.opSreg != :opsreg AND (e.opEst = :opest OR e.opEst = :opest2)
//			order by e.id DESC"
//            )
        $query = $this->getEntityManager()
            ->createQuery(
                "
			SELECT e.id, e.opNop, e.codigo, e.opDenoop, e.opCif, r.reDeno, e.opEst, e.opTpex, e.opTel
            FROM App\Entity\Operator e INNER JOIN App\Entity\Register r WITH e.opRegistro = r.id
			WHERE e.opCif like :cif and e.opDenoop like :denoop AND e.opTpex != :optpex
			order by e.id DESC"
            )
            ->setParameter('cif', '%' . $opCif . '%')
            ->setParameter('denoop', '%' . $opDenoop . '%')
            ->setParameter('optpex', 'P');
//            ->setParameter('opest', 'C')
//            ->setParameter('opest2', 'P');
           
        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @param Operator $operator
     * @return array|null
     */
    public function getOperatorNormative(Operator $operator)
    {
        $opNop = $operator->getOpNop();
        $opReg = $operator->getOpReg();
        $opSreg = $operator->getOpSreg();
        $productos = array();

        if ($opReg == '4') {
            foreach ($operator->getOpCultivosRec2() as $i) {
                if (null !== ($i->getRu2Cultivos())) {
                    if ((!in_array($i->getRu2Cultivos()->getCuDeno(), $productos))) {
                        array_push($productos, $i->getRu2Cultivos()->getCuDeno());
                    }
                }
            }
        }

        $res = null;
        // Buscamos la normativa por Registro y subregistro
        if ($opReg != '4' and $opSreg != 'IVV') {

            $query = $this->getEntityManager()
                ->createQuery(
                    'SELECT e.normaNormativa, e.normaProducto, e.normaCaDeno
                     FROM App\Entity\Normativa e
                     WHERE e.normaReg LIKE :opreg and e.normaSreg LIKE :opsreg'
                )
                ->setParameter('opreg', $opReg)->setParameter('opsreg', $opSreg);
            try {
                $res = $query->getResult();
            } catch (NoResultException $e) {
                $res = null;
            }

        } elseif ($opReg == '3' and $opSreg == 'IVV') {

            $opCa = substr($opNop, 0, 2);
            $query = $this->getEntityManager()
                ->createQuery(
                    'SELECT e.normaNormativa, e.normaProducto, e.normaCaDeno
                     FROM App\Entity\Normativa e
                     WHERE e.normaReg LIKE :opreg and e.normaSreg LIKE :opsreg and e.normaCa LIKE :opca'
                )
                ->setParameter('opreg', $opReg)->setParameter('opsreg', $opSreg)->setParameter('opca', $opCa);
            try {
                $res = $query->getResult();
            } catch (NoResultException $e) {
                $res = null;
            }

        } elseif ($opReg == '4' and ($opSreg == 'API' || $opSreg == 'FPI' || $opSreg == 'IPI')
            and (sizeof($productos) > 0)
        ) {
            $opCa = substr($opNop, 0, 2);
            // Meter el producto, para produccion integrada
            // hay que sacar la normativa por el producto y comunidad autonoma.
            $res = array();
            foreach ($productos as $producto) {

                $query = $this->getEntityManager()
                    ->createQuery(
                        'SELECT e.normaNormativa, e.normaProducto, e.normaCaDeno
					     FROM App\Entity\Normativa e
                         WHERE e.normaReg LIKE :opreg and e.normaSreg LIKE :opsreg
                         and e.normaCa LIKE :opca AND e.normaProducto LIKE :producto'
                    )
                    ->setParameter('opreg', $opReg)->setParameter('opsreg', $opSreg)->setParameter(
                        'opca',
                        $opCa
                    )->setParameter('producto', $producto);
                try {
                    $nor = $query->getResult();
                    if (!in_array($nor, $res)) {
                        array_push($res, $nor[0]);
                    }
                } catch (NoResultException $e) {
                    $res = null;
                }
            }
        } else {
            $res = " - ";
        }

        return $res;
    }

    /**
     * @param $opCif
     * @return array|null
     */
    public function getOperators($opCif)
    {

        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT e.id, e.opNop, e.codigo, e.opDenoop, e.opCif, r.reDeno, e.opEst, e.opTpex, e.opTel
			FROM App\Entity\Operator e INNER JOIN App\Entity\Register r WITH e.opRegistro = r.id
			WHERE e.opCif like :cif AND e.opSreg != :opsreg AND e.opEst = :opest 
			order by e.id DESC"
            )
            ->setParameter('cif', '%' . $opCif . '%')
            ->setParameter('opsreg', '2OP')->setParameter('opest', 'C');
        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }

    }

    /**
     * @param $opCif
     * @return array|null
     */
    public function getOperatorsCif($opCif)
    {

        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT e.id, e.opNop, e.codigo, e.opDenoop, e.opCif, r.reDeno, e.opEst, e.opTpex
			     FROM App\Entity\Operator e INNER JOIN App\Entity\Register r WITH e.opRegistro = r.id
			     WHERE e.opCif like :cif AND e.opEst = :opest
                 order by e.id DESC"
            )
            ->setParameter('cif', '%' . $opCif . '%')
            ->setParameter('opest', 'C');
        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @return array|null
     */
    public function getOperatorsConCif()
    {
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT DISTINCT e.opCif
			     FROM App\Entity\Operator e
			     WHERE e.opCif != :vacio
			     ORDER BY e.id DESC"
            )
            ->setParameter('vacio', '');
        //->setMaxResults(50);
        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getOperatosConCifEmailNoUser()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        /**
         * Select usando Expr() Class de Doctrine ->
         * http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/query-builder.html#the-expr-class
         */
        $q = $qb->select(array('DISTINCT e.opCif,e.opDenoop, e.opEma'))
            ->from('App\Entity\Operator', 'e')
            ->where(
                $qb->expr()->notLike('e.opCif', ':vacio'),
                $qb->expr()->notLike('e.opCif', ':naporta'),
                $qb->expr()->notLike('e.opEst', ':N'),
                $qb->expr()->notLike('e.opEst', ':B'),
                $qb->expr()->notLike('e.opEst', ':D'),
                $qb->expr()->gt($qb->expr()->length('e.opEma'), 6)
            )
            ->setParameter('vacio', '')
            ->setParameter('naporta', '%NO%')
            ->setParameter('N', 'N')
            ->setParameter('B', 'B')
            ->setParameter('D', 'D')
//            ->setMaxResults(50)
            ->getQuery();

        try {
            return $q->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /*
     * Operators con cif que no tienen cuenta de usuario
     */
    /**
     * @return array|null
     */
    public function getOperatorsCifNotUser()
    {
        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT DISTINCT e.opCif, e.opEma
			     FROM App\Entity\Operator e
			     WHERE e.opCif != :vacio
				 AND e.opCif NOT IN
					(SELECT u.username
						FROM App\Entity\UserOperator u)
                 ORDER BY e.opCif"
            )
            ->setParameter('vacio', '');
        //->setMaxResults(50);
        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @param $opAct
     * @param $opCif
     * @param $opDenoop
     * @return array
     */
    public function getOperatorsAct($opAct, $opCif, $opDenoop)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        /**
         * Select usando Expr() Class de Doctrine ->
         * http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/query-builder.html#the-expr-class
         */
        $q = $qb->select(array('op.id as id, op.opNop as opNop, op.codigo as codigo,
         op.opDenoop as opDenoop, op.opCif as opCif, re.reDeno as reDeno,
          op.opEst as opEst, op.opEst, op.opTpex as opTpex, op.opTel as opTel'))
            ->from('App\Entity\Operator', 'op')
            ->where(
                $qb->expr()->like('op.opAct', ':opAct'),
                $qb->expr()->like('op.opCif', ':opCif'),
                $qb->expr()->like('op.opDenoop', ':opDenoop')
            )
            ->innerJoin('App\Entity\Register', 're', 'WITH', 'op.opRegistro = re.id')
            ->setParameter('opAct', '%' . $opAct . '%')
            ->setParameter('opCif', '%' . $opCif . '%')
            ->setParameter('opDenoop', '%' . $opDenoop . '%')
            ->getQuery();

        $result = $q->getResult();

        return $result;
    }

    /**
     * @return array
     */
    public function findDistinctOpAct()
    {
        $em =  $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        /**
         * Select usando Expr() Class de Doctrine ->
         * http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/query-builder.html#the-expr-class
         */
        $q = $qb->select(array('DISTINCT op.opAct'))
            ->from('App\Entity\Operator', 'op')
            ->where(
                $qb->expr()->notLike('op.opAct', ':vacio')
            )
            ->setParameter('vacio', '')
            ->addOrderBy('op.opAct')
            ->getQuery();

        $result = $q->getResult();

        return $result;
    }

    public function findOneByOpNop()
    {
        $em =  $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        /**
         * Select usando Expr() Class de Doctrine ->
         * http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/query-builder.html#the-expr-class
         */
        $q = $qb->select(array('DISTINCT op.opAct'))
            ->from('App\Entity\Operator', 'op')
            ->where(
                $qb->expr()->notLike('op.opAct', ':vacio')
            )
            ->setParameter('vacio', '')
            ->addOrderBy('op.opAct')
            ->getQuery();

        $result = $q->getResult();

        return $result;
    }

    /**
     * @param $opCif
     * @return array|null
     */
    public function getOperatorCifandNop($opCif, $opNop)
    {

        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT e.id, e.opNop, e.codigo, e.opDenoop, e.opCif, r.reDeno, e.opEst, e.opTpex
			     FROM App\Entity\Operator e INNER JOIN App\Entity\Register r WITH e.opRegistro = r.id
			     WHERE e.opCif like :cif AND e.opEst = :opest AND e.opNop like :nop
                 order by e.id DESC"
            )
            ->setParameter('cif', '%' . $opCif . '%')
            ->setParameter('nop', '%' . $opNop . '%')
            ->setParameter('opest', 'C');
        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }


    /**
     * @param $opCif
     * @return array|null
     */
    public function getOperatorTelefono($opNop)
    {

        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT e.opTel
			     FROM App\Entity\Operator e INNER JOIN App\Entity\Register r WITH e.opRegistro = r.id
			     WHERE e.opEst = :opest AND e.opNop like :nop
                 order by e.id DESC"
            )
            ->setParameter('nop', '%' . $opNop . '%')
            ->setParameter('opest', 'C');
        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

     /**
     * @param $opCif
     * @return array|null
     */
    public function getOperatorEmail($opNop)
    {

        $query = $this->getEntityManager()
            ->createQuery(
                "SELECT e.opEma
			     FROM App\Entity\Operator e INNER JOIN App\Entity\Register r WITH e.opRegistro = r.id
			     WHERE e.opEst = :opest AND e.opNop like :nop
                 order by e.id DESC"
            )
            ->setParameter('nop', '%' . $opNop . '%')
            ->setParameter('opest', 'C');
        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
