<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Tools;

use App\Entity\ActividadesI;
use App\Entity\AvesCorral;
use App\Entity\Cultivos;
use App\Entity\Especies;
use App\Entity\IAvesCorral;
use App\Entity\Industrias;
use App\Entity\ProductosG;
use App\Entity\Register;
use App\Entity\TiposCultivos;
use App\Entity\TiposProducc;
use App\Entity\TiposProducto;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Container;

use App\Entity\Client;
use App\GsBase\GsBaseXml;
use App\GsBase\GsBase;

use App\Entity\Operator;
use App\Entity\CultivosRec;
use App\Entity\CultivosRec2;
use App\Entity\UserOperator;
use App\Entity\Contact;
use App\Entity\Ganaderias;
use App\Entity\Productos;
use App\Entity\ProductosIndus;
use App\Entity\ProductosPae;

/**
 * Class ToolsUpdate
 * @package App\Tools
 */
class ToolsUpdate extends AbstractController
{

    protected $container;

    public function __construct(Container $c)
    {
        $this->container = $c;
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @param Operator $operator
     * @return array
     */
    public function getCultivoRecOperator(GsBase $gsbase, GsBaseXml $gsbasexml, Operator $operator)
    {
        $xmlCutlivosRec = $gsbasexml->getXmlUpdateCultivosRec($operator->getOpNop());
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xmlCutlivosRec, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $cultivosrecxml = $this->get('jms_serializer')->deserialize(
            $newXml,
            'App\Entity\RegistroCultivosRec',
            'xml'
        );

        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        $em = $this->entityManager;
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        /** @var CultivosRec $culrec */
        foreach ($cultivosrecxml->Registro as $culrec) {
            $registersProcessed++;
            $entity = $em->getRepository(CultivosRec::class)->findOneBy(
                array('codigo' => $culrec->getCodigo())
            );
            if (!$entity) {
                $culrec->setRuOperator(
                    $em->getRepository(Operator::class)->findOneBy(array('opNop' => $operator->getOpNop()))
                );
                $ruTiposProducto = $em->getRepository(TiposProducto::class)->findOneBy(
                    array('codigo' => $culrec->getRuTppr())
                );
                $culrec->setRuTiposProducto($ruTiposProducto);
                $ruProducto = $em->getRepository(Productos::class)->findOneBy(
                    array('codigo' => $culrec->getRuPro())
                );
                $culrec->setRuProducto($ruProducto);
                $ruCultivos = $em->getRepository(Cultivos::class)->findOneBy(
                    array('codigo' => $culrec->getRuCul())
                );
                $culrec->setRuCultivos($ruCultivos);

                $em->persist($culrec);
                $registersCreated++;

            } else {
                if ($em->getRepository(CultivosRec::class)->compareEntities($culrec, $entity)) {
                    $entity->setRuTppr($culrec->getRuTppr());
                    $entity->setRuPro($culrec->getRuPro());
                    $entity->setRuSitr($culrec->getRuSitr());
                    $entity->setRuCul($culrec->getRuCul());
                    $entity->setRuAct($culrec->getRuAct());
                    $entity->setRuRc($culrec->getRuRc());

                    $entity->setRuTiposProducto(
                        $em->getRepository(TiposProducto::class)->findOneBy(
                            array('codigo' => $culrec->getRuTppr())
                        )
                    );
                    $entity->setRuProducto(
                        $em->getRepository(Productos::class)->findOneBy(array('codigo' => $culrec->getRuPro()))
                    );
                    $entity->setRuCultivos(
                        $em->getRepository(Cultivos::class)->findOneBy(array('codigo' => $culrec->getRuCul()))
                    );

                    $registersUpdated++;

                }
            }
        }
        $em->flush();
        $em->getUnitOfWork()->clear();

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersProcessed' => $registersProcessed,
        );

    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @param Operator $operator
     * @return array
     */
    public function getCultivoRec2Operator(GsBase $gsbase, GsBaseXml $gsbasexml, Operator $operator)
    {
//        $cultivosrec = $this->getDoctrine()->getRepository('AppBundle:CultivosRec2')->findAll();
        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        $xmlCultivosRec2 = $gsbasexml->getXmlUpdateCultivosRec2($operator->getOpNop());
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xmlCultivosRec2, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $cultivosrec2xml = $this->get('jms_serializer')->deserialize(
            $newXml,
            'App\Entity\RegistroCultivosRec2',
            'xml'
        );

        // Eliminar entidades
        //$opproductospae = $em->getRepository('AppBundle:ProductosPae')->findByPipNop($operator->getOpNop());
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        /** @var CultivosRec2 $culrec2 */
        foreach ($cultivosrec2xml->Registro as $culrec2) {

            $entity = $em->getRepository(CultivosRec2::class)->findOneBy(array('codigo' => $culrec2->getCodigo()));
            if (!$entity) {
                $culrec2->setRu2Operator($operator);
                $ru2Cultivos = $em->getRepository(Cultivos::class)->findOneBy(
                    array('codigo' => $culrec2->getRu2Cul())
                );
                $culrec2->setRu2Cultivos($ru2Cultivos);
                $em->persist($culrec2);
                $registersCreated++;

            } else {
                if ($em->getRepository(CultivosRec2::class)->compareEntities($culrec2, $entity)) {
                    $entity->setRu2Pol($culrec2->getRu2Pol());
                    $entity->setRu2Sit($culrec2->getRu2Sit());
                    $entity->setRu2Par($culrec2->getRu2Par());
                    $entity->setRu2Cul($culrec2->getRu2Cul());
                    $entity->setRu2Rec($culrec2->getRu2Rec());
                    $entity->setRu2Cultivos(
                        $em->getRepository(Cultivos::class)->findOneBy(array('codigo' => $culrec2->getRu2Cul()))
                    );
                    $em->flush();
                    $registersUpdated++;
                }
            }
            $registersProcessed++;
        }

        $em->flush();
        //$em->clear();
        foreach ($em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $em->getEventManager()->removeEventListener($event, $listener);
            }
        }

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersProcessed' => $registersProcessed,
        );

    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @param Operator $operator
     * @return array
     */
    public function getGanaderiasOperator(GsBase $gsbase, GsBaseXml $gsbasexml, Operator $operator)
    {
        $xmlGanaderias = $gsbasexml->getXmlUpdateGanaderias($operator->getOpNop());
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xmlGanaderias, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $ganaderiasxml = $this->get('jms_serializer')->deserialize(
            $newXml,
            'App\Entity\RegistroGanaderias',
            'xml'
        );


        $em = $this->entityManager;
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        /** @var Ganaderias $gn */
        foreach ($ganaderiasxml->Registro as $gn) {

            $entity = $em->getRepository(Ganaderias::class)->findOneBy(array('codigo' => $gn->getCodigo()));
            if (!$entity) {
                $gn->setGnOperator($operator);
                $gnEspecies = $em->getRepository(Especies::class)->findOneBy(array('codigo' => $gn->getGnEsp()));
                $gn->setGnEspecies($gnEspecies);

                $gnTiposProducc = $em->getRepository(TiposProducc::class)->findOneBy(
                    array('codigo' => $gn->getGnTpn())
                );
                $gn->setGnTiposProducc($gnTiposProducc);
                $gnProductosG = $em->getRepository(ProductosG::class)->findOneBy(
                    array('codigo' => $gn->getGnPro())
                );
                $gn->setGnProductosG($gnProductosG);
                $em->persist($gn);
                $registersCreated++;

            } else {
                if ($em->getRepository(Ganaderias::class)->compareEntities($gn, $entity)) {

                    $entity->setGnRc($gn->getGnRc());
                    $entity->setGnEsp($gn->getGnEsp());
                    $entity->setGnTpn($gn->getGnTpn());
                    $entity->setGnPro($gn->getGnPro());
                    $entity->setGnTpcu($gn->getGnTpcu());
                    $entity->setGnRaza($gn->getGnRaza());
                    $entity->setGnUcap($gn->getGnUcap());

                    $entity->setGnEspecies(
                        $em->getRepository(Especies::class)->findOneBy(array('codigo' => $gn->getGnEsp()))
                    );
                    $entity->setGnTiposProducc(
                        $em->getRepository(TiposProducc::class)->findOneBy(array('codigo' => $gn->getGnTpn()))
                    );
                    $entity->setGnProductosG(
                        $em->getRepository(ProductosG::class)->findOneBy(array('codigo' => $gn->getGnPro()))
                    );
                    $registersUpdated++;
                    $em->flush();
                }
            }
            $registersProcessed++;
        }
        $em->flush();
        //$em->clear();
        foreach ($em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $em->getEventManager()->removeEventListener($event, $listener);
            }
        }

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersProcessed' => $registersProcessed,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @param Operator $operator
     * @return array
     */
    public function getProductosPaeOperator(GsBase $gsbase, GsBaseXml $gsbasexml, Operator $operator)
    {
        $xmlProductosPae = $gsbasexml->getXmlUpdateProductosPae($operator->getOpNop());
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xmlProductosPae, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $productospaexml = $this->get('jms_serializer')->deserialize(
            $newXml,
            'App\Entity\RegistroProductosPae',
            'xml'
        );
        #var_dump($productospaexml);
        #exit;
        $em = $this->entityManager;
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        // Eliminar entidades
        //$opproductospae = $em->getRepository('AppBundle:ProductosPae')->findByPipNop($operator->getOpNop());
        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        /** @var ProductosPae $ppae */
        foreach ($productospaexml->Registro as $ppae) {

            $entity = $em->getRepository(ProductosPae::class)->findOneBy(array('codigo' => $ppae->getCodigo()));
            if (!$entity) {
                $ppae->setPipOperator($operator);
                $em->persist($ppae);
                $registersCreated++;
            } else {
                if ($em->getRepository(ProductosPae::class)->compareEntities($ppae, $entity)) {
                    $entity->setPipPro($ppae->getPipPro());
                    $entity->setPipEst($ppae->getPipEst());
                    $entity->setPipDsc($ppae->getPipDsc());
                    $entity->setPipTpp($ppae->getPipTpp());
                    $em->flush();
                    $registersUpdated++;
                }
            }
            $registersProcessed++;
        }
        $em->flush();
        //$em->clear();
        foreach ($em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $em->getEventManager()->removeEventListener($event, $listener);
            }
        }

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersProcessed' => $registersProcessed,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @param Operator $operator
     * @return array
     */
    public function getProductosIndusOperator(GsBase $gsbase, GsBaseXml $gsbasexml, Operator $operator)
    {
        $xmlProductosIndus = $gsbasexml->getXmlUpdateProductosIndus($operator->getOpNop());
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xmlProductosIndus, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $productosindusxml = $this->get('jms_serializer')->deserialize(
            $newXml,
            'App\Entity\RegistroProductosIndus',
            'xml'
        );

        $em = $this->entityManager;
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        /** @var ProductosIndus $pp */
        foreach ($productosindusxml->Registro as $pp) {
            $entity = $em->getRepository(ProductosIndus::class)->findOneBy(array('codigo' => $pp->getCodigo()));
            if (!$entity) {
                $pp->setPiOperator($operator);
                $piProductos = $em->getRepository(Productos::class)->findOneBy(array('codigo' => $pp->getPiPro()));
                $pp->setPiProductos($piProductos);
                $em->persist($pp);
                $registersCreated++;
            } else {
                if ($em->getRepository(ProductosIndus::class)->compareEntities($pp, $entity)) {
//                    dump('update - Registro: ' . $entity->getId());
                    $entity->setPiPro($pp->getPiPro());
                    $entity->setPiMarca($pp->getPiMarca());
                    $entity->setPiDpro($pp->getPiDpro());
                    $entity->setPiCpro($pp->getPiCpro());
                    $em->flush();
                    $registersUpdated++;
                }
            }
            $registersProcessed++;
        }

        $em->flush();
        //$em->clear();
        foreach ($em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $em->getEventManager()->removeEventListener($event, $listener);
            }
        }

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersProcessed' => $registersProcessed,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @param Operator $operator
     * @return array
     */
    public function getAvesCorralOperator(GsBase $gsbase, GsBaseXml $gsbasexml, Operator $operator)
    {
        $xmlAvesCorral = $gsbasexml->getXmlUpdateAvesCorral($operator->getOpNop());
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xmlAvesCorral, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $avesCorralXml = $this->get('jms_serializer')->deserialize(
            $newXml,
            'App\Entity\RegistroAvesCorral',
            'xml'
        );

        $em = $this->entityManager;
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        /** @var AvesCorral $avc */
        foreach ($avesCorralXml->Registro as $avc) {
            $entity = $em->getRepository(AvesCorral::class)->findOneBy(array('codigo' => $avc->getCodigo()));
            if (!$entity) {
                $avc->setAvcOperator($operator);
                $em->persist($avc);
                $em->flush();
                $registersCreated++;
            } else {
                if ($em->getRepository(AvesCorral::class)->compareEntities($avc, $entity)) {
                    $entity->setAvcEsp($avc->getAvcEsp());
                    $entity->setAvcTpn($avc->getAvcTpn());
                    $em->flush();
                    $registersUpdated++;
                }
            }
            $registersProcessed++;
        }
        $em->flush();
        //$em->clear();
        foreach ($em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $em->getEventManager()->removeEventListener($event, $listener);
            }
        }

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersProcessed' => $registersProcessed,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @param Operator $operator
     * @return null
     */
    public function getIAvesCorralOperator(GsBase $gsbase, GsBaseXml $gsbasexml, Operator $operator)
    {
        $xmlIAvesCorral = $gsbasexml->getXmlUpdateIAvesCorral($operator->getOpNop());
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xmlIAvesCorral, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $iAvesCorralXml = $this->get('jms_serializer')->deserialize(
            $newXml,
            'App\Entity\RegistroIAvesCorral',
            'xml'
        );

        $em = $this->entityManager;
        $id = null;

        /** @var IAvesCorral $avi */
        foreach ($iAvesCorralXml->Registro as $avi) {

            $entity = $em->getRepository(IAvesCorral::class)->findOneBy(array('codigo' => $avi->getCodigo()));
            if (!$entity) {
                $avi->setAviOperator($operator);
                $em->persist($avi);
                $em->flush();
                $id++;
            } else {
                if ($em->getRepository(IAvesCorral::class)->compareEntities($avi, $entity)) {
                    $entity->setAviPrd($avi->getAviPrd());
                    $entity->setAviVar($avi->getAviVar());
                    $entity->setAviInd($avi->getAviInd());
                    $entity->setAviMar($avi->getAviMar());
                    $em->flush();
                }
            }
        }
        $em->flush();
        //$em->clear();
        foreach ($em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $em->getEventManager()->removeEventListener($event, $listener);
            }
        }

        return $id;
    }

    /*
     * Carga y update de tablas no dependientes de Operator
     */

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @return array
     */
    public function getProductos(GsBase $gsbase, GsBaseXml $gsbasexml)
    {

        $xml = $gsbasexml->getXmlUpdateProductos();
        $xmlRes = $gsbase->gsbase_exec('consulta_xml', $xml, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $registers = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroProductos', 'xml');

        $em = $this->entityManager;
        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;
        #var_dump($registers);
        #exit;
        /** @var Productos $registerXml */
        foreach ($registers->Registro as $registerXml) {
            $ptTc = $registerXml->getPtTc();
            $ptCu = $registerXml->getPtCu();
            $ptTi = $registerXml->getPtTi();

            if ($ptTc != null) {
                $tipoCultivo = $this->getDoctrine()->getRepository(TiposCultivos::class)->findOneBy(
                    array('codigo' => $ptTc)
                );
                $registerXml->setPtTipoCultivo($tipoCultivo);
            }
            if ($ptCu != null) {
                $cultivo = $this->getDoctrine()->getRepository(Cultivos::class)->findOneBy(
                    array('codigo' => $ptCu)
                );
                $registerXml->setPtCultivos($cultivo);
            }
            if ($ptTi != null) {
                $tiposProducto = $this->getDoctrine()->getRepository(TiposProducto::class)->findOneBy(
                    array('codigo' => $ptTi)
                );
                $registerXml->setPtTiposProducto($tiposProducto);
            }

            /** @var Productos $register */
            $register = $this->getDoctrine()->getRepository(Productos::class)->findOneBy(
                array('codigo' => $registerXml->getCodigo())
            );

            /*
            if(strtoupper($registerXml->getPtDeno()) == $registerXml->getPtDeno()){
                $registerXml->setPtEst('L');
            }else{
                $registerXml->setPtEst('B');
            }
            */

            if (!$register) {
                if (strtoupper($registerXml->getPtDeno()) == $registerXml->getPtDeno()) {
                    $registerXml->setPtEst('L');
                    $em->persist($registerXml);
                    $em->flush();
                    $em->clear();
                    $registersCreated++;
                }
            } else {
                $updateEntity = $em->getRepository(Productos::class)->compareEntities($registerXml, $register);
                if ($updateEntity) {
                    
                    $register->setPtDeno($registerXml->getPtDeno());
                    $register->setPtTc($registerXml->getPtTc());
                    $register->setPtCu($registerXml->getPtCu());
                    $register->setPtTi($registerXml->getPtTi());
                    //$register->setPtEst($registerXml->getPtEst());
                    $register->setPtTipoCultivo(
                        $em->getRepository('AppBundle:TiposCultivos')->findOneBy(
                            array('codigo' => $registerXml->getPtTc())
                        )
                    );
                    $register->setPtTiposProducto(
                        $em->getRepository('AppBundle:TiposProducto')->findOneBy(
                            array('codigo' => $registerXml->getPtTi())
                        )
                    );
                    $register->setPtCultivos(
                        $em->getRepository('AppBundle:Cultivos')->findOneBy(array('codigo' => $registerXml->getPtCu()))
                    );
                    $em->flush();
                    $em->clear();
                    $registersUpdated++;
                }
            }
            $registersProcessed++;
        }

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersProcessed' => $registersProcessed,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @return array
     */
    public function getTiposCultivos(GsBase $gsbase, GsBaseXml $gsbasexml)
    {
        $xml = $gsbasexml->getXmlUpdateTiposCultivos();

        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xml, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $registers = $this->get('jms_serializer')->deserialize(
            $newXml,
            'App\Entity\RegistroTiposCultivos',
            'xml'
        );

        $em = $this->entityManager;
        $registersCreated = array();
        $registersUpdated = array();
        $registersNotUpdated = array();

        /** @var TiposCultivos $registerXml */
        foreach ($registers->Registro as $registerXml) {
            $register = $this->getDoctrine()->getRepository('AppBundle:TiposCultivos')->findOneBy(
                array('codigo' => $registerXml->getCodigo())
            );
            if (!$register) {
                $em->persist($registerXml);
                array_push($registersCreated, $registerXml);
            } else {
                $updateEntity = $em->getRepository('AppBundle:TiposCultivos')->compareEntities($registerXml, $register);
                if ($updateEntity) {
                    $register->setTcDeno($registerXml->getTcDeno());
                    $register->setTcRoae($registerXml->getTcRoae());
                    array_push($registersUpdated, $register);
                } else {
                    array_push($registersNotUpdated, $register->getId() . " - " . $register->getCodigo());
                }
            }
        }
        $em->flush();
        $em->clear();

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersNotUpdated' => $registersNotUpdated,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @return array
     */
    public function getTiposProducto(GsBase $gsbase, GsBaseXml $gsbasexml)
    {
        $xml = $gsbasexml->getXmlUpdateTiposProducto();
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xml, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $registers = $this->get('jms_serializer')->deserialize(
            $newXml,
            'App\Entity\RegistroTiposProducto',
            'xml'
        );

        $em = $this->entityManager;
        $registersCreated = array();
        $registersUpdated = array();
        $registersNotUpdated = array();

        /** @var TiposProducto $registerXml */
        foreach ($registers->Registro as $registerXml) {
            $register = $this->getDoctrine()->getRepository('AppBundle:TiposProducto')->findOneBy(
                array('codigo' => $registerXml->getCodigo())
            );
            if (!$register) {
                $em->persist($registerXml);
                array_push($registersCreated, $registerXml);
            } else {
                $updateEntity = $em->getRepository('AppBundle:TiposProducto')->compareEntities($registerXml, $register);
                if ($updateEntity) {
                    $register->setTiDeno($registerXml->getTiDeno());
                    array_push($registersUpdated, $register);
                } else {
                    array_push($registersNotUpdated, $register->getId() . " - " . $register->getCodigo());
                }
            }
        }
        $em->flush();
        $em->clear();

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersNotUpdated' => $registersNotUpdated,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @return array
     */
    public function getCultivos(GsBase $gsbase, GsBaseXml $gsbasexml)
    {
        $xml = $gsbasexml->getXmlUpdateCultivos();
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xml, 'consulta-xml');

        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $registers = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroCultivos', 'xml');

        $em = $this->entityManager;
        $registersCreated = array();
        $registersUpdated = array();
        $registersNotUpdated = array();

        /** @var Cultivos $registerXml */
        foreach ($registers->Registro as $registerXml) {
            $register = $this->getDoctrine()->getRepository('AppBundle:Cultivos')->findOneBy(
                array('codigo' => $registerXml->getCodigo())
            );
            if (!$register) {
                $em->persist($registerXml);
                array_push($registersCreated, $registerXml);
            } else {
                $updateEntity = $em->getRepository('AppBundle:Cultivos')->compareEntities($registerXml, $register);
                if ($updateEntity) {
                    $register->setCuDeno($registerXml->getCuDeno());
                    $register->setCuRoae($registerXml->getCuRoae());
                    array_push($registersUpdated, $register);
                } else {
                    array_push($registersNotUpdated, $register->getId() . " - " . $register->getCodigo());
                }
            }
        }

        $em->flush();
        $em->clear();

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersNotUpdated' => $registersNotUpdated,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @return array
     */
    public function getEspecies(GsBase $gsbase, GsBaseXml $gsbasexml)
    {
        $xml = $gsbasexml->getXmlUpdateEspecies();
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xml, 'consulta-xml');

        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $registers = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroEspecies', 'xml');

        $em = $this->entityManager;
        $registersCreated = array();
        $registersUpdated = array();
        $registersNotUpdated = array();

        /** @var Especies $registerXml */
        foreach ($registers->Registro as $registerXml) {
            $register = $this->getDoctrine()->getRepository('AppBundle:Especies')->findOneBy(
                array('codigo' => $registerXml->getCodigo())
            );
            if (!$register) {
                $em->persist($registerXml);
                array_push($registersCreated, $registerXml);
            } else {
                $updateEntity = $em->getRepository('AppBundle:Especies')->compareEntities($registerXml, $register);
                if ($updateEntity) {
                    $register->setEsDeno($registerXml->getEsDeno());
                    array_push($registersUpdated, $register);
                } else {
                    array_push($registersNotUpdated, $register->getId() . " - " . $register->getCodigo());
                }
            }
        }

        $em->flush();
        $em->clear();

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersNotUpdated' => $registersNotUpdated,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @return array
     */
    public function getProductosG(GsBase $gsbase, GsBaseXml $gsbasexml)
    {
        $xml = $gsbasexml->getXmlUpdateProductosG();
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xml, 'consulta-xml');

        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $registers = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroProductosG', 'xml');

        $em = $this->entityManager;
        $registersCreated = array();
        $registersUpdated = array();
        $registersNotUpdated = array();

        /** @var ProductosG $registerXml */
        foreach ($registers->Registro as $registerXml) {
            $register = $this->getDoctrine()->getRepository('AppBundle:ProductosG')->findOneBy(
                array('codigo' => $registerXml->getCodigo())
            );
            if (!$register) {
                $registerXml->setPnEst('L');
                $em->persist($registerXml);
                array_push($registersCreated, $registerXml);

            } else {
                $updateEntity = $em->getRepository('AppBundle:ProductosG')->compareEntities($registerXml, $register);
                if ($updateEntity) {
                    $register->setPnDeno($registerXml->getPnDeno());
                    array_push($registersUpdated, $register);
                } else {
                    array_push($registersNotUpdated, $register->getId() . " - " . $register->getCodigo());
                }
            }
        }

        $em->flush();
        $em->clear();

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersNotUpdated' => $registersNotUpdated,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @return array
     */
    public function getTiposProducc(GsBase $gsbase, GsBaseXml $gsbasexml)
    {
        $xml = $gsbasexml->getXmlUpdateTiposProducc();
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xml, 'consulta-xml');

        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $registers = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroTiposProducc', 'xml');

        $em = $this->entityManager;
        $registersCreated = array();
        $registersUpdated = array();
        $registersNotUpdated = array();

        /** @var TiposProducc $registerXml */
        foreach ($registers->Registro as $registerXml) {
            $register = $this->getDoctrine()->getRepository('AppBundle:TiposProducc')->findOneBy(
                array('codigo' => $registerXml->getCodigo())
            );
            if (!$register) {
                $em->persist($registerXml);
                array_push($registersCreated, $registerXml);
            } else {
                $updateEntity = $em->getRepository('AppBundle:TiposProducc')->compareEntities($registerXml, $register);
                if ($updateEntity) {
                    $register->setTpnDeno($registerXml->getTpnDeno());
                    array_push($registersUpdated, $register);
                } else {
                    array_push($registersNotUpdated, $register->getId() . " - " . $register->getCodigo());
                }
            }
        }

        $em->flush();
        $em->clear();

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersNotUpdated' => $registersNotUpdated,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @return array
     */
    public function getActividadesI(GsBase $gsbase, GsBaseXml $gsbasexml)
    {
        $xml = $gsbasexml->getXmlUpdateActividadesI();
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xml, 'consulta-xml');

        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $registers = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroActividadesI', 'xml');

        $em = $this->entityManager;
        $registersCreated = array();
        $registersUpdated = array();
        $registersNotUpdated = array();

        /** @var ActividadesI $registerXml */
        foreach ($registers->Registro as $registerXml) {
            $register = $this->getDoctrine()->getRepository('AppBundle:ActividadesI')->findOneBy(
                array('codigo' => $registerXml->getCodigo())
            );
            if (!$register) {
                $em->persist($registerXml);
                array_push($registersCreated, $registerXml);
            } else {
                $updateEntity = $em->getRepository('AppBundle:ActividadesI')->compareEntities($registerXml, $register);
                if ($updateEntity) {
                    $register->setAinDeno($registerXml->getAinDeno());
                    array_push($registersUpdated, $register);
                } else {
                    array_push($registersNotUpdated, $register->getId() . " - " . $register->getCodigo());
                }
            }
        }

        $em->flush();
        $em->clear();

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersNotUpdated' => $registersNotUpdated,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @param Operator $operator
     * @return array
     */
    public function getIndustriasOperator(GsBase $gsbase, GsBaseXml $gsbasexml, Operator $operator)
    {
        $opCcl = $operator->getOpCcl();
        if (strlen($opCcl) < 5) {
            $opCcl = str_pad($operator->getOpCcl(), 5, "0", STR_PAD_LEFT);
        }

        $xmlIndustrias = $gsbasexml->getXmlUpdateIndustriasCcl($opCcl);
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xmlIndustrias, 'consulta-xml');

        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $industriasxml = $this->get('jms_serializer')->deserialize(
            $newXml,
            'App\Entity\RegistroIndustrias',
            'xml'
        );

        $em = $this->entityManager;
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        /** @var Industrias $in */
        foreach ($industriasxml->Registro as $in) {

            $reg = $this->getDoctrine()->getRepository('AppBundle:Industrias')->findBy(
                array('codigo' => $in->getCodigo(), 'inNop' => $operator->getOpNop(), 'inAct' => $in->getInAct())
            );

            if (!$reg) {
                /* La relacion de operadores con Industrias tiene que
                 ir a traves del ID de clientes y no del numero de operador*/
                $in->setInOperator(
                    $em->getRepository(Operator::class)->findOneBy(array('opNop' => $operator->getOpNop()))
                );
                $in->setInNop($operator->getOpNop());

                $inActividadesI = $em->getRepository('AppBundle:ActividadesI')->findOneBy(
                    array('codigo' => $in->getInAct())
                );
                $in->setInActividadI($inActividadesI);

                $em->persist($in);
                $registersCreated++;

            } else {
                $entity = $em->getRepository('AppBundle:Industrias')->find($reg[0]->getId());

                if ($em->getRepository('AppBundle:Industrias')->compareEntities($in, $entity)) {

                    $entity->setInAct($in->getInAct());
                    $entity->setInSit($in->getInSit());
                    $entity->setInCdp($in->getInCdp());
                    $entity->setInDom($in->getInDom());
                    $entity->setInTel($in->getInTel());
                    $entity->setInProv($in->getInProv());
                    $entity->setInPob($in->getInPob());

                    $entity->setInActividadI(
                        $em->getRepository('AppBundle:ActividadesI')->findOneBy(array('codigo' => $in->getInAct()))
                    );
                    //print_r($entity->getId().'/n');
                    $em->flush();
                    $registersUpdated++;
                }
            }
            $registersProcessed++;
        }
        $em->flush();
        $em->clear();

        foreach ($em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $em->getEventManager()->removeEventListener($event, $listener);
            }
        }

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersProcessed' => $registersProcessed,
        );

    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @param Operator $operator
     * @return array
     */
    public function getCultivoRecBisOperator(GsBase $gsbase, GsBaseXml $gsbasexml, Operator $operator)
    {
        $xmlCutlivosRec = $gsbasexml->getXmlUpdateCultivosRec($operator->getOpNop());
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xmlCutlivosRec, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $cultivosrecxml = $this->get('jms_serializer')->deserialize(
            $newXml,
            'App\Entity\RegistroCultivosRec',
            'xml'
        );

        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        $em = $this->entityManager;
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        foreach ($em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $em->getEventManager()->removeEventListener($event, $listener);
            }
        }

        /** @var CultivosRec $culrec */
        foreach ($cultivosrecxml->Registro as $culrec) {
            $registersProcessed++;
            $entity = $em->getRepository('AppBundle:CultivosRec')->findOneBy(
                array('codigo' => $culrec->getCodigo())
            );
            if (!$entity) {
                $culrec->setRuOperator(
                    $em->getRepository(Operator::class)->findOneBy(array('opNop' => $operator->getOpNop()))
                );
                $ruTiposProducto = $em->getRepository('AppBundle:TiposProducto')->findOneBy(
                    array('codigo' => $culrec->getRuTppr())
                );
                $culrec->setRuTiposProducto($ruTiposProducto);
                $ruProducto = $em->getRepository('AppBundle:Productos')->findOneBy(
                    array('codigo' => $culrec->getRuPro())
                );
                $culrec->setRuProducto($ruProducto);
                $ruCultivos = $em->getRepository('AppBundle:Cultivos')->findOneBy(
                    array('codigo' => $culrec->getRuCul())
                );
                $culrec->setRuCultivos($ruCultivos);

                $em->persist($culrec);
                $registersCreated++;

            } else {
                if ($em->getRepository('AppBundle:CultivosRec')->compareEntities($culrec, $entity)) {
                    $entity->setRuTppr($culrec->getRuTppr());
                    $entity->setRuPro($culrec->getRuPro());
                    $entity->setRuSitr($culrec->getRuSitr());
                    $entity->setRuCul($culrec->getRuCul());
                    $entity->setRuAct($culrec->getRuAct());
                    $entity->setRuRc($culrec->getRuRc());
                    $entity->setRuTiposProducto(
                        $em->getRepository('AppBundle:TiposProducto')->findOneBy(
                            array('codigo' => $culrec->getRuTppr())
                        )
                    );
                    $entity->setRuProducto(
                        $em->getRepository('AppBundle:Productos')->findOneBy(array('codigo' => $culrec->getRuPro()))
                    );
                    $entity->setRuCultivos(
                        $em->getRepository('AppBundle:Cultivos')->findOneBy(array('codigo' => $culrec->getRuCul()))
                    );
                    $registersUpdated++;
                }
            }
        }
        $em->flush();
        $em->getUnitOfWork()->clear();

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersProcessed' => $registersProcessed,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @param UserOperator $user
     * @param string $opCcl
     * @return array
     */
    public function getClient(GsBase $gsbase, GsBaseXml $gsbasexml, UserOperator $user, $opCcl)
    {
        $xmlClient = $gsbasexml->getXmlUpdateClient($opCcl);
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xmlClient, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $clientxml = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroClient', 'xml');

        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        foreach ($em->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $em->getEventManager()->removeEventListener($event, $listener);
            }
        }

        /** @var Client $client */
        foreach ($clientxml->Registro as $client) {
            $registersProcessed++;

            /** @var Client $entity */
            $entity = $em->getRepository('AppBundle:Client')->findOneBy(array('codigo' => $client->getCodigo()));
            if (!$entity) {
                $em->persist($client);
                $registersCreated++;
                $em->flush();
                $user->setClientId($client);
                $em->flush();

            } else {
                if ($em->getRepository('AppBundle:Client')->compareEntities($client, $entity)) {
                    $entity->setClDeno($client->getClDeno());
                    $entity->setClCif($client->getClCif());
                    $entity->setClCdp($client->getClCdp());
                    $entity->setClDom($client->getClDom());
                    $entity->setClProv($client->getClProv());
                    $entity->setClPob($client->getClPob());
                    $entity->setClPais($client->getClPais());
                    $entity->setClTel($client->getClTel());
                    $entity->setClFax($client->getClFax());
                    $entity->setClEma($client->getClEma());
                    $entity->setClActi($client->getClActi());

                    $registersUpdated++;
                    $em->flush();
                }
            }
        }
//        $em->getUnitOfWork()->clear();

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersProcessed' => $registersProcessed,
        );
    }

    /**
     * Actualizador de cliente
     *
     * Dado un codigo gsBase de Cliente compara y actualiza los datos si procede.
     *
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @param Client $client
     * @return array
     */
    public function updateClient(GsBase $gsbase, GsBaseXml $gsbasexml, Client $client)
    {
        $xmlClient = $gsbasexml->getXmlUpdateClient($client->getCodigo());
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xmlClient, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $clientxml = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroClient', 'xml');

        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        $em = $this->entityManager;

        /** @var Client $clientXml */
        foreach ($clientxml->Registro as $clientXml) {
            $registersProcessed++;

            if ($em->getRepository('AppBundle:Client')->compareEntities($client, $clientXml)) {
                $client->setClDeno($clientXml->getClDeno());
                $client->setClCif($clientXml->getClCif());
                $client->setClCdp($clientXml->getClCdp());
                $client->setClDom($clientXml->getClDom());
                $client->setClProv($clientXml->getClProv());
                $client->setClPob($clientXml->getClPob());
                $client->setClPais($clientXml->getClPais());
                $client->setClTel($clientXml->getClTel());
                $client->setClFax($clientXml->getClFax());
                $client->setClEma($clientXml->getClEma());
                $client->setClActi($clientXml->getClActi());

                $registersUpdated++;
                $em->flush();
            }

        }

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersProcessed' => $registersProcessed,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @param Client $client
     * @return array
     */
    public function getContact(GsBase $gsbase, GsBaseXml $gsbasexml, Client $client)
    {
        $xmlContact = $gsbasexml->getXmlRetrieveContact($client->getCodigo());
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xmlContact, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $contactxml = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroContact', 'xml');
        $registersProcessed = 0;
        $registersCreated = 0;
        $registersUpdated = 0;

        $em = $this->entityManager;

        /** @var ArrayCollection $contacts */
        $contacts = $contactxml->Registro;
        if ($contacts->count() > 0) {
            /** @var Contact $contact */
            $contact = $contacts->first();
            /** @var Contact $entity */
            $entity = $em->getRepository('AppBundle:Contact')->findOneBy(array('codigo' => $contact->getCodigo()));
            if (!$entity) {
                $em->persist($contact);
                $em->flush();
                $client->setContact($contact);
                $em->flush();
                $registersCreated++;
            } else {
                if ($em->getRepository('AppBundle:Contact')->compareEntities($contact, $entity)) {
                    $entity->setCnDeno($contact->getCnDeno());
                    $entity->setCnApe1($contact->getCnApe1());
                    $entity->setCnApe2($contact->getCnApe2());
                    $registersUpdated++;
                    $em->flush();
                }
            }
        }

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersProcessed' => $registersProcessed,
        );
    }

    /**
     * @param GsBase $gsbase
     * @param GsBaseXml $gsbasexml
     * @return array
     */
    public function updateRegister(GsBase $gsbase, GsBaseXml $gsbasexml)
    {
        $xml = $gsbasexml->getXmlUpdateRegistry();

        $xmlRes = $gsbase->gsbase_exec('consulta_xml', $xml, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $registers = $this->get('jms_serializer')->deserialize(
            $newXml,
            'App\Entity\RegistroRegister',
            'xml'
        );
        $em = $this->entityManager;
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
                array_push($registersCreated, $registerXml);
            } else {
                $updateEntity = $em->getRepository(Register::class)->compareEntities($registerXml, $register);
                if ($updateEntity) {
                    $register->setReDeno($registerXml->getReDeno());
                    $register->setReTipo($registerXml->getReTipo());
                    $register->setRePad($registerXml->getRePad());
                    $register->setReAct($registerXml->getReAct());
                    array_push($registersUpdated, $register);
                } else {
                    array_push($registersNotUpdated, $register->getId() . " - " . $register->getCodigo());
                }
            }
        }
        $em->flush();
        $em->clear();

        return array(
            'registersCreated' => $registersCreated,
            'registersUpdated' => $registersUpdated,
            'registersNotUpdated' => $registersNotUpdated,
        );
    }
}
