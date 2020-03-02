<?php

/*
 * Copyright (c) 2018-2019.
 *
 * Desarrollado por Atlantic International Technology para Sohiscert
 *
 * Este fichero utiliza como base el archivo SendEmailCommand de swiftmailer-bundle
 * localizado en vendor/symfony/swiftmailer-bundle/Command/
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder; 

use App\Entity\DocumentosFTP;
use App\Entity\Operator;
use Symfony\Component\Finder\Finder;

/**
 * Class EmailUpdateDocConcluCommand
 * @package App\Command
 */
class EmailUpdateDocConcluCommand extends Command 
{
    protected static $defaultName = 'email:emaildoccon:send';
    public function __construct()
    {
        $this->ftp = new Finder();
         # Datos Conexión FTP para poder Obtener Fecha Modificación de los Archivos
        $this->ftp_server = 'sohiscert3.ddns.cyberoam.com';
        $this->ftp_user_name = 'userftp1';
        $this->ftp_user_pass = 'AtlIntTec.12';
         // you *must* call the parent constructor
         parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('email:emaildoccon:send')
            ->setDescription('Send simple email message')
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'The from address of the message')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'The to address of the message')
            ->addOption('subject', null, InputOption::VALUE_REQUIRED, 'The subject of the message')
            ->addOption('body', null, InputOption::VALUE_REQUIRED, 'The body of the message')
            ->addOption('mailer', null, InputOption::VALUE_REQUIRED, 'The mailer name', 'mailer_mail')
            ->addOption('content-type', null, InputOption::VALUE_REQUIRED, 'The body content type of the message', 'text/html')
            ->addOption('charset', null, InputOption::VALUE_REQUIRED, 'The body charset of the message', 'UTF8')
            ->addOption('body-source', null, InputOption::VALUE_REQUIRED, 'The source where body come from [stdin|file]', 'stdin')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command creates and send simple email message.

<info>php %command.full_name% --mailer=custom_mailer --content-type=text/xml</info>

You can get body of message from file:
<info>php %command.full_name% --body-source=file --body=/path/to/file</info>

EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) :int
    {
        # Definimos Variable de Cominezo de Ejecución 
        $now = date("Y-m-d H:i:s");

        # Definimos e Inicializamos Contadores de
        # Facturas, conclusioness, Certificados, Análisis y Mails a Remitir
        $facNew = 0;
        $facUpdate = 0;
        $facSO = 0;
        $totalFact = 0;
        $procFact = 0;
        $docNVFact = 0;

        $carNew = 0;
        $carUpdate = 0;
        $carSO = 0;
        $totalCart = 0;
        $procCart = 0;
        $docNVCart = 0;

        $cerNew = 0;
        $cerUpdate = 0;
        $cerSO = 0;
        $totalCert = 0;
        $procCert = 0;
        $docNVCert = 0;

        $anaNew = 0;
        $anaUpdate = 0;
        $anaSO = 0;
        $procAna = 0;
        $docNVAna = 0;

        $contMail = 0;

        # Rutas de los Documentos en el Servidor FTP
        #$rutasftp = array('factura' => '/facturasintranet', 'certificado' => '/SITIO2', 'conclusiones' => '/SITIO3', 'analisis' => "/SITIO1");


        # Obtener Fechas, Día Actual y Día de la Semana Atrás desde el día actual
        $diahoy = date('Y-m-d', time());
        $diahoy = strtotime($diahoy);

        $semantes = '2019-01-22';
        #$semantes = date('Y-m-d', strtotime('-1 week'));
        $semantes = strtotime($semantes);

        
        # Rutas para Pruebas
        $rutasftp = array('conclusiones' => '/sitio4CON');
        #$rutasftp = array('factura' => '/facturasintranet');
        $em = new ContainerBuilder();

        #MNN Creamos el archivo update de reccorridos de archivos de certificados
        $urlBase = $em->getParameter('path_update_logs');

        # Definimos Variable de Fin de Ejecución 
        $end = date("Y-m-d H:i:s");

        # Definimos la Ruta Completa y el Nombre del Fichero LOG que se va a generar
        $path_file = $urlBase.'register_recorridos_CONCLUSIONES_'.date("d_m_Y").'.log';

        # Abrimos el Archivo con Permisos de Sobrescritura
        $log = fopen($path_file, "w+");

        fwrite($log,("\n* ARCHIVOS DE conclusionesS\n"));

        #MNN

        # Recorremos los Directorios FTP definidos anteriormente en las rutas
        # Definimos la Ruta
        foreach ($rutasftp as $tipodoc => $ruta) {

            # Establecemos Conexión FTP
            $this->finder->files()->in("ftp://$this->ftp_user_name:$this->ftp_user_pass@$this->ftp_server")->name($ruta.'*.pdf'); 

            # Verificamos la Conexión FTP
            if (!$this->finder->hasResults()) {  
                echo "\n ¡La conexión FTP ha fallado!\n";
                echo "\n Se intentó conectar al $this->ftp_server por el usuario $this->ftp_user_name"; 
                echo " \n";
                exit(); 

            } else {
                echo "\n Conexión a $this->ftp_server realizada con éxito, por el usuario " . $this->ftp_user_name . " \n";
            }

            
            # Obtener el número de archivos contenidos en el directorio actual
            $lista = $this->finder->files();
            $numarch = count($lista);


            echo "\n -> Documentos, " . $tipodoc . ": " . $numarch . " archivos \n";

            switch ($tipodoc) {

                case 'conclusiones':
                    # Total de Certificados
                    $totalCert = $numarch;
                    break;
            
                
                default:
                    # code...
                    break;
            }

            # Definimos Contadores Generales
            $contArch = 0;
            $docNV = 0;
            #$proc = 0;

            echo "\n Procesando " . $tipodoc . "... \n";

            # Recorremos Archivo por Archivo por Directorio

            for ($i=0; $i < $numarch ; $i++) {
                
                switch ($tipodoc) {
                   

                    case 'conclusiones':
                        # Certificados Procesados
                        $procCert = $contArch++;
                        break;
                    
                    default:
                        # code...
                        break;
                }
                #echo "█\n" ;

                # Obtenemos la Fecha de Modificación del Archivo FTP
                unset($docftp);
                $docftp = $this->finder->files();

                if ($docftp==-1){
                    unset($docftp);
                    #unset($conn_id);
                    $this->finder->closedir();
                    unset($conn_id);
                   
                    
                    fwrite($log,("\n FALLO, INTENTANDO CONECTAR DE NUEVO"));
                    

                                # Establecemos Conexión FTP
                $this->finder->files()->in("ftp://$this->ftp_user_name:$this->ftp_user_pass@$this->ftp_server")->name($lista[$i]); 


                    if (!$this->finder->hasResults()) {  
                        echo "\n ¡La conexión FTP ha fallado DESPUES DEL ERROR!\n";
                        echo " \n";
                        exit(); 

                    } else {
                        echo "\n ERROR AL OBTENER FECHA!!! VOLVEMOS A CONECTAR AL SERVIDOR " . $this->ftp_user_name . " \n";
                        fwrite($log,("\n FALLO, CONECTAMOS DE NUEVO Y SEGUIMOS"));
                    }

                    $docftp = $this->finder->date('since today');
                    #$docftp=filemtime("ftp://$ftp_user_name:$ftp_user_pass@sohiscert3.ddns.cyberoam.com/".$lista[$i]);
                }
                

                $fmoddoc = date("Y-m-d", $docftp);
                $fecha_bruta=$fmoddoc;

                $fmoddoc = strtotime($fmoddoc);


                #echo $semantes . " - " . $fmoddoc . " - " . $diahoy;


                #echo "\n" . $fmoddoc . "\n";
                echo " " . $contArch . "\r";
                #echo "\n - Doc: " . $lista[$i] . " | F.M: " . $fmoddoc . " - F.F: 2019-01-02 \n";
                #echo " " . $contArch . " - Procesadas: " . $proc . "\r";
                

                # Comprobamos la Fecha de Modificación del Archivo FTP con
                # la Fecha del Día Anterior como Filtro de Exclusión de 
                # aquellos archivos que no hayan sido modificados o no sean nuevos
                # 
                #if (strcmp($fdoc, $semantes) == 0) {


                

                # Escribimos Comienzo y Fin de Ejecución
                fwrite($log,("\n* CONTADOR: ". $contArch ." | Fecha numerica: ". $docftp ." | FECHA REAL: ".$fecha_bruta." RUTA: ".$lista[$i]."\n"));





                if (($fmoddoc >= $semantes) && ($fmoddoc <= $diahoy)) {
                   
                #if (strtotime($fmoddoc) == strtotime('2018-12-17')) {
                #    echo "\n Entro \n";
               
                # Comprobamos sólo los archivos PDF de los Directorios definidos del Servidor FTP
                # y a su vez, aquellos que NO Contengan Untitled
                if ((strpos($lista[$i], '.pdf') !== false) && (strpos($lista[$i], 'Untitled') === false) && (strpos($lista[$i], '-') !== false)) {

                    
                    

                    $em =  $em->container->get('doctrine')->getManager();
                    $archivo = $em->getRepository(DocumentosFTP::class)->findOneByNbDoc($lista[$i]);

                    switch ($tipodoc) {

                        case 'conclusiones':
                            
                            # Obtenemos la Fecha de Modificación del Archivo FTP
                            $docftp = $this->finder->date('since today');
                            $fechadoc = date("Y-m-d H:i:s", $docftp);

                            $nbop='';
                          
                            #var_dump($archivo);
                            #exit('Valor Certificado');

                             # Si NO Existe la Factura en BB.DD.
                            if (!isset($archivo)) {
                                
                                # Obtenemos el Operador para ello
                                #  - tenemos que localizar la posición del último "-"
                                #  - eliminar la cadena de texto hasta la posción del "-"
                                #  - y al texto resultante le quitamos el "-" y la extensión ".pdf"
                                #$posg = strrpos($lista[$i], '-');
                                #var_dump($posg);
                                #$op = substr($lista[$i], 0, $posg);
                                #var_dump($op);
                                $op = substr(strrchr($lista[$i], '/'), 1);
                                #var_dump($op);
                                #$op = trim($op, '-');
                                $op= substr($op, 0, -4);
                                

                                #Recorremos el array
                                $porciones = explode("-", $op);
                           
                                for ($j=0; $j < count($porciones) ; $j++) {
                                    # Obtenemos el Código del Operador a partir del Nombre
                                    $cons = $em->container->get('doctrine')->getManager();

                                    #MNN Consulta para la versión inferior a PHP 7.0
                                    $datosOp = $cons->getRepository(Operator::class)->findOneByOpNop($porciones[$j]);

                                    if (count($datosOp) > 0){
                                        $operador2=$porciones[$j];

                                    }

                                }    
                                if(!$operador2){
                                    continue 2;
                                }
                                
                                if (strcmp(substr($op, 0, 4), 'F27-') == 0 or strcmp(substr($op, 0, 4), 'F194')== 0){
                                    
                                    $nbop = $operador2;

                                }#else{
                                  #  $nbop='XX';
                                #}
                                # Si el Documento No Contiene más '-' o No Empiece por F ni por 1
                               /* if ((strrpos($op, '-') == false && strrpos($op, ' ') == false) || (strcmp(substr($op, 0, 1), 'F') <> 0 && strcmp(substr($op, 0, 1), '1') <> 0)) {
                                    
                                    $nbop = $op;
                                    #echo "\n If 1 \n";

                                    # Si el Documenta Comienza por F, 1 o S
                                }elseif (strcmp(substr($op, 0, 1), 'F') == 0 || strcmp(substr($op, 0, 1), '1') == 0 || strcmp(substr($op, 0, 1), 'S') == 0) {
                                    
                                    # Obtenemos el Nombre del Operador a partir de la última
                                    # posición del '-'
                                    $tamnc = strlen($op);
                                    $uposg = strrpos($op, '-');

                                    $nbop = substr($op, ($uposg + 1), $tamnc);
                                    #echo "\n If 2 \n";

                                    # Si el Documento Comienza por NAQS o NOP
                                }elseif (strcmp(substr($op, 0, 4), 'NAQS') == 0 || strcmp(substr($op, 0, 3), 'NOP') == 0) {
                                    
                                    # Obtenemos el Nombre del Operador a partir de la última
                                    # posición del '(espacio)'
                                    $tamnc = strlen($op);
                                    $uposg = strrpos($op, ' ');

                                    $nbop = substr($op, ($uposg + 1), $tamnc);
                                    #echo "\n If 3 \n";
                                }
                                */
                                #var_dump($nbop);   

                                # Obtenemos el Código del Operador a partir del Nombre
                                #$cons = $em->container->get('doctrine')->getManager();

                                #MNN Consulta para la versión inferior a PHP 7.0
                                #$datosOp = $cons->getRepository(Operator::class)->findOneByOpNop($nbop);

                                # Consultas

                                #$datosOp = $cons->getRepository(Operator::class)->findOneByOpNop($nbop);

                                #MNN consulta para versión PHP 5.6
                              
                                
                                $query = $em->createQuery('SELECT ope.codigo, ope.opNop, ope.opEma, reg.reDeno, ope.opCdp
                                                             FROM AppBundle:Operator ope
                                                             INNER JOIN AppBundle:Register reg WITH ope.opRegistro=reg.id
                                                            WHERE ope.opNop = :nom')->setParameter('nom', $nbop);

                                $datosOp = $query->getResult();

                                
                                
                                #$datosOp = $cons->getRepository(Operator::class)->findOneBy(array('opNop' => $nbop));

                                $operador = array();

                                #var_dump(count($datosOp));
                                #var_dump($datosOp);
                                #exit('Datos del Operador');

                                # Si el Operador Existe, NO es Nulo, en el Sistema
                                if (count($datosOp) > 0) {
                     
                                    # Actualzamos el Contador de Certificados Nuevos
                                    $cerNew++;

                                    foreach ($datosOp as $registro) {
                                        #var_dump($registro);
                                        #exit('Registro');

                                        foreach ($registro as $key => $value) {
                                            # code...
                                            echo "\n - " . $key . ": " . $value;
                                            $operador[$key] = $value;
                                        }
                                        
                                    }

                                   
                                    #exit("\n Hasta Datos Operador \n");

                                    # Recuperamos Sólo el Nombre del Documeto para Almacenar Sin la Ruta 
                                    # $nbdoc = substr($lista[$i], 18);
                                    $nbdoc = $lista[$i];
                                
                                    # Inicializamos Variable y Añadimos los Valores para el Nuevo Documento
                                    $docNew = new DocumentosFTP();


                                    if (isset($operador["opCdp"])) {
                                        # code...
                                        $docNew->setOpCdp($operador["opCdp"]);

                                    }else{
                                        $docNew->setOpCdp(" ");
                                    }

                                    $docNew->setOpNop($nbop);
                                    $docNew->setTipoDoc($tipodoc);
                                    $docNew->setNbDoc($nbdoc);
                                    $docNew->setFechaDoc(new \DateTime($fechadoc));


                                    $em->persist($docNew);
                                    $em->flush();

                                    #var_dump($docNew);
                                    #exit('Certificado Grabado BB.DD.');
                                    
                                    if (isset($operador["opEma"])) {
                                        # code...
                                        $datamail = array(
                                            "operator" => $nbop,
                                            "tipo" => $tipodoc,
                                            "documento" => $nbdoc,
                                            "mail" => $operador["opEma"],
                                            "alcance"=>$operador["reDeno"]
                                        );
                               
                                    }else{

                                        # Actualzamos el Contador de Certificados Incorrectos, Sin E-mail
                                        $cerSO++;
                                    }
                                    
                                }else{
                                    # Actualzamos el Contador de Certificados Incorrectos, Sin Operador
                                    $cerSO++;

                                }

                            }else{
                                # Si Existe el Ceertificado en la BB.DD.
                                
                                # Asignamos Archivo a una Variable Nueva para Evitar Errores de Trabajo
                                $registro = $archivo;
                                
                                #var_dump($registro);
                                #exit('Archivo');
                                
                                # Recuèramos la Fecha Almacenada del Documento
                                $fechaalmdoc = $registro->getFechaDoc();
                                
                                #var_dump($fechaalmdoc);
                                #exit('Fecha Almacenada');

                                # Convertimos la Fecha Almacenada del Documento en un Array
                                # para poder recuperar el valor almacenado del campo Date
                                $campo = (array) $fechaalmdoc;
                                
                                # Recorremos los Valores del Campo
                                foreach ($campo as $key => $value) {
                                    #var_dump($key);
                                    
                                    # Comparamos los Index de los Campos
                                    if (strcmp($key, "date") === 0) {
                                        
                                        # Almacenamos la Posición del Caracter '.'
                                        $pos = strpos($value, '.');

                                        # Obtenemos la Cadena de Fecha y Hora, excluyendo desde el '.'
                                        $fechaalm = substr($value, 0, $pos);

                                        # Comparamos la Fecha del Documento del Servidor FTP con
                                        # la Fecha del mismo Documento almacenada en BB.DD.
                                        if (strcmp($fechadoc, $fechaalm) <> 0) {
                                            # Asignamos Valores del Registro para su posterior envio
                                            # y Actualizamos la Fecha del Documento del Registro
                                            #$registro->getOpCdp();
                                            $nbop = $registro->getOpNop();
                                            $tipodoc = $registro->getTipoDoc();
                                            $nbdoc = $registro->getNbDoc();
                                            $registro->setFechaDoc(new \DateTime($fechadoc));

                                            $em->persist($registro);
                                            $em->flush();

                                            #var_dump($registro);
                                            #exit('Fecha Registro Modificada');

                                            # Obtenemos los Datos del Operador a partir del Nombre para recuperar el Mail 
                                            #$cons = $em->container->get('doctrine')->getManager();
                                            #$datosOp = $cons->getRepository(Operator::class)->findOneByOpNop($nbop);
                                            
                                            $query = $em->createQuery('SELECT ope.codigo, ope.opNop, ope.opEma, reg.reDeno
                                                             FROM AppBundle:Operator ope
                                                             INNER JOIN AppBundle:Register reg WITH ope.opRegistro=reg.id
                                                            WHERE ope.opNop = :nom')->setParameter('nom', $nbop);

                                            $datosOp = $query->getResult();

                                            $operador = array();

                                            # Si el Operador Existe, NO es Nulo, en el Sistema
                                            if (count($datosOp) > 0) {

                                                # Actualzamos el Contador de Certificados Actualizados
                                                $cerUpdate++;

                                                foreach ($datosOp as $registro) {
                                                    #var_dump($registro);

                                                    foreach ($registro as $key => $value) {

                                                        # echo "\n - " . $key . ": " . $value;
                                                        $operador[$key] = $value;
                                                    }
                                                    
                                                }

                                                # Parámetros para el Envío del Mail
                                                if (isset($operador["opEma"])) {
                                                    # code...
                                                    $datamail = array(
                                                        "operator" => $nbop,
                                                        "tipo" => $tipodoc,
                                                        "documento" => $nbdoc,
                                                        "mail" => $operador["opEma"],
                                                        "alcance"=>$operador["reDeno"]
                                                    );

                                                }else{

                                                    # Actualzamos el Contador de Certificados Incorrectos, Sin E-mail
                                                    $cerSO++;
                                                }

                                                #var_dump($datamail);
                                                # exit('Parámetros para Envío Mail de Documento Actualizado');
                                            }else{

                                                # Actualzamos el Contador de Certificados Incorrectos, Sin Operador
                                                $cerSO++;
                                            }
                                        }
                                    }

                                # exit('Entro en Certificados Existentes');
                                }
                            }

                            break;
                      
                        default:
                            # code...
                            break;
                    }
                    
                    #
                    # Si Existen Datos de Actualización para Remitir por Mail 
                    #


                    if (isset($datamail)) {
                
                        if ($datamail['mail']!=''){
                            dump($datamail);
                        if($datamail["mail"] != null){
                            $datamail["mail"] = array_filter(preg_split('[;,/ ]',trim($datamail["mail"])));
                                if($datamail["mail"][0]){
                                    $datamail["mail"] = $datamail["mail"][0];
                                    str_replace("ñ","n",$datamail["mail"]);
                                    str_replace("á","a",$datamail["mail"]);
                                        str_replace("é","e",$datamail["mail"]);
                                        str_replace("í","i",$datamail["mail"]);
                                        str_replace("ó","o",$datamail["mail"]);
                                        str_replace("ú","u",$datamail["mail"]);
                                    if($datamail["mail"]==null || ($datamail["mail"] != [] && $datamail["mail"] != null && $datamail["mail"] != "" && !filter_var($datamail["mail"], FILTER_VALIDATE_EMAIL))){
                                        $path_file_fail = $urlBase.'register_falladas_CON_'.date("d_m_Y").'.log';
                                        $open_file = fopen($path_file_fail,'a+');
                                        fwrite($open_file,date("Y-m-d H:i:s"). "---->" .implode($datamail));
                                        fclose($open_file);
                                        $datamail["mail"] = null;
                                    }
                                }
                            }
                        
                        $mailerServiceName = sprintf('swiftmailer.mailer.%s', $input->getOption('mailer'));
                        if (!$em->container->has($mailerServiceName)) {
                            throw new \InvalidArgumentException(sprintf('The mailer "%s" does not exist', $input->getOption('mailer')));
                        }

                        switch ($input->getOption('body-source')) {
                            case 'file':
                                $filename = $input->getOption('body');
                                $content = file_get_contents($filename);
                                if ($content === false) {
                                    throw new \Exception('Could not get contents from ' . $filename);
                                }
                                $input->setOption('body', $content);
                                break;

                            case 'stdin':
                                break;

                            default:
                                throw new \InvalidArgumentException('Body-input option should be "stdin" or "file"');
                        }
                  
                        

                        $message = $this->createMessage($input, $datamail);
                        $mailer = $em->container->get($mailerServiceName);
                        $output->writeln(sprintf('<info>Sent %s emails<info>', $mailer->send($message)));
                        
                        $contMail++;
                        unset($datamail);
                        # exit('Envió de Mail Realizado');

                    }
                }
                    
                }else{

                    # Documentos NO Válidos
                    switch ($tipodoc) {
                        

                        case 'conclusiones':
                            # Certificados No Válidos
                            $docNVCert = $docNV++;;
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }

                } # Cierre de Comparar Fechas
            }

        # Cerramos la Conexión FTP 
        $this->finder->closedir();
        }

        echo "\n Proceso Finalizado, generando Archivo Log ...\n";

        #
        # Archivo LOG con la Información Procesada
        #
        # Definimos la Ruta
        $urlBase = $em->getParameter('path_update_logs');

        # Definimos Variable de Fin de Ejecución 
        $end = date("Y-m-d H:i:s");

        # Definimos la Ruta Completa y el Nombre del Fichero LOG que se va a generar
        $path_file = $urlBase.'update_datedocuments_CONCLUSIONES_'.date("d_m_Y").'.log';

        # Abrimos el Archivo con Permisos de Sobrescritura
        $log = fopen($path_file, "w+");

        # Escribimos Comienzo y Fin de Ejecución
        fwrite(
            $log,
            ("\n* DOCUMENTOS FTP => Comienzo: ". $now ." | Final: ". $end ."\n")
        );

        # Escribimos Información sobre Facturas
        /*fwrite(
            $log,
            ("\n - Facturas => Total: ". $totalFact ." | Procesadas: ". $procFact ." | Nuevas: ". $facNew ." | Actualizadas: ". $facUpdate ." | Sin Operador: ". $facSO ."\n")
        );*/

        # Escribimos Información sobre Certificados
        fwrite(
            $log,
            ("\n - Conclusioness => Total: ". $totalCert ." | Procesados: ". $procCert ." | Nuevos: ". $cerNew ." | Actualizados: ". $cerUpdate ." | Sin Operador: ". $cerSO ."\n")
        );

        # Escribimos Información sobre conclusioness
        /*fwrite(
            $log,
            ("\n - conclusioness => Total: ". $totalCart ." | Procesadas: ". $procCart ." | Nuevas: ". $carNew ." | Actualizadas: ". $carUpdate ." | Sin Operador: ". $carSO ."\n")
        );*/

        # Escribimos Información sobre conclusioness
        /*fwrite(
            $log,
            ("\n - Análisis => Total: ". $totalAna ." | Procesadas: ". $procAna ." | Nuevas: ". $anaNew ." | Actualizadas: ". $anaUpdate ." | Sin Operador: ". $anaSO ."\n")
        );*/

        # Escribimos Información sobre Documentos No Válidos
        fwrite(
            $log,
            ("\n - Documentos NO Válidos => conclusioness: ". $docNVFact ." | conclusioness: " . $docNVCert ." | conclusioness: " . $docNVCart ." | Análisis: " . $docNVAna . "\n")
        );

        # Escribimos Información sobre Mails Enviados
        fwrite(
            $log,
            ("\n - Mails => Enviados: ". $contMail ."\n")
        );

        # Cerramos el Archivo
        fclose($log);


        echo "\n Archivo Log," . $path_file . ", Generado con Éxito \n";
        return 0;
    }

    /**
     * OMITIMOS LA FUNCIÓN QUE PREGUNTA LOS PARÁMETROS PARA EL ENVÍO DEL E-MAIL 
     * {@inheritdoc}
     */
    /*
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        // Symfony <2.5 BC
        /** @var QuestionHelper|DialogHelper $questionHelper
        $questionHelper = $this->getHelperSet()->has('question') ? $this->getHelperSet()->get('question') : $this->getHelperSet()->get('dialog');

        foreach ($input->getOptions() as $option => $value) {
           
            if ($value === null) {
                // Symfony <2.5 BC
                if ($questionHelper instanceof QuestionHelper) {
                    $question = new Question(sprintf('<question>%s</question>: ', ucfirst($option)));
                } else {
                    $question = sprintf('<question>%s</question>: ', ucfirst($option));
                }
                $input->setOption($option, $questionHelper->ask($input, $output, $question));
            }
        }
    }
    */

    /**
     * {@inheritdoc}
     */
    /*public function isEnabled()
    {
        $em = new ContainerBuilder();
        return $em->has('swiftmailer.mailer.mailer_mail');
    }*/

    /**
     * Creates new message from input options.
     *
     * @param InputInterface $input An InputInterface instance
     *
     * @return \Swift_Message New message
     */
    private function createMessage(InputInterface $input, Array $datos)
    {
      
        # Recuperamos el Nombre del Operador, Tipo de Documento y Destinatario
        $nbop = $datos['operator'];
        $tipodoc = $datos['tipo'];
        $destino = $datos['mail'];
        $alcance = $datos['alcance'];

        # Según el Tipo de Documento Extraemos SÓLO el Nombre de los Documentos
        switch ($tipodoc) {

            case 'conclusiones':

                $tipodoc2="Conclusiones de auditoría";
                $nbdoc = substr($datos['documento'], 8);

                break;
            
            default:
                # code...
                break;
        }
        

        $from  = 'noreply@sohiscert.com';
        # $to = $destino;
        $to = 'manuel.navarro@atlantic.es';
        $subject = "Alta de documento en Área Privada web: Conclusiones de auditoría"; 
        
        /*MNN Modificamos la plantilla */

        $body  = '


        <!DOCTYPE html>
        <html>
        <head>
            <title>Area Privada Sohiscert - Notificación de Actualización de Documentos</title>
        </head>
        <body>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="background">
                <tr>
                    <td align="center" valign="top" width="100%" class="background">
                        <table cellpadding="0" cellspacing="0" width="600" class="wrap">
                            <tr>
                                <td valign="top" class="wrap-cell" style="padding-top:30px; padding-bottom:30px;">
                                    <table cellpadding="0" cellspacing="0" class="force-full-width">
                                        <tr>
                                            <td height="82" align="center" valign="top" class="header-cell">
                                                <img width="322" height="80"
                                                     src="http://sohiscert.com/wp-content/uploads/2015/04/logoretina1.png"
                                                     alt="logo-Sohiscert">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><br>&nbsp;<br></td>
                                        </tr>
                                        <tr>
                                            <td valign="top" class="body-cell">
                                                <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff">
                                                    <tr>
                                                        <td valign="top"
                                                            style="padding-bottom:20px; background-color:#ffffff;">
                                                            <h2><p align="center"><b>Notificación de Actualización de Documentos</b></h2>
                                                            <br>&nbsp;
                                                            <p align="justify">Desde SOHISCERT le informamos que ya tiene disponible los siguientes documentos en su Área Privada web:
                                                        </td>
                                                    </tr>
                                                   
                                                    <tr>
                                                        <td>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Alcance de certificación: <b>' . $alcance . '</b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><br>&nbsp;<br></td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Número de Expediente: <b>' . $nbop . '</b>
                                                            
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><br>&nbsp;<br></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Tipo de documento: <b>' . ucwords($tipodoc2) . '</b></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><br>&nbsp;<br></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Puede acceder al Área Privada a través de este enlace:
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">
                                                            <br>
                                                            <br>
                                                            <p><a target="_blank" href="https://intranet-sohiscert.e4ff.pro-eu-west-1.openshiftapps.com/web/login" style="text-decoration:none"><font size="3px" face="arial" color="#40733c" weight="500"><b> ACCEDER AL ÁREA PRIVADA DE CLIENTES</b></font></a>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top" class="footer-cell">
                                                <br>
                                                <br>
                                                <p>Si usted no dispone de clave y usuario para acceder, solicítelas enviando un email a: <a href="mailto:areaprivadaweb@sohiscert.com">areaprivadaweb@sohiscert.com</a> o llamando al teléfono 955868051.
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top" class="footer-cell">
                                                <br>
                                                <br>
                                                <p>Saludos
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ';

       

        $charset = "UTF-8";


        $input->setOption('subject', $subject);
        $input->setOption('body', $body);
        $input->setOption('content-type', 'text/html');
        $input->setOption('charset', $charset);
        $input->setOption('from', $from);
        $input->setOption('to', $to);


        $message = (new \Swift_Message(
            $input->getOption('subject'),
            $input->getOption('body'),
            $input->getOption('content-type'),
            $input->getOption('charset')
        ));
        $message->setFrom($input->getOption('from'));
        $message->setTo($input->getOption('to'));

        return $message;
    }
}
