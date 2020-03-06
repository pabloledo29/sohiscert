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
use Symfony\Component\Finder\Finder;

/**
 * Class EmailUpdateDocCommand
 * @package App\Command
 */
class EmailUpdateDocCommand extends Command
{
    protected static $defaultName = 'email:emaildoc:send';
    public function __construct(string $path_update_logs,string $ftp_server, string $ftp_user_name, string $ftp_user_pass)
    {
        $this->path_update_logs = $path_update_logs;
        $this->finder = new Finder();
         # Datos Conexión FTP para poder Obtener Fecha Modificación de los Archivos
        $this->ftp_server = $ftp_server;
        $this->ftp_user_name = $ftp_user_name;
        $this->ftp_user_pass = $ftp_user_pass;
         // you *must* call the parent constructor
         parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('email:emaildoc:send')
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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        # Definimos Variable de Cominezo de Ejecución 
        $now = date("Y-m-d H:i:s");

        # Definimos e Inicializamos Contadores de
        # Facturas, Cartas, Certificados, Análisis y Mails a Remitir
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
        #$rutasftp = array('factura' => '/facturasintranet', 'certificado' => '/SITIO2', 'carta' => '/SITIO3', 'analisis' => "/SITIO1");


        # Obtener Fechas, Día Actual y Día de la Semana Atrás desde el día actual
        $diahoy = date('Y-m-d', time());
        $diahoy = strtotime($diahoy);

        $semantes = '2019-01-01';
        #$semantes = date('Y-m-d', strtotime('-1 week'));
        $semantes = strtotime($semantes);

        
        # Rutas para Pruebas
        $rutasftp = array('certificado' => '/SITIO2');
        #$rutasftp = array('factura' => '/facturasintranet');

        $em = new ContainerBuilder();
        #MNN Creamos el archivo update de reccorridos de archivos de certificados
        $urlBase = $this->path_update_logs;

        # Definimos Variable de Fin de Ejecución 
        $end = date("Y-m-d H:i:s");

        # Definimos la Ruta Completa y el Nombre del Fichero LOG que se va a generar
        $path_file = $urlBase.'register_recorridos_'.date("d_m_Y").'.log';

        # Abrimos el Archivo con Permisos de Sobrescritura
        $log = fopen($path_file, "w+");

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

                case 'factura':
                    # Total de Facturas
                    $totalFact = $numarch;
                    break;

                case 'certificado':
                    # Total de Certificados
                    $totalCert = $numarch;
                    break;

                case 'carta':
                    # Total de Cartas
                    $totalCart = $numarch;
                    break;

                case 'analisis':
                    # Total de Análisis
                    $totalAna = $numarch;
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
                    case 'factura':
                        # Facturas Procesadas
                        $procFact = $contArch++;
                        break;

                    case 'certificado':
                        # Certificados Procesados
                        $procCert = $contArch++;
                        break;

                    case 'carta':
                        # Cartas Procesadas
                        $procCart = $contArch++;
                        break;

                    case 'analisis':
                        # Análisis Procesados
                        $procAna = $contArch++;
                        break;
                    
                    default:
                        # code...
                        break;
                }
                #echo "█\n" ;

                # Obtenemos la Fecha de Modificación del Archivo FTP
                unset($docftp);
                #$docftp = ftp_mdtm($conn_id, $lista[$i]);

                $docftp=$this->finder->date('since today');

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
                        
                        case 'factura':

                            # Obtenemos la Fecha de Modificación del Archivo FTP
                            $docftp = $this->finder->date('since today');
                            $fechadoc = date("Y-m-d H:i:s", $docftp);

                            #var_dump($archivo);
                            #exit('Valor Documento');

                            # Si NO Existe la Factura en BB.DD.
                            if (!isset($archivo)) {
                                
                                # Obtenemos el Operador para ello
                                #  - tenemos que localizar la posición del último "-"
                                #  - eliminar la cadena de texto hasta la posción del "-"
                                #  - y al texto resultante le quitamos el "-" y la extensión ".pdf"
                                $posg = strpos($lista[$i], '-');

                                $op = substr($lista[$i], $posg);
                                $op = trim($op, '-');
                                $nbop = trim($op, '.pdf');
                                #var_dump($nbop);

                                # Obtenemos el Código del Operador a partir del Nombre
                                #$cons = $em->container->get('doctrine')->getManager();
                                #$datosOp = $cons->getRepository(Operator::class)->findOneByOpNop($nbop);
                                echo "\n ". $nbop;
                                $query = $em->createQuery('SELECT ope.codigo, ope.opNop, ope.opEma
                                                             FROM App\Entity\Operator ope
                                                            WHERE ope.opNop = :nom')->setParameter('nom', $nbop);

                                $datosOp = $query->getResult();

                                $operador = array();


                                #var_dump(count($datosOp));
                                #exit('Aqui');
                                
                                # Si el Operador Existe, NO es Nulo, en el Sistema
                                if (count($datosOp) > 0) {

                                    # Actualzamos el Contador de Facturas Nuevas
                                    $facNew++;

                                    foreach ($datosOp as $registro) {
                                        #var_dump($registro);

                                        foreach ($registro as $key => $value) {

                                            #echo "\n - " . $key . ": " . $value;
                                            $operador[$key] = $value;
                                        }
                                        
                                    }

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


                                    if (isset($operador["opEma"])) {
                                        # code...
                                        $datamail = array(
                                            "operator" => $nbop,
                                            "tipo" => $tipodoc,
                                            "documento" => $nbdoc,
                                            "mail" => $operador["opEma"]
                                        );

                                    }else{

                                        # Actualzamos el Contador de Facturas Incorrectas, Sin E-mail
                                        $facSO++;
                                    }

                                    

                                }else{

                                    # Actualzamos el Contador de Facturas Incorrectas, Sin Operador
                                    $facSO++;
                                }

                            }else{
                                # Si Existe la Factura en la BB.DD.
                                
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
                                            
                                            $query = $em->createQuery('SELECT ope.codigo, ope.opNop, ope.opEma
                                                                         FROM App\Entity\Operator ope
                                                                        WHERE ope.opNop = :nom')->setParameter('nom', $nbop);

                                            $datosOp = $query->getResult();

                                            $operador = array();

                                            # Si el Operador Existe, NO es Nulo, en el Sistema
                                            if (count($datosOp) > 0) {

                                                # Actualzamos el Contador de Facturas Actualizadas
                                                $facUpdate++;

                                                foreach ($datosOp as $registro) {
                                                    #var_dump($registro);

                                                    foreach ($registro as $key => $value) {
                                                        # code...
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
                                                        "mail" => $operador["opEma"]
                                                    );

                                                }else{

                                                    # Actualzamos el Contador de Facturas Incorrectas, Sin E-mail
                                                    $facSO++;
                                                }

                                                # var_dump($datamail);
                                                # exit('Parámetros para Envío Mail de Documento Actualizado');
                                            }else{

                                                # Actualzamos el Contador de Facturas Incorrectas, Sin Operador
                                                $facSO++;
                                            }
                                        }
                                    }

                                # exit('Entro en Facturas Existentes');
                                }
                            }
                            break;


                        case 'certificado':
                            
                            # Obtenemos la Fecha de Modificación del Archivo FTP
                            $docftp = $this->finder->date('since today');
                            $fechadoc = date("Y-m-d H:i:s", $docftp);

                            #var_dump($archivo);
                            #exit('Valor Certificado');

                             # Si NO Existe la Factura en BB.DD.
                            if (!isset($archivo)) {
                                
                                # Obtenemos el Operador para ello
                                #  - tenemos que localizar la posición del último "-"
                                #  - eliminar la cadena de texto hasta la posción del "-"
                                #  - y al texto resultante le quitamos el "-" y la extensión ".pdf"
                                $posg = strrpos($lista[$i], '-');
                                #var_dump($posg);
                                $op = substr($lista[$i], 0, $posg);
                                #var_dump($op);
                                $op = substr(strrchr($op, '/'), 1);
                                #var_dump($op);
                                $op = trim($op, '-');
                                #var_dump($op);


                                # Si el Documento No Contiene más '-' o No Empiece por F ni por 1
                                if ((strrpos($op, '-') == false && strrpos($op, ' ') == false) || (strcmp(substr($op, 0, 1), 'F') <> 0 && strcmp(substr($op, 0, 1), '1') <> 0)) {
                                    
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
                                
                                #var_dump($nbop);   

                                # Obtenemos el Código del Operador a partir del Nombre
                                #$cons = $em->container->get('doctrine')->getManager();

                                #MNN Consulta para la versión inferior a PHP 7.0
                                #$datosOp = $cons->getRepository(Operator::class)->findOneByOpNop($nbop);

                                # Consultas

                                #$datosOp = $cons->getRepository(Operator::class)->findOneByOpNop($nbop);

                                #MNN consulta para versión PHP 5.6
                                $query = $em->createQuery('SELECT ope.codigo, ope.opNop, ope.opEma
                                                             FROM App\Entity\Operator ope
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

                                    #var_dump($operador);
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
                                            "mail" => $operador["opEma"]
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
                                            
                                            $query = $em->createQuery('SELECT ope.codigo, ope.opNop, ope.opEma
                                                             FROM App\Entity\Operator ope
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
                                                        "mail" => $operador["opEma"]
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


                        case 'carta':

                            # Obtenemos la Fecha de Modificación del Archivo FTP
                            $docftp = $this->finder->date('since today');
                            $fechadoc = date("Y-m-d H:i:s", $docftp);

                            # Obtener Día Antes
                            $diantes = date('Y-m-d H:i:s', strtotime('-1 day'));

                            #var_dump($archivo);
                            #exit('Valor Certificado');

                             # Si NO Existe la Carta en BB.DD.
                            if (!isset($archivo)) {
                                
                                # Obtenemos el Operador para ello
                                #  - tenemos que localizar la posición del último "-"
                                #  - eliminar la cadena de texto hasta la posción del "-"
                                #  - y al texto resultante le quitamos el "-" y la extensión ".pdf"
                                $posg = strrpos($lista[$i], '-');
                                #var_dump($posg);
                                $op = substr($lista[$i], 0, $posg);
                                #var_dump($op);
                                $op = substr(strrchr($op, '/'), 1);
                                #var_dump($op);
                                $op = trim($op, '-');
                                #var_dump($op);

                                # Si el Documento No Contiene más '-' o No Empiece por F
                                if (strrpos($op, '-') == false || strcmp(substr($op, 0, 1), 'F') <> 0) {
                                    
                                    $nbop = $op;

                                }else{
                                    $tamnc = strlen($op);
                                    $uposg = strrpos($op, '-');

                                    $nbop = substr($op, ($uposg + 1), $tamnc);
                                }                                
                                #var_dump($nbop);

                                # Obtenemos el Código del Operador a partir del Nombre
                                #$cons = $em->container->get('doctrine')->getManager();
                                #$datosOp = $cons->getRepository(Operator::class)->findOneByOpNop($nbop);
                                

                                $query = $em->createQuery('SELECT ope.codigo, ope.opNop, ope.opEma
                                                             FROM App\Entity\Operator ope
                                                            WHERE ope.opNop = :nom')->setParameter('nom', $nbop);

                                $datosOp = $query->getResult();

                                $operador = array();

                                #var_dump(count($datosOp));
                                #exit('Datos del Operador');

                                # Si el Operador Existe, NO es Nulo, en el Sistema
                                if (count($datosOp) > 0) {

                                    # Actualzamos el Contador de Cartas Nuevas
                                    $carNew++;

                                    foreach ($datosOp as $registro) {
                                        #var_dump($registro);
                                        #exit('Registro');

                                        foreach ($registro as $key => $value) {
                                            # code...
                                            #echo "\n - " . $key . ": " . $value;
                                            $operador[$key] = $value;
                                        }
                                        
                                    }

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
                                            "mail" => $operador["opEma"]
                                        );

                                    }else{

                                        # Actualzamos el Contador de Cartas Incorrectas, Sin E-mail
                                        $carSO++;
                                    }
                                    
                                }else{
                                    # Actualzamos el Contador de Cartas Incorrectas, Sin Operador
                                    $carSO++;
                                }

                            }else{
                                # Si Existe la Carta en la BB.DD.
                                
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
                                            
                                            $query = $em->createQuery('SELECT ope.codigo, ope.opNop, ope.opEma
                                                             FROM App\Entity\Operator ope
                                                            WHERE ope.opNop = :nom')->setParameter('nom', $nbop);

                                            $datosOp = $query->getResult();    

                                            $operador = array();

                                            # Si el Operador Existe, NO es Nulo, en el Sistema
                                            if (count($datosOp) > 0) {

                                                # Actualzamos el Contador de Cartas Actualizadas
                                                $carUpdate++;

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
                                                        "mail" => $operador["opEma"]
                                                    );

                                                }else{

                                                    # Actualzamos el Contador de Cartas Incorrectas, Sin E-mail
                                                    $carSO++;
                                                }

                                                #var_dump($datamail);
                                                # exit('Parámetros para Envío Mail de Documento Actualizado');
                                            }else{

                                                # Actualzamos el Contador de Cartas Incorrectas, Sin Operador
                                                $carSO++;
                                            }
                                        }
                                    }

                                # exit('Entro en Cartas Existentes');
                                }
                            }

                            break;


                        case 'analisis':
                            
                            # Obtenemos la Fecha de Modificación del Archivo FTP
                            $docftp = $this->finder->date('since today');
                            $fechadoc = date("Y-m-d H:i:s", $docftp);

                            #var_dump($archivo);
                            #exit('Valor Documento');

                            # Si NO Existe la Factura en BB.DD.
                            if (!isset($archivo)) {
                                
                                # Obtenemos el Operador para ello
                                #  - tenemos que localizar la posición del último "-"
                                #  - eliminar la cadena de texto hasta la posción del "-"
                                #  - y al texto resultante le quitamos el "-" y la extensión ".pdf"
                                $posg = strpos($lista[$i], '-');

                                $op = substr($lista[$i], $posg);
                                $op = trim($op, '-');
                                $nbop = trim($op, '.pdf');
                                #var_dump($nbop);

                                # Obtenemos el Código del Operador a partir del Nombre
                                #$cons = $em->container->get('doctrine')->getManager();
                                #$datosOp = $cons->getRepository(Operator::class)->findOneByOpNop($nbop);
                                
                                $query = $em->createQuery('SELECT ope.codigo, ope.opNop, ope.opEma
                                                             FROM App\Entity\Operator ope
                                                            WHERE ope.opNop = :nom')->setParameter('nom', $nbop);

                                $datosOp = $query->getResult();


                                $operador = array();

                                #var_dump(count($datosOp));
                                #exit('Aqui');
                                
                                # Si el Operador Existe, NO es Nulo, en el Sistema
                                if (count($datosOp) > 0) {

                                    # Actualzamos el Contador de Facturas Nuevas
                                    $anaNew++;

                                    foreach ($datosOp as $registro) {
                                        #var_dump($registro);

                                        foreach ($registro as $key => $value) {

                                            #echo "\n - " . $key . ": " . $value;
                                            $operador[$key] = $value;
                                        }
                                        
                                    }

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


                                    if (isset($operador["opEma"])) {
                                        # code...
                                        $datamail = array(
                                            "operator" => $nbop,
                                            "tipo" => $tipodoc,
                                            "documento" => $nbdoc,
                                            "mail" => $operador["opEma"]
                                        );

                                    }else{

                                        # Actualzamos el Contador de Análisis Incorrectos, Sin E-mail
                                        $anaSO++;
                                    }

                                }else{

                                    # Actualzamos el Contador de Facturas Incorrectas, Sin Operador
                                    $anaSO++;
                                }

                            }else{
                                # Si Existe la Factura en la BB.DD.
                                
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
                                            
                                            $query = $em->createQuery('SELECT ope.codigo, ope.opNop, ope.opEma
                                                             FROM App\Entity\Operator ope
                                                            WHERE ope.opNop = :nom')->setParameter('nom', $nbop);

                                            $datosOp = $query->getResult();

                                            $operador = array();

                                            # Si el Operador Existe, NO es Nulo, en el Sistema
                                            if (count($datosOp) > 0) {

                                                # Actualzamos el Contador de Facturas Actualizadas
                                                $anaUpdate++;

                                                foreach ($datosOp as $registro) {
                                                    #var_dump($registro);

                                                    foreach ($registro as $key => $value) {
                                                        # code...
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
                                                        "mail" => $operador["opEma"]
                                                    );

                                                }else{

                                                    # Actualzamos el Contador de Análisis Incorrectos, Sin E-mail
                                                    $anaSO++;
                                                }

                                                # var_dump($datamail);
                                                # exit('Parámetros para Envío Mail de Documento Actualizado');
                                            }else{

                                                # Actualzamos el Contador de Facturas Incorrectas, Sin Operador
                                                $anaSO++;
                                            }
                                        }
                                    }

                                # exit('Entro en Facturas Existentes');
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
                        # exit('Envió de Mail Realizado');
                    }
                    
                }else{

                    # Documentos NO Válidos
                    switch ($tipodoc) {
                        case 'factura':
                            # Facturas No Procesadas
                            $docNVFact = $docNV++;;
                            break;

                        case 'certificado':
                            # Certificados No Válidos
                            $docNVCert = $docNV++;;
                            break;

                        case 'carta':
                            # Cartas No Válidas
                            $docNVCart = $docNV++;;
                            break;

                        case 'analisis':
                            # Cartas No Válidas
                            $docNVAna = $docNV++;;
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
        $urlBase = $this->path_update_logs;

        # Definimos Variable de Fin de Ejecución 
        $end = date("Y-m-d H:i:s");

        # Definimos la Ruta Completa y el Nombre del Fichero LOG que se va a generar
        $path_file = $urlBase.'update_datedocuments_'.date("d_m_Y").'.log';

        # Abrimos el Archivo con Permisos de Sobrescritura
        $log = fopen($path_file, "w+");

        # Escribimos Comienzo y Fin de Ejecución
        fwrite(
            $log,
            ("\n* DOCUMENTOS FTP => Comienzo: ". $now ." | Final: ". $end ."\n")
        );

        # Escribimos Información sobre Facturas
        fwrite(
            $log,
            ("\n - Facturas => Total: ". $totalFact ." | Procesadas: ". $procFact ." | Nuevas: ". $facNew ." | Actualizadas: ". $facUpdate ." | Sin Operador: ". $facSO ."\n")
        );

        # Escribimos Información sobre Certificados
        fwrite(
            $log,
            ("\n - Certificados => Total: ". $totalCert ." | Procesados: ". $procCert ." | Nuevos: ". $cerNew ." | Actualizados: ". $cerUpdate ." | Sin Operador: ". $cerSO ."\n")
        );

        # Escribimos Información sobre Cartas
        fwrite(
            $log,
            ("\n - Cartas => Total: ". $totalCart ." | Procesadas: ". $procCart ." | Nuevas: ". $carNew ." | Actualizadas: ". $carUpdate ." | Sin Operador: ". $carSO ."\n")
        );

        # Escribimos Información sobre Cartas
        fwrite(
            $log,
            ("\n - Análisis => Total: ". $totalAna ." | Procesadas: ". $procAna ." | Nuevas: ". $anaNew ." | Actualizadas: ". $anaUpdate ." | Sin Operador: ". $anaSO ."\n")
        );

        # Escribimos Información sobre Documentos No Válidos
        fwrite(
            $log,
            ("\n - Documentos NO Válidos => Facturas: ". $docNVFact ." | Certificados: " . $docNVCert ." | Cartas: " . $docNVCart ." | Análisis: " . $docNVAna . "\n")
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
   /* public function isEnabled()
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

        # Según el Tipo de Documento Extraemos SÓLO el Nombre de los Documentos
        switch ($tipodoc) {
            case 'factura':
                
                $nbdoc = substr($datos['documento'], 18);

                break;

            case 'analisis':
            case 'certificado':
            case 'carta':
                
                $nbdoc = substr($datos['documento'], 8);

                break;
            
            default:
                # code...
                break;
        }
        

        $from  = 'noreply@sohiscert.com';
        # $to = $destino;
        $to = 'manuel.navarro@atlantic.es';
        $subject = "Area Privada Sohiscert - Notificacion de Actualizacion de Documentacion del Operador"; 
        
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
                                            <td><br><br></td>
                                        </tr>
                                        <tr>
                                            <td valign="top" class="body-cell">
                                                <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff">
                                                    <tr>
                                                        <td valign="top"
                                                            style="padding-bottom:20px; background-color:#ffffff;">
                                                            <h2><p align="justify"><b>Notificación de Actualización de Documentos</b></h2>
                                                            <br>&nbsp;<br>
                                                            <p align="justify">Desde SOHISCERT le informamos que ya tiene disponible los siguientes documentos en su Área Privada web:se ha actualizado su <b>' . $tipodoc . ', '. $nbdoc . ',</b> del operador <b>' .  $nbop . '</b> en su Área Privada.
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><br><br></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>- Documento: ' . $nbop . '</b>
                                                            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>- Tipo de documento: ' . $tipodoc . '</b></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><br>&nbsp;<br>&nbsp;<br></td>
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
                                                <p>Si usted no disponer de clave y usuario para acceder, solicitelas enviando un email a: <a href="mailto:areaprivadaweb@sohiscert.com">areaprivadaweb@sohiscert.com</a> o llamando al teléfono 955868051
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

        /*$body  = '


        <!DOCTYPE html>
        <html>
        <head>
            <title>Area Privada Sohiscert - Notificación de Actualización de Documentación del Operador</title>
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
                                            <td><br><br></td>
                                        </tr>
                                        <tr>
                                            <td valign="top" class="body-cell">
                                                <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff">
                                                    <tr>
                                                        <td valign="top"
                                                            style="padding-bottom:20px; background-color:#ffffff;">
                                                            <h2><p align="justify"><b>Notificación de Actualización de Documentación</b></h2>
                                                            <br>&nbsp;<br>
                                                            <p>Estimado Cliente,
                                                            <p align="justify">desde Sohiscert le informamos que se ha actualizado su <b>' . $tipodoc . ', '. $nbdoc . ',</b> del operador <b>' .  $nbop . '</b> en su Área Privada.
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><br><br></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>- Documento: ' . $nbdoc . '</b>
                                                            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>- Tipo: ' . $tipodoc . '</b></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><br>&nbsp;<br>&nbsp;<br></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            Saludos.
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td valign="top" class="footer-cell">
                                                <br>
                                                <br>
                                                <p>Email de Contacto: <a href="mailto:sohiscert@sohiscert.com">Sohiscert</a>
                                                </p>
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
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ';*/

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
