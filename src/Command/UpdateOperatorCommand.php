<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\Operator;
use App\Entity\UpdateLog;
use App\Entity\Register;


/**
 * Class UpdateOperatorCommand
 * @package App\Command
 */
class UpdateOperatorCommand extends Command
{
    protected static $defaultName = 'gsbase:update:operator';
    public function __construct(string $path_update_logs,$gsbase,$gsbasexml,$jms_serializer,$em)
    {
        $this->path_update_logs= $path_update_logs;
        $this->gsbase =$gsbase;
        $this->gsbasexml =$gsbasexml;
        $this->jms_serializer=$jms_serializer;
        $this->em = $em;
         // you *must* call the parent constructor
         parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('gsbase:update:operator')
            ->setDescription('Comando que actualiza la entidad Operator con la tabla operadores de GsBase');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $updateStart = date("H:i:s") . substr((string)microtime(), 1, 6);
        
        $urlBase = $this->path_update_logs;
        $path_file = $urlBase . 'update_' . date("d_m_Y") . '.log';
        #$path_file = __DIR__ . '/../../../app/logs/update/update_' . date("d_m_Y") . '.log';
        $log = fopen($path_file, "a+");
        $lines = file($path_file, FILE_SKIP_EMPTY_LINES);
        $lastLine = trim(array_pop($lines));

        if ($lastLine != 'SI') {
            exit();
        } else {
            fwrite($log, "\n");
            fwrite($log, "NO\n");
        }

        $gsbase = $this->gsbase;
        $gsbasexml = $this->gsbasexml;
        $xml = $gsbasexml->getXmlUpdateOperator();
	    $conex = -1;

        if ($gsbase->getGsbase() == null) {
            $output->writeln("No se ha podido conectar con el servidor de GsBase");
	        $conex = 0;
        }else{
		  $conex = 1;	
	}

        $xmlRes = $gsbase->gsbase_exec('consulta_xml', $xml, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );

        $operators = $this->jms_serializer->deserialize(
            $newXml,
            'App\Entity\RegistroOperator',
            'xml'
        );
       
        $em = $this->em;

        $operatorsCreated = 0;
        $operatorsUpdated = 0;
        $operatorsProcessed = 0;
	    $proceso = 0;
        $operatorsNoInsert = array(
            'FBR',
            'FCG',
            'FGE',
            'FHC',
            'FKO',
            'FOP', 
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
            'FBS',
            'VCA',
            'IDL',
            'IBR',
            'DL'
        );

	$datosxml = -1;
	if($xmlRes != null){
		$datosxml = 1;
	}else{
		$datosxml = 0;
	}
    
	//$datos = array("hola");
        /** @var Operator $operatorXml */
        foreach ($operators->Registro as $operatorXml) {
            
            $opReg = $operatorXml->getOpReg();

            $opSubreg = $operatorXml->getOpSreg();
            if ($opReg != null) {
                $register = $em->getRepository(Register::class)->findOneBy(array('codigo' => $opReg));
                $operatorXml->setOpRegistro($register);
            }
            if ($opSubreg != null) {
                $subregister = $em->getRepository(Register::class)->findOneBy(array('codigo' => $opSubreg));
                $operatorXml->setOpSubregistro($subregister);

                /** Atividad introducida pues no estan guardadas en gsBase.
                 * Deberian de estar en actividades-i pero no hay ninguna tabla intermedia con la cual relacionarla
                 */
                if ($opSubreg == 'VAE') {
                    $operatorXml->setOpAct('SEMILLEROS Y VIVEROS');
                } elseif ($opSubreg == 'IOZ') {
                    $operatorXml->setOpAct('ENVASADOR AZAFRAN DE LA MANCHA');
                } elseif ($opSubreg == 'FOP' || $opSubreg == 'SOP' || $opSubreg == 'IOP') {
                    $operatorXml->setOpAct('ENVASADOR PIMENTON MURCIA');
                } elseif ($opSubreg == 'IML') {
                    $operatorXml->setOpAct('ELABORACIÓN, ENVASADO Y COMERCIALIZACIÓN DE MELÓN');
                } elseif ($opSubreg == 'ICA') {
                    $operatorXml->setOpAct('INDUSTRIA CÁRNICA, SACRIFICIO, DESPIECE Y COMERCIALIZACIÓN');
                } elseif ($opSubreg == 'ICE') {
                    $operatorXml->setOpAct('ELABORACIÓN, ENVASADO Y COMERCIALIZACIÓN DE MELÓN');
                }
            }
            $operator = $em->getRepository(Operator::class)->findOneBy(
                array('codigo' => $operatorXml->getCodigo())
            );
            
	    //array_push($datos, $operator);

            if (!$operator) {


                if (!in_array($operatorXml->getOpSreg(), $operatorsNoInsert)) {
		//	$output->writeln("Código de producto NO Operativo"); // Si el valor del campo opSreg coincide con el de los Operadores que NO se Insertan no hacemos nada
		//}else{
                	$em->persist($operatorXml);
                	$operatorsCreated++;
			        $proceso = 1;
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
                    //$operator->setOpAct($operatorXml->getOpAct());
                    $operator->setOpPvcl($operatorXml->getOpPvcl());
                    $operator->setOpPbcl($operatorXml->getOpPbcl());
                    $operator->setOpFaud($operatorXml->getOpFaud());
                    $operator->setOpTecdeno($operatorXml->getOpTecdeno());
                    $operator->setOpEma($operatorXml->getOpEma());
                    $operator->setOpTecema($operatorXml->getOpTecema());
                    $operator->setOpClp($operatorXml->getOpClp());

                    $opReg = $operatorXml->getOpReg();
                    $opSubreg = $operatorXml->getOpSreg();
                    if ($opReg != null && $opReg != $operator->getOpReg()) {
                        $register = $em->getRepository(Register::class)->findOneBy(array('codigo' => $opReg));
                        $operator->setOpRegistro($register);
                    }
                    if ($opSubreg != $operator->getOpSreg()) {
                        $subregister = $em->getRepository(Register::class)->findOneBy(
                            array('codigo' => $opSubreg)
                        );
                        $operator->setOpSubregistro($subregister);
                    }
                    $operator->setOpReg($opReg);
                    $operator->setOpSreg($opSubreg);
                    $operatorsUpdated++;
		            $proceso = 2;
                }
            }
            //$em->getManager();
            $em->flush();
            $em->clear();
            $operatorsProcessed++;
	        $proceso = 3;
        }

        $updateStack = new UpdateLog();
        $em->persist($updateStack);
        $em->flush();

        $updateEnd = date("H:i:s") . substr((string)microtime(), 1, 6);

        fwrite(
            $log,
            ("OPERADORES => Comienzo: " . $updateStart . " | Final: " . $updateEnd .
                " | Registros Procesados: " . $operatorsProcessed . " | Registros Creados: "
                . $operatorsCreated . " | Registros Actualizados: " . $operatorsUpdated . " | Conexión Establecida: " . $conex . " | Datos XML: " . $datosxml) . "\n"
        );

	   /*foreach ($datos as $op){
		  fwrite(
			$log,
			(" Cod Operador: " . $op) . "\n"
		  );
	   }*/
        fwrite($log, "SI");
        fclose($log);
        return 0;
    }
}
