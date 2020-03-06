<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Controller;

use App\Entity\Register;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Operator;
use App\Entity\RelationshipRegister;
use App\Entity\CultivosRecAux;

/**
 * Class UpdateController
 *
 * Contiene los Controladores y métodos necesarios para updatear los registros del sistema.
 *
 * @package App\Controller
 */
class UpdateController extends AbstractController
{
    /**
     * @Route("/update/updateOperator", name="update_operator_list")
     * @return Response
     */
    public function updateOperatorAction()
    {
        $gsbase = $this->get('gsbase');
        if ($gsbase->getGsbase() == null) {
            return $this->render(
                'public/operatorlist.html.twig',
                array('errormsg' => "Error estableciendo conexión con gsbase")
            );
        }
        $gsbasexml = $this->get('gsbasexml');
        $xml = $gsbasexml->getXmlUpdateOperator();
        $xmlRes = $gsbase->gsbase_exec('consulta_xml', $xml, 'consulta-xml');

        /* Change to lowercase tags from xml and transform to OperatorObject  */
        $newXml = preg_replace("#</?\w+#e", "strtolower('\\0')", $xmlRes);
        $operators = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroOperator', 'xml');

        $em = $this->getDoctrine()->getManager();
        $operatorsCreated = array();
        $operatorsUpdated = array();
        $operatorsNotUpdated = array();
        $operatorsNoInsert = array(
            'FBR',
            'FCG',
            'FGE',
            'FHC',
            'FKO',
            'FOP',
            'FPI',
            'FOZ',
            'FVA',
            'FVC',
            'FVC09',
            'FVF',
            'FVG',
            'FVI',
            'FVL',
            'FVN',
            'FVR',
            'FVS',
            'FVX',
            'FVZ',
            'EOZ',
            '2OP',
            'FBS',
            'VCA',
            'IDL',
            'IBR',
            'DL'
        );

        /** @var Operator $operatorXml */
        foreach ($operators->Registro as $operatorXml) {
            $opReg = $operatorXml->getOpReg();
            $opSubreg = $operatorXml->getOpSreg();
            if ($opReg != null) {
                $register = $this->getDoctrine()->getRepository(Register::class)->findOneBy(
                    array('codigo' => $opReg)
                );
                $operatorXml->setOpRegistro($register);
            }
            if ($opSubreg != null) {
                $subregister = $this->getDoctrine()->getRepository(Register::class)->findOneBy(
                    array('codigo' => $opSubreg)
                );
                $operatorXml->setOpSubregistro($subregister);
            }
            // Metemos la actividad a mano porque estas no estan guardadas en su sistema.
            // Deberian de estar en actividades-i pero no hay ninguna tabla intermedia con la cual relacionarla
            if ($opSubreg == 'VAE') {
                $operatorXml->setOpAct('SEMILLEROS Y VIVEROS');
            } elseif ($opSubreg == 'IOZ') {
                $operatorXml->setOpAct('ENVASADOR AZAFRAN DE LA MANCHA');
            } elseif ($opSubreg == 'FOP' || $opSubreg == 'SOP' || $opSubreg == 'IOP') {
                $operatorXml->setOpAct('ENVASADOR PIMENTON MURCIA');
            } elseif ($opSubreg == 'FPI') {
                $operatorXml->setOpTpex('P');
            }

            $operator = $this->getDoctrine()->getRepository(Operator::class)->findOneBy(
                array('codigo' => $operatorXml->getCodigo())
            );

            if (!$operator) {
                if ($operatorXml->getOpReg() != '3' && !in_array($operatorXml->getOpSreg(), $operatorsNoInsert)) {

                    $em->persist($operatorXml);
                    $em->flush();
                    $em->clear();
                    array_push($operatorsCreated, $operatorXml->getId() . " - " . $operatorXml->getCodigo());
                }
            } else {
                $updateEntity = $em->getRepository(Operator::class)->compareEntities($operatorXml, $operator);
                if ($updateEntity) {
                    $operator->setOpDenoop($operatorXml->getOpDenoop());
                    $operator->setOpCif($operatorXml->getOpCif());
                    $operator->setOpCdp($operatorXml->getOpCdp());
                    $operator->setOpCcl($operatorXml->getOpCcl());
                    $operator->setOpDomop($operatorXml->getOpDomop());
                    $operator->setOpTel($operatorXml->getOpTel());
                    $operator->setOpEst($operatorXml->getOpEst());
                    $operator->setOpTpex($operatorXml->getOpTpex());
                    $operator->setOpNop($operatorXml->getOpNop());
                    $operator->setOpGgn($operatorXml->getOpGgn());
                    $operator->setOpNrgap($operatorXml->getOpNrgap());
                    $operator->setOpNam($operatorXml->getOpNam());
                    $operator->setOpAct($operatorXml->getOpAct());
                    $operator->setOpPvcl($operatorXml->getOpPvcl());
                    $operator->setOpPbcl($operatorXml->getOpPbcl());

                    $opReg = $operatorXml->getOpReg();
                    $opSubreg = $operatorXml->getOpSreg();
                    if ($opReg != null && $opReg != $operator->getOpReg()) {
                        $register = $this->getDoctrine()->getRepository(Register::class)->findOneBy(
                            array('codigo' => $opReg)
                        );
                        $operator->setOpRegistro($register);
                    }
                    if ($opSubreg != $operator->getOpSreg()) {
                        $subregister = $this->getDoctrine()->getRepository(Register::class)->findOneBy(
                            array('codigo' => $opSubreg)
                        );
                        $operator->setOpSubregistro($subregister);
                    }

                    $operator->setOpReg($opReg);
                    $operator->setOpSreg($opSubreg);

                    $em->flush();
                    $em->clear();
                    array_push($operatorsUpdated, $operator);
                } else {
                    array_push($operatorsNotUpdated, $operator->getId() . " - " . $operator->getCodigo());
                }
            }
        }

        return $this->render(
            'public/operatorlist.html.twig',
            array('operatorsCreated' => $operatorsCreated, 'operatorsUpdated' => $operatorsUpdated)
        );
    }

    /**
     * @Route("/update/updateRegister", name="update_register")
     * @return Response
     */
    public function updateRegisterAction()
    {
        $gsbase = $this->get('gsbase');
        if ($gsbase->getGsbase() == null) {
            return $this->render(
                'public/operatorlist.html.twig',
                array('errormsg' => "Error estableciendo conexión con gsbase")
            );
        }

        $gsbasexml = $this->get('gsbasexml');
        $xml = $gsbasexml->getXmlUpdateregistry();

        $xmlRes = $gsbase->gsbase_exec('consulta_xml', $xml, 'consulta-xml');

        $newXml = preg_replace("#</?\w+#e", "strtolower('\\0')", $xmlRes);
        $registers = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroRegister', 'xml');

        $em = $this->getDoctrine()->getManager();
        $registersCreated = array();
        $registersUpdated = array();
        $registersNotUpdated = array();

        /** @var Register $registerXml */
        foreach ($registers->Registro as $registerXml) {
            $register = $this->getDoctrine()->getRepository(Register::class)->findOneBy(
                array('codigo' => $registerXml->getCodigo())
            );
            if (!$register) {
                $em->persist($registerXml);
                $em->flush();
                $em->clear();
                array_push($registersCreated, $registerXml);

                /* EN CASO DE IFS QUE SE REPITEN LOS CODIGOS
                }elseif($register && ($register->getReTipo()!=$registerXml->getReTipo())) {
                  $em->persist($registerXml);
                  $em->flush();
                  $em->clear();

                */
            } else {
                $updateEntity = $em->getRepository(Register::class)->compareEntities($registerXml, $register);

                /** @var Register $registerXml */
                if ($updateEntity && $registerXml->getCodigo() != 'IFS') {
                    $register->setReDeno($registerXml->getReDeno());
                    $register->setReTipo($registerXml->getReTipo());
                    $register->setRePad($registerXml->getRePad());
                    $register->setReAct($registerXml->getReAct());
                    $em->flush();
                    $em->clear();
                    array_push($registersUpdated, $register);
                } else {
                    array_push($registersNotUpdated, $register->getId() . " - " . $register->getCodigo());
                }
            }
        }

        return $this->render(
            'public/registerlist.html.twig',
            array('registersCreated' => $registersCreated, 'registersUpdated' => $registersUpdated)
        );
    }

    /**
     * @Route("/update/updateBasicInfo", name="update_basic_show")
     * @return Response
     */
    public function updateBasicAction()
    {
        return $this->render('public/basicinfo.html.twig');

    }

    /**
     * @Route("/update/updateBasicInfo/response", name="update_basic_info")
     * @return Response
     */
    public function updateBasicInfoAction()
    {
        /*
          Función valida para: tipos_cultivo, tipos_producto, cultivos, especies, productos_g, tipos-producc
        */
        $toolsupdate = $this->get('toolsupdate');
        $gsbasexml = $this->get('gsbasexml');
        $gsbase = $this->get('gsbase');

        if ($gsbase->getGsbase() == null) {
            return $this->render(
                'public/operatorlist.html.twig',
                array('errormsg' => "Error estableciendo conexión con gsbase")
            );
        }
        #var_dump($toolsupdate);
        #exit;
        $tiposCultivos = $toolsupdate->getTiposCultivos($gsbase, $gsbasexml);
        $tiposProducto = $toolsupdate->getTiposProducto($gsbase, $gsbasexml);
        $cultivos = $toolsupdate->getCultivos($gsbase, $gsbasexml);
        $especies = $toolsupdate->getEspecies($gsbase, $gsbasexml);
        $productosG = $toolsupdate->getProductosG($gsbase, $gsbasexml);
        $tiposProducc = $toolsupdate->getTiposProducc($gsbase, $gsbasexml);
//        $actividadesI = $toolsupdate->getActividadesI($gsbase, $gsbasexml);

        $gsbase->gsbase_stop();

        $response = new Response(
            json_encode(
                array(
                    'type' => 'success',
                    'tiposCultivos' => $tiposCultivos,
                    'tiposProducto' => $tiposProducto,
                    'cultivos' => $cultivos,
                    'especies' => $especies,
                    'productosG' => $productosG,
                    'tiposProducc' => $tiposProducc,
                )
            )
        );

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
     * @Route("/update/updateProductos", name="update_productos")
     * @return Response
     */
    public function updateProductosAction()
    {
        $toolsupdate = $this->get('toolsupdate');
        $gsbasexml = $this->get('gsbasexml');
        $gsbase = $this->get('gsbase');

        if ($gsbase->getGsbase() == null) {
            return $this->render(
                'public/operatorlist.html.twig',
                array('errormsg' => "Error estableciendo conexión con gsbase")
            );
        }

        $productos = $toolsupdate->getProductos($gsbase, $gsbasexml);

        return $this->render(
            'public/registerlist.html.twig',
            array(
                'registersCreated' => $productos['registersCreated'],
                'registersUpdated' => $productos['registersUpdated'],
                'registersNotUpdated' => $productos['registersNotUpdated'],
            )
        );
    }

    /**
     * @Route("/update/operatorEntity", name="update_operator_entity")
     * @return Response
     */
    public function getOperatorEntityAction()
    {
        $operators = $this->getDoctrine()->getRepository(Operator::class)->findAll();
        $em = $this->getDoctrine()->getManager();
        $i = 0;
//        $batchSize = 20;
        foreach ($operators as $operator) {
            //$em = $this->getDoctrine()->getEntityManager();
            $i++;
            $relation = $this->getDoctrine()->getManager()->getRepository(RelationshipRegister::class)->getRelationByRegSreg(
                $operator->getOpReg(),
                $operator->getOpSreg()
            ); //[rlEntity] => Ganaderias [rlInfo] => PRODUCTOSECO
            $operator->setOpEntity($relation['rlEntity']);
            $em->persist($operator);
        }

        $em->flush();
        $em->clear();
        foreach ($em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $em->getEventManager()->removeEventListener($event, $listener);
            }
        }
//        dump($i);

        return $this->render('public/alloperatorsinfo.html.twig', array('prueba' => 'prueba'));
    }

    /**
     * @Route("/update/getAllOperatorsCultivosRec", name="update_all_operators_cultivos_rec")
     * @return Response
     */
    public function getAllOperatorsCultivosRec()
    {
        $updateStart = date("H:i:s") . substr((string)microtime(), 1, 6);
        echo $updateStart . ' | ';

        $operatorsCultivosRec = $this->getDoctrine()->getRepository(Operator::class)->findBy(
            array('opEntity' => 'CultivosRec', 'opEst' => 'C')
        );
        $toolsupdate = $this->get('toolsupdate');
        $gsbasexml = $this->get('gsbasexml');
        $gsbase = $this->get('gsbase');

        $countCultivosRec = 0;
//        $countCultivosRecUpdate = 0;

        foreach ($operatorsCultivosRec as $operator) {
            //$operator =  $this->getDoctrine()->getRepository(Operator::class)->find(
            //$operatorcultivo->getIdOperator()
            //);

            $cultivosRecOperator = $toolsupdate->getCultivoRecOperator($gsbase, $gsbasexml, $operator);
            dump($cultivosRecOperator);
            print_r($operator->getOpNop() . '</br>');
            $countCultivosRec++;
        }

        $gsbase->gsbase_stop();
        $updateEnd = date("H:i:s") . substr((string)microtime(), 1, 6);
        echo $updateEnd . ' |||';
//        dump($countCultivosRec);

        return $this->render('public/alloperatorsinfo.html.twig', array('prueba' => 'prueba'));
    }

    /**
     * @Route("/update/getAllOperatorsCultivosRec2", name="update_all_operators_cultivos_rec2")
     * @return Response
     */
    public function getAllOperatorsCultivosRec2()
    {
        $operatorsCultivosRec2 = $this->getDoctrine()->getRepository(Operator::class)->findBy(
            array('opEntity' => 'CultivosRec2', 'opEst' => 'C')
        );
        $toolsupdate = $this->get('toolsupdate');
        $gsbasexml = $this->get('gsbasexml');
        $gsbase = $this->get('gsbase');
        $countCultivosRec2 = 0;
        $countCultivosRec2Update = 0;

        foreach ($operatorsCultivosRec2 as $operator) {
            //$operator =  $this->getDoctrine()->getRepository(Operator::class)->find(
            //$operatorcultivo->getIdOperator()
            //);
            $cultivosRec2Operator = $toolsupdate->getCultivoRec2Operator($gsbase, $gsbasexml, $operator);
            dump($cultivosRec2Operator);
            $countCultivosRec2++;
        }

        $gsbase->gsbase_stop();
        dump($countCultivosRec2);
        dump($countCultivosRec2Update);

        return $this->render('public/alloperatorsinfo.html.twig', array('prueba' => 'prueba'));
    }

    /**
     * @Route("/update/getAllOperatorsGanaderias", name="update_all_operators_ganaderias")
     * @return Response
     */
    public function getAllOperatorsGanaderias()
    {
        // Obtenemos todos los operadores y los recorremos uno por uno para sacar su NOP-REG-SUBREG y sacar su info
        //$operators = $this->getDoctrine()->getRepository(Operator::class)->findBy(array('opSreg' => 'PAE'));
        $operatorsGanaderias = $this->getDoctrine()->getRepository(Operator::class)->findBy(
            array('opEntity' => 'Ganaderias', 'opEst' => 'C')
        );
        //$cultivosrec = $this->getDoctrine()->getRepository('App\Entity\CultivosRec')->findAll();
        //$lastOperatorIdCultivoRec = end($cultivosrec)->getRuOperator()->getId();
        $toolsupdate = $this->get('toolsupdate');
        $gsbasexml = $this->get('gsbasexml');
        $gsbase = $this->get('gsbase');
        $countGanaderias = 0;
        $countGanaderiasUpdate = 0;

        foreach ($operatorsGanaderias as $operator) {
            //$operator =  $this->getDoctrine()->getRepository(Operator::class)->find(
            //$operatorcultivo->getIdOperator()
            //);
            $ganaderiaOperator = $toolsupdate->getGanaderiasOperator($gsbase, $gsbasexml, $operator);
            var_dump($ganaderiaOperator);
            $countGanaderias++;
        }

        $gsbase->gsbase_stop();
        dump($countGanaderias);
        dump($countGanaderiasUpdate);

        return $this->render('public/alloperatorsinfo.html.twig', array('prueba' => 'prueba'));
    }

    /**
     * @Route("/update/getAllOperatorsProductosIndus", name="update_all_operators_productos_indus")
     * @return Response
     */
    public function getAllOperatorsProductosIndus()
    {
        $operatorsProductosIndus = $this->getDoctrine()->getRepository(Operator::class)->findBy(
            array('opEntity' => 'ProductosIndus', 'opEst' => 'C')
        );

        $toolsupdate = $this->get('toolsupdate');
        $gsbasexml = $this->get('gsbasexml');
        $gsbase = $this->get('gsbase');

        $countProductosIndus = 0;
//        $countProductosIndusUpdate = 0;

        foreach ($operatorsProductosIndus as $operator) {
            //$operator =  $this->getDoctrine()->getRepository(Operator::class)->find(
            //$operatorcultivo->getIdOperator()
            //);
            $toolsupdate->getProductosIndusOperator($gsbase, $gsbasexml, $operator);
//            $productosIndusOperator = $toolsupdate->getProductosIndusOperator($gsbase, $gsbasexml, $operator);
//            dump($productosIndusOperator);
            $countProductosIndus++;
        }

        $gsbase->gsbase_stop();
//        dump($countProductosIndus);
//        dump($countProductosIndusUpdate);

        return $this->render('public/alloperatorsinfo.html.twig', array('prueba' => 'prueba'));
    }

    /**
     * @Route("/update/getAllOperatorsProductosPae", name="update_all_operators_productos_pae")
     * @return Response
     */
    public function getAllOperatorsProductosPae()
    {
        $operatorsProductosPae = $this->getDoctrine()->getRepository(Operator::class)->findBy(
            array('opEntity' => 'ProductosPae', 'opEst' => 'C')
        );
        $toolsupdate = $this->get('toolsupdate');
        $gsbasexml = $this->get('gsbasexml');
        $gsbase = $this->get('gsbase');

        $countProductosPae = 0;
//        $countProductosPaeUpdate = 0;

        foreach ($operatorsProductosPae as $operator) {
            //$operator =  $this->getDoctrine()->getRepository(Operator::class)->find(
            //$operatorcultivo->getIdOperator()
            //);
            $toolsupdate->getProductosPaeOperator($gsbase, $gsbasexml, $operator);
//            $productosPaeOperator = $toolsupdate->getProductosPaeOperator($gsbase, $gsbasexml, $operator);
//            dump($productosPaeOperator);
            $countProductosPae++;
        }
        $gsbase->gsbase_stop();
//        dump($countProductosPae);
//        dump($countProductosPaeUpdate);

        return $this->render('public/alloperatorsinfo.html.twig', array('prueba' => 'prueba'));

    }

    /**
     * @Route("/update/getAllOperatorsAvescorral", name="update_all_operators_avescorral")
     * @return Response
     */
    public function getAllOperatorsAvescorral()
    {
        $operatorsAvescorral = $this->getDoctrine()->getRepository(Operator::class)->findBy(
            array('opEntity' => 'Avescorral', 'opEst' => 'C')
        );

        $toolsupdate = $this->get('toolsupdate');
        $gsbasexml = $this->get('gsbasexml');
        $gsbase = $this->get('gsbase');

        $countProductosAvescorral = 0;
//        $countProductosAvescorralUpdate = 0;

        foreach ($operatorsAvescorral as $operator) {
            //$operator =  $this->getDoctrine()->getRepository(Operator::class)->find(
            //$operatorcultivo->getIdOperator()
            //);
            $toolsupdate->getIAvesCorralOperator($gsbase, $gsbasexml, $operator);
//            $productosAvescorral = $toolsupdate->getIAvesCorralOperator($gsbase, $gsbasexml, $operator);
//            dump($productosAvescorral);
            $countProductosAvescorral++;
        }

        $gsbase->gsbase_stop();
//        dump($countProductosAvescorral);
//        dump($countProductosAvescorralUpdate);

        return $this->render('public/alloperatorsinfo.html.twig', array('prueba' => 'prueba'));
    }

    /*
        /**
         * @Route("/update/getAllOperatorsIndustrias", name="update_all_operators_ganaderias")
         */
    /*
        public function getAllOperatorsIndustrias()
        {


          $operatorsIndustrias = $this->getDoctrine()->getRepository(Operator::class)->findBy(
            array('opEntity' => 'Industrias', 'opEst' => 'C')
            );

          $toolsupdate = $this->get('ToolsUpdate');
          $gsbasexml = $this->get('gsbasexml');
          $gsbase = $this->get('gsbase');

          $countIndustrias = 0;
          $countIndustriasUpdate = 0;

          foreach ($operatorsIndustrias as $operator) {
              //$operator =  $this->getDoctrine()->getRepository(Operator::class)->find(
    $operatorcultivo->getIdOperator()
    );
              $industriasOperator = $toolsupdate->getIndustriasOperator($gsbase, $gsbasexml, $operator);
              //var_dump($industriasOperator);
              $countIndustrias++;
          }

          $gsbase->gsbase_stop();

          print_r($countIndustrias);
          //print_r($countIndustriasUpdate);


          return $this->render('public/alloperatorsinfo.html.twig', array('prueba' => 'prueba'));
        }
    */

    /**
     * @Route("/update/getAllOperatorsIndustrias", name="update_all_operators_ganaderias")
     * @return Response
     */
    public function getAllOperatorsIndustrias()
    {
        $operatorsIndustrias = $this->getDoctrine()->getRepository(Operator::class)->findBy(
            array('opEntity' => 'Industrias', 'opEst' => 'C')
        );

        $toolsupdate = $this->get('toolsupdate');
        $gsbasexml = $this->get('gsbasexml');
        $gsbase = $this->get('gsbase');

        $countIndustrias = 0;
//        $countIndustriasUpdate = 0;
        foreach ($operatorsIndustrias as $operator) {
            //$operator =  $this->getDoctrine()->getRepository(Operator::class)->find(
            //$operatorcultivo->getIdOperator()
            //);
            $toolsupdate->getIndustriasOperator($gsbase, $gsbasexml, $operator);
//            $industriasOperator = $toolsupdate->getIndustriasOperator($gsbase, $gsbasexml, $operator);
//            dump($industriasOperator);
            $countIndustrias++;
        }

        $gsbase->gsbase_stop();
        print_r($countIndustrias);
        //print_r($countIndustriasUpdate);

        return $this->render('public/alloperatorsinfo.html.twig', array('prueba' => 'prueba'));
    }

    /**
     * @Route("/update/getAllOperatorsCultivosRecAux", name="update_all_operators_cultivos_rec_aux")
     * @return Response
     */
    public function getAllOperatorsCultivosRecAux()
    {
        // Obtenemos todos los operadores y los recorremos uno por uno para sacar su NOP-REG-SUBREG y sacar su info
        //$operators = $this->getDoctrine()->getRepository(Operator::class)->findBy(array('opSreg' => 'PAE'));
        $operators = $this->getDoctrine()->getRepository(Operator::class)->findAll();

        $countCultivosRec = 0;
        $countCultivosRecNo = 0;
        $em = $this->getDoctrine()->getManager();
        foreach ($operators as $operator) {
            $relation = $em->getRepository(RelationshipRegister::class)->getRelationByRegSreg(
                $operator->getOpReg(),
                $operator->getOpSreg()
            ); //[rlEntity] => Ganaderias [rlInfo] => PRODUCTOSECO

            if ($relation['rlEntity'] == 'CultivosRec' &&
                !$this->getDoctrine()->getRepository(CultivosRecAux::class)->findOneBy(
                    array('idOperator' => $operator->getId())
                )
            ) {
                $cultivosrecaux = new CultivosRecAux;
                $cultivosrecaux->setIdOperator($operator->getId());
                $cultivosrecaux->setNopOperator($operator->getOpNop());
                $cultivosrecaux->setCreateCultivo(false);
                $cultivosrecaux->setUpdateCultivo(false);
                $em->persist($cultivosrecaux);
                $em->flush();
                $countCultivosRec++;
            } else {
                $countCultivosRecNo++;
            }
            $em->clear();
        }
        //$gsbase->gsbase_stop();
//        dump($countCultivosRec);
//        dump($countCultivosRecNo);

        return $this->render('public/alloperatorsinfo.html.twig', array('prueba' => 'prueba'));
    }

    /**
     * @Route("/update/importcsv", name="import")
     * @return Response
     */
    public function updateRelationshipRegister()
    {
        $file_handle = fopen(__DIR__ . '/../../../web/uploads/importcsv.csv', "r");
        $em = $this->getDoctrine()->getManager();

        //$arr = array();
//        $line = fgetcsv($file_handle, 1024);
        while (!feof($file_handle)) {

            $line = fgetcsv($file_handle, 1024);

            //Previene de la inserción de filas en blanco en el final del csv
            if ($line) {
                $rlRegister = new RelationshipRegister($line[0], $line[1], $line[3], $line[4], $line[5], $line[6]);

                $em->persist($rlRegister);
                $em->flush();
                $em->clear();
                //array_push($arr,$line);
                //ld($line);
            }
        }

        fclose($file_handle);

        $allRelationshipRegisters = $this->getDoctrine()->getRepository(RelationshipRegister::class)->findAll();

        return $this->render(
            'public/rlregister.html.twig',
            array('prueba' => "Salida del csv", 'allRelationshipRegisters' => $allRelationshipRegisters)
        );
    }
}
