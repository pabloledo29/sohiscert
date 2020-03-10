<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Controller;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\UpdateLog;
use App\Entity\CultivosRec;
use App\Entity\Ganaderias;
use App\Entity\Industrias;
use App\Entity\Productos;
use App\Entity\ProductosG;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Operator;
use App\Entity\RelationshipRegister;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * Class OperatorController
 *
 * Controladores y métodos asociados relacionados con Operadores.
 * Tanto para usuarios con rol Admin como con rol User.
 *
 * Los Controladores públicas se encuentran bajo la ruta / y desactivadas.
 * Los Controladores para UserOperator bajo la ruta /private
 * Los Controladores para rol Admin bajo la ruta /admin
 *
 * @package App\Controller
 */
class OperatorController extends AbstractController
{
    /**
     * Controlador que renderiza el formulario de búsqueda público de Operadores.
     *
     * Solo implementa búsqueda por CIF, Deno o Selección de producto.
     *
     * @return Response
     * @Route("/operatorlist/search", name="public_operator_search")
     */
    public function searchOperatorAction()
    {
        return $this->render('public/searchoperator.html.twig', array('productos' => $this->loadAllProducts()));
    }

    /**
     * Controlador que devuelve el resultado de la búsqueda pública de Operadores.
     *
     * @return Response
     * @Route("/operatorlist/search/result", name="public_operator_search_result")
     */
    public function searchAction()
    {
        $request = $this->get('request');
        $opCIF = $request->request->get('cif');
        $opDenoop = $request->request->get('denoop');
        $opReg = $request->request->get('opreg');
        $prodDeno = $request->request->get('idprod'); // Se recibe el nombre del producto

        $em = $this->getDoctrine()->getManager();
        
        if (strlen($opCIF) < 8 && strlen($opDenoop) < 5 && strlen($opReg) < 1 && strlen($prodDeno) < 1) {
            $allProducts = $this->loadAllProducts();

            return $this->render(
                'Public/searchoperator.html.twig',
                array(
                    'registers' => '',//$registers
                    'productos' => $allProducts,
                    'errormsg' => 'Debe introducir un CIF completo, una denominación de al menos 5
                     caracteres o elegir un producto.',
                )
            );
        }
        

        $operators = array();
        if ($prodDeno == '' && ($opCIF != '' || $opDenoop != '' || $opReg != '')) {
            // Caso en el que se busque por nombre o cif solo
            $operators = $em->getRepository(Operator::class)->findOperator($opCIF, $opDenoop, $opReg);

        } elseif ($prodDeno != '' && ($opCIF == '' && $opDenoop == '' && $opReg == '')) {
            // Caso que solo se busque por productos, y CIF y DENO esten vacios.
            //Se devuelven todos los operadores que tengan ese producto
            $operators = $this->getOperatorsByProduct($prodDeno);

        } else {
            // Caso que o Deno o Cif o los dos esten rellenos y se busque tambien por producto.
            //La interseccion de las busqueda

            $operatorsCifDeno = $em->getRepository(Operator::class)->findOperator($opCIF, $opDenoop, $opReg);

            if (sizeof($operatorsCifDeno) > 0) {
                $operators = $this->getOperatorsByProductCifDeno($prodDeno, $operatorsCifDeno);
            }
        }

        $response = new Response(json_encode($operators));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Función que busca Operadores por Denominación de producto.
     *
     * Recibida una muestra de operadores que previamente ha podido ser filtrada por
     * los criterios generales de CIF, subRegistro o denominación.
     *
     * @param string $prodDeno La denominación de producto proporcionada por el listado selectivo.
     * @param array $operatorsCifDeno Array asociativo con Operadores.
     * @return array Array de elementos Operator.
     */
    private function getOperatorsByProductCifDeno($prodDeno, array $operatorsCifDeno)
    {
        $operators = array();
        $em = $this->getDoctrine()->getManager();
        foreach ($operatorsCifDeno as $op) {
            $operator = $em->getRepository(Operator::class)->find($op['id']);
            $producto = $em->getRepository(Productos::class)->findOneBy(
                array('ptDeno' => $prodDeno)
            );

            if ($producto) {
                $cultivosRecOperator = $em->getRepository(CultivosRec::class)->findBy(
                    array('ruOperator' => $operator, 'ruProducto' => $producto)
                );
                if (sizeof($cultivosRecOperator) > 0) {
                    array_push($operators, $op);
                }
            } else {
                $productoG = $em->getRepository(ProductosG::class)->findOneBy(
                    array('pnDeno' => $prodDeno)
                );
                $ganaderiasOperator = $em->getRepository(Ganaderias::class)->findBy(
                    array('gnOperator' => $operator, 'gnProductosG' => $productoG)
                );
                if (sizeof($ganaderiasOperator) > 0) {
                    array_push($operators, $op);
                }
            }
        }

        return $operators;
    }

    /**
     * Función que devuelve Operadores buscados por denominación de producto.
     *
     * Dada una denominación de producto ya sea de Productos o ProductosG
     * Devuelve un array con los Operadores que están vinculados a dicho producto.
     *
     * @param string $prodDeno Denominación de producto.
     * @return array Array con resultado de datos de operador a mostrar.
     */
    private function getOperatorsByProduct($prodDeno)
    {
        $em = $this->getDoctrine()->getManager();
        $operators = array();
        $operatorsCod = array();

        //Sacamos todos los operadores de cultivosRec que tengan ese producto
        $producto = $em->getRepository(Productos::class)->findOneBy(
            array('ptDeno' => $prodDeno)
        );
        if ($producto) {
            $cultivosRecProducto = $em->getRepository(CultivosRec::class)->findBy(
                array('ruProducto' => $producto)
            );
            /* Para Operadores de CultivosRec */
            /** @var CultivosRec $item */
            foreach ($cultivosRecProducto as $item) {
                if (!in_array($item->getRuOperator()->getCodigo(), $operatorsCod)) {
                    $operator = $item->getRuOperator();
                    array_push(
                        $operators,
                        array(
                            'id' => $operator->getId(),
                            'opNop' => $operator->getOpNop(),
                            'codigo' => $operator->getCodigo(),
                            'opDenoop' => $operator->getOpDenoop(),
                            'opCif' => $operator->getOpCif(),
                            'reDeno' => $operator->getOpRegistro()->getReDeno(),
                            'opEst' => $operator->getOpEst(),
                            'opTpex' => $operator->getOpTpex(),
                            'opTel' => $operator->getOpTel(),
                        )
                    );
                    array_push($operatorsCod, $operator->getCodigo());
                }
            }
        }
        /* Para productos de Ganadería */
        $productoG = $em->getRepository(ProductosG::class)->findOneBy(
            array('pnDeno' => $prodDeno)
        );
        if ($productoG) {
            $ganaderiaProducto = $em->getRepository(Ganaderias::class)->findBy(
                array('gnProductosG' => $productoG)
            );
            /* Para los Operadores de Ganaderias */
            /** @var Ganaderias $item */
            foreach ($ganaderiaProducto as $item) {
                if (!in_array($item->getGnOperator()->getCodigo(), $operatorsCod)) {
                    $operator = $item->getGnOperator();
                    array_push(
                        $operators,
                        array(
                            'id' => $operator->getId(),
                            'opNop' => $operator->getOpNop(),
                            'codigo' => $operator->getCodigo(),
                            'opDenoop' => $operator->getOpDenoop(),
                            'opCif' => $operator->getOpCif(),
                            'reDeno' => $operator->getOpRegistro()->getReDeno(),
                            'opEst' => $operator->getOpEst(),
                            'opTpex' => $operator->getOpTpex(),
                            'opTel' => $operator->getOpTel(),
                        )
                    );
                    array_push($operatorsCod, $operator->getCodigo());
                }
            }
        }

        return $operators;
    }

    /**
     * Controlador que muestra el expediente de un Operador
     *
     * Funcionalidad desarrollada para la muestra pública del registro de operadores.
     *
     * @param $id
     * @return Response
     * @Route("/operatorlist/search/result/{id}", name="public_operator_show_operator")
     */
    public function showOperator($id)
    {
        $em = $this->getDoctrine()->getManager();
        $operator = $em->getRepository(Operator::class)->find($id);

        $normativas = null;
        $info = null;

        //info a mostrar:
        $relation = $em->getRepository(RelationshipRegister::class)->getRelationByRegSreg(
            $operator->getOpReg(),
            $operator->getOpSreg()
        );
        $normativas = $em->getRepository(Operator::class)->getOperatorNormative($operator);

        $info = $relation;
        $estado = $this->showState($operator->getOpEst());

        $updateLog = $em->getRepository(UpdateLog::class)->getLastUpdateLog();

        return $this->render(
            'public/showoperator.html.twig',
            array(
                'operator' => $operator,
                'info' => $info,
                'normativas' => $normativas,
                'estado' => $estado,
                'updateLog' => $updateLog
            )
        );
    }

    /**
     * Función que muestra la denominación del estado de un oprador.
     *
     * Datos de demominación de estado de un operador.
     *
     * @param string $opEst El código de estado de un Operador.
     * @return string
     */
    private function showState($opEst)
    {
        switch ($opEst) {
            case $opEst === 'V':
                return 'VERIFICACIÓN DOCUMENTAL';
            case $opEst === 'A':
                return 'AUDITORÍA';
            case $opEst === 'E':
                return 'PASO A EVALUACIÓN';
            case $opEst === 'F':
                return 'PASO A CERTIFICACIÓN';
            case $opEst === 'C':
                return 'LICENCIA CONCEDIDA';
            case $opEst === 'D':
                return 'LICENCIA DENEGADA';
            case $opEst === 'P':
                return 'SUSPENSIÓN LICENCIA';
            case $opEst === 'R':
                return 'RETIRADA LICENCIA';
            case $opEst === 'B':
                return 'ARCHIVADO POR BAJA';
            case $opEst === 'QT':
                return 'VERIFICACIÓN CUESTIONARIO';
            case $opEst === 'N':
                return 'CONFORME';
            case $opEst === 'X':
                return 'CANCELACION';
            case $opEst === 'I':
                return 'SIN ACTIVIDAD';
            case $opEst === 'T':
                return 'REVISIÓN PREVIA';
            default:
                return '';
        }
    }

    /*
    * Controllers para ser empleados por admin
    */

    /**
     * Controlador para generar el formulario de búsqueda de Operadores por un usuario con rol Admin.
     *
     * Permite búsqueda general por CIF o Denominación de cliente.
     * Así como por lista selectiva de Productos y Actividades.
     *
     * @return Response
     * @Route("/admin/search", name="admin_search_operator")
     */
    public function adminSearchOperatorAction()
    {
        
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
       
        return $this->render('admin/searchoperator.html.twig', array(
            'productos' => $this->loadAllProducts(), 'actividades' => $this->loadAllActivities()
        ));
    }

    /**
     * Controlador que devuelve el resultado de una búsqueda de Operador.
     *
     * Respuesta AJAX con los datos de operador.
     *
     * @return Response
     * @Route("/admin/search/result", name="admin_operator_searchresult")
     */
    public function adminSearchAction(Request $request)
    {
        
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }


        $opCIF = $request->request->get('cif');
        $opDenoop = $request->request->get('denoop');
        $opReg = $request->request->get('opreg');
        $prodDeno =$request->request->get('idprod'); // Se recibe el nombre del producto
        $opAct = $request->request->get('idAct');


        $em = $this->getDoctrine()->getManager();

        if (strlen($opCIF) < 8 && strlen($opDenoop) < 5 &&
            strlen($opReg) < 1 && strlen($prodDeno) < 1 && strlen($opAct) < 1
        ) {
            $allProducts = $this->loadAllProducts();
            $allActivities = $this->loadAllActivities();

            return $this->render(
                'admin/searchoperator.html.twig',
                array(
                    'registers' => '',//$registers
                    'productos' => $allProducts,
                    'actividades' => $allActivities,
                    'errormsg' => 'Debe introducir un CIF completo, una denominación de al menos 5 caracteres o elegir
                     un producto o una actividad.',
                )
            );
        }

        $operators = array();
        if ($prodDeno === '' && ($opCIF !== '' || $opDenoop !== '' || $opReg != '')) {
            // Caso en el que se busque por nombre o cif solo
            $operators = $em->getRepository(Operator::class)->findOperator($opCIF, $opDenoop, $opReg);

        } elseif ($prodDeno !== '' && ($opCIF === '' && $opDenoop === '' && $opReg == '' && $opAct === '')) {
            // Caso que solo se busque por productos, y CIF y DENO esten vacios.
            // Se devuelven todos los operadores que tengan ese producto
            $operators = $this->getOperatorsByProduct($prodDeno);
        } elseif ($opAct !== '') {
            // Caso en que se busque solo por actividad.
            $operators = $this->searchByAct($opAct, $opCIF, $opDenoop);
        } else {
            // Caso que o Deno o Cif o los dos esten rellenos
            // y se busque tambien por producto. La interseccion de las busqueda

            $operatorsCifDeno = $em->getRepository(Operator::class)->findOperator($opCIF, $opDenoop, $opReg);
            if (sizeof($operatorsCifDeno) > 0) {
                $operators = $this->getOperatorsByProductCifDeno($prodDeno, $operatorsCifDeno);
            }
        }

        $response = new Response(json_encode($operators));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
     * Controlador para mostrar el expediente de un Operador.
     *
     * Dada una id de Operador muestra un usuario con rol de Administrador la ficha del expediente de un Oprador
     *
     * @param string $id La id en el sisema symfony2 de un determinado Operador.
     * @return Response
     * @Route("/admin/search/result/{id}", name="admin_operator_show_operator")
     */
    public function adminShowOperator($id)
    {
        
        $em = $this->getDoctrine()->getManager();

        $operator = $em->getRepository(Operator::class)->find($id);
        $normativas = null;
        $info = null;

        //info a mostrar:
        $relation = $this->getDoctrine()->getRepository(RelationshipRegister::class)->getRelationByRegSreg(
            $operator->getOpReg(),
            $operator->getOpSreg()
        );
        $normativas = $this->getDoctrine()->getRepository(Operator::class)->getOperatorNormative($operator);

        $info = $relation;
        $estado = $this->showState($operator->getOpEst());
        $updateLog = $this->getDoctrine()->getManager()->getRepository(UpdateLog::class)->getLastUpdateLog();

        return $this->render(
            'admin/useroperator_expediente.html.twig',
            array(
                'operator' => $operator,
                'info' => $info,
                'normativas' => $normativas,
                'estado' => $estado,
                'updateLog' => $updateLog
            )
        );
    }

    /*
     * Reorganización de controllers para SHC Fase II
     */

    /* Controllers para ser empleados por userAdmin */

    /**
     * Controlador que muestra el expediente de un Operador a un usuario con rol Admin.
     *
     * Muestra los datos de un operador desde la ficha de un cliente.
     *
     * @param string $id La id en el sistema symfony2 del Operador.
     * @return Response
     * @Route("/admin/expediente/show/{id}", name="admin_useroperator_expediente_show")
     */
    public function adminShowExpedienteAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        $operator = $this->getDoctrine()->getManager()->getRepository(Operator::class)->find($id);

        return $this->render(
            'admin/useroperator_expediente.html.twig',
            $this->retrieveOperatorInfo($operator)
        );
    }

    /*
     *  Controllers para ser empleados por un UserOperator
     */

    /**
     * Controlador que muestra el expediente de un Operador vinculado al UserOperator logueado.
     *
     * Recibida por POST la id de un Operador comprueba que existe y que está asociada al UserOperator de la sesión.
     *
     * @param Request $request
     * @return Response
     * @Route("/private/expediente/show", name="private_useroperator_expediente_show")
     */
    public function showExpedienteAction(Request $request)
    {
        $user = $this->getUser();
        $id = $request->request->get('id');
        $operator = $this->getDoctrine()->getManager()->getRepository(Operator::class)->find($id);

        if (!$user->getOperators()->contains($operator)) {
            throw $this->createAccessDeniedException();
        }

        return $this->render(
            'private/useroperator_expediente.html.twig',
            $this->retrieveOperatorInfo($operator)
        );
    }

    /**
     * Función que extrae los datos a mostrar del expediente de un Operador dado.
     *
     * Devuelve un array con la información de:
     *  * Operador
     *  * estado asociado a Operador
     *  * Información vinculada por relación RelationshipRegister.
     *
     * @param Operator $operator Un Operador dado.
     * @return array
     */
    private function retrieveOperatorInfo(Operator $operator)
    {
        $em = $this->getDoctrine()->getManager();
        //info a mostrar:
        $normativas = null;
        $info = null;
        $relation = $em->getRepository(RelationshipRegister::class)->getRelationByRegSreg(
            $operator->getOpReg(),
            $operator->getOpSreg()
        );
        
        $normativas = $em->getRepository(Operator::class)->getOperatorNormative($operator);
        $updateLog = $em->getRepository(UpdateLog::class)->getLastUpdateLog();
        $info = $relation;

        return array(
            'operator' => $operator,
            'info' => $info,
            'normativas' => $normativas,
            'estado' => $this->showState($operator->getOpEst()),
            'updateLog' => $updateLog,
        );
    }

    /**
     * Función que proporciona el listado de productos y productosG
     *
     * Permite poblar el listado selectivo de productos del formulario de búsquedas de Operadores.
     * Se muestran solo los productos con estado distinto de B, dicho estado es introducido manualmente.
     *
     * @return array
     */
    private function loadAllProducts()
    {
        $productos = $this->getDoctrine()->getManager()->getRepository(Productos::class)->findBy(
            [],
            ['ptDeno' => 'ASC']
        );
        $productosG = $this->getDoctrine()->getManager()->getRepository(ProductosG::class)->findBy(
            [],
            ['pnDeno' => 'ASC']
        );

        $allProducts = array();
        foreach ($productos as $product) {
            if (!in_array($product->getPtDeno(), $allProducts) && $product->getPtEst() != 'B') {
                array_push($allProducts, $product->getPtDeno());
            }
        }
        foreach ($productosG as $product) {
            if (!in_array($product->getPnDeno(), $allProducts) && $product->getPnEst() != 'B') {
                array_push($allProducts, $product->getPnDeno());
            }
        }
        sort($allProducts);

        return $allProducts;
    }

    /**
     * Función que extrae de la DB las actividades vinculadas directamente a Operador
     * y todas aquellas de ActividadesI que aparecen en algún operador con Industria.
     *
     * Extrae de la DB todas las actividades que aparecen directamente en Operador
     * y todas aquellas que aparecen vinculadas a un operador de industria.
     *
     * Devolviendo un array ordenado por deno de actividad para conformar
     * el selector de búsqueda con un val formado por:
     *
     *  prefix-id o prefix-opAct
     *
     * @return array
     */
    private function loadAllActivities()
    {
        /**
         * Transforma cada fila en una nueva indicando procedencia del valor
         *
         * @param array $row Array asociativo con ID de Act y Deno
         * @return array Array con id transformado y Deno
         */
        $appendActType = function ($row) {
            return ['ind-' . $row['id'], strtoupper($row['ainDeno'])];
        };

        /**
         * Transforma cada fila en una nueva con el id transformado para indicar procedencia y el opAct
         *
         * @param array $row Array asociativo con opAct
         * @return array Array con id transformada y opAct
         */
        $appendOpType = function ($row) {
            return ['op-' . $row['opAct'], strtoupper($row['opAct'])];
        };

        $indusAct = $this->getDoctrine()->getManager()->getRepository(Industrias::class)->getDistinctActividades();
        $indusAct = array_map($appendActType, $indusAct);

        $opAct = $this->getDoctrine()->getManager()->getRepository(Operator::class)->findDistinctOpAct();
        $opAct = array_map($appendOpType, $opAct);

        $actividades = array_merge($indusAct, $opAct);
        usort($actividades, function ($a, $b) {
            return strnatcmp($a[1], $b[1]);
        });

        return $actividades;
    }

    /**
     * Búsqueda por actividad.
     *
     * Función que permite la búsqueda por actividad ya sea de Operador o Industrial.
     * Pudiendo además filtrar por CIF o denominación.
     *
     * @param $actId
     * @param $opCIF
     * @param $opDenoop
     * @return array
     */
    private function searchByAct($actId, $opCIF, $opDenoop)
    {
        $em = $this->getDoctrine()->getManager();
        $actParam = explode('-', $actId, 2);
        if ($actParam[0] === 'op') {
            $operators = $em->getRepository(Operator::class)->getOperatorsAct($actParam[1], $opCIF, $opDenoop);
        } elseif ($actParam[0] === 'ind') {
            $operators = $em->getRepository(Industrias::class)
                ->getIndusOperatorsByAct($actParam[1], $opCIF, $opDenoop);
        } else {
            return [];
        }


        return $operators;
    }
}