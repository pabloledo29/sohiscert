<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Controller;


use App\Entity\Operator;
use App\Entity\UserOperator;
use App\Entity\UpdateLog;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use DataDog\PagerBundle\Pagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class UserAdminController
 *
 * Contiene los controladores y métodos necearios para la Administración por parte del personal de la aplicación.
 *
 * @package App\Controller
 */
class UserAdminController extends AbstractController
{
    /**
     * Listado de UserOperators por Username(CIF)
     *
     * Genera la vista del listado general de Operadores con filtro de búsqueda por CIF.
     *
     * @param Request $request
     * @return Response
     * @Route("/admin/home", name="admin_useroperator_list")
     */
    public function indexAction(Request $request)
    {
        $qb = $this->getDoctrine()->getManager()->getRepository(UserOperator::class)
            ->createQueryBuilder('u');

        $options = [
            'sorters' => ['u.username' => 'ASC'],
            'applyFilter' => [$this, 'userOperatorFilters'], // custom filter handling
            'limit' => 25,
            'range' => 10,
            'maxPerPage' => 100,
        ];

        $userOperators = new Pagination($qb, $request, $options);

        return $this->render('admin/useroperator_datadoglist.html.twig', array('userOperators' => $userOperators));
    }

    /**
     * Filtro de búsqueda.
     *
     * Contiene el filtor de búsqueda necesario para el listado de Operadores por CIF con el componente DatadogList.
     *
     * @param QueryBuilder $qb
     * @param $key
     * @param $val
     * @throws \Exception
     */
    public function userOperatorFilters(QueryBuilder $qb, $key, $val)
    {
        switch ($key) {
            case 'usuario':
                if ($val) {
                    $qb->andWhere($qb->expr()->like('u.username', ':name'));
                    $qb->setParameter('name', "%$val%");
                }
                break;
            default:
                // if user attemps to filter by other fields, we restrict it
                throw new \Exception("filter not allowed");
        }
    }

    /**
     * Vista de perfil de UserOperator
     *
     * Genera la vista para administradores de la ficha de Cliente de un UserOperator.
     *
     * @param Request $request
     * @return Response
     * @Route("/admin/client/view", name="admin_useroperator_view")
     */
    public function userOperatorViewAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $userOperatorId = $request->request->get('userOperatorId');
        $userOperatorUsername = $request->request->get('userOperatorUsername');
        $em = $this->getDoctrine()->getManager();

        if (!is_null($userOperatorId)) {
            $userOperator = $em->getRepository(UserOperator::class)->find($userOperatorId);
        } else {
            $userOperator = $em->getRepository(UserOperator::class)->findOneBy(
                array('username' => $userOperatorUsername)
            );
        }

        if ($userOperator == null) {
            throw $this->createAccessDeniedException();
        }

        $updateLog = $em->getRepository(UpdateLog::class)->getLastUpdateLog();

        return $this->render('admin/useroperator_profile.html.twig', array(
            'userOperator' => $userOperator, 'updateLog' => $updateLog));
    }

    /**
     * Controlador de actualización manual de información de Cliente y Operadores.
     *
     * A petición del administrador actualiza los datos de un Cliente vinculado a UserOperator.
     * Así como la vinculación de todos los Operadores ya en el sistema con dicho UserOperator.
     *
     * @param Request $request
     * @return Response
     * @Route("/admin/client/refresh", name="admin_useroperator_update")
     */
    public function userOperatorUpdateAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getManager();
        $toolsupdate = $this->container->get('toolsupdate');
        $gsbase = $this->container->get('gsbase');
        $gsbasexml = $this->container->get('gsbasexml');
        $id = $request->request->get('id');

        /** @var UserOperator $userOperator */
        $userOperator = $em->getRepository(UserOperator::class)->find($id);
        $operators = $em->getRepository(Operator::class)->findBy(array('opCif' => $userOperator->getUsername()));

        if (count($operators) > 0) {
            
            $operator = $operators[0];
            $opCcl = $operator->getOpCcl();
            $client = $toolsupdate->getClient($gsbase, $gsbasexml, $userOperator, $opCcl);
        } else {
            var_dump("hola");
            exit;
            $client['registersProcessed'] = 0;
        }

        $gsbase->gsbase_stop();

        if ($client['registersProcessed'] < 1) {
            $response = new Response(
                json_encode(array('type' => 'error', 'msg' => 'Error recuperando los datos del cliente.'))
            );
        } else {

            $userOperator->getOperators()->clear();

            foreach ($operators as $operator) {
                $userOperator->addOperator($operator);
            }
            $em->flush();

            $serializer = $this->container->get('jms_serializer');
            $cliente = $serializer->serialize($userOperator->getClientId(), 'json');
            //$operators = $serializer->serialize($userOperator->getOperators(), 'json');
            $ops = $serializer->serialize($operators, 'json');

            $response = new Response(
                json_encode(
                    array(
                        'type' => 'success',
                        'processed' => $client['registersProcessed'],
                        'client' => $cliente,
                        'operators' => $ops,
                    )
                )
            );
        }
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
