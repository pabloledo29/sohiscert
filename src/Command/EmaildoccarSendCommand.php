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
use App\Entity\OpNopTransform;

use App\Entity\DocumentosFTP;
use Swift_Mailer;
use Swift_SmtpTransport;

use App\Command\Exception;
/**
 * Class EmaildoccarSendCommand
 * @package App\Command
 */
class EmaildoccarSendCommand extends Command
{
    protected static $defaultName = 'email:emaildoccar:send';
    public function __construct(string $path_update_logs,string $ftp_server, string $ftp_user_name, string $ftp_user_pass, $mailer,$em)
    {
        $this->path_update_logs = $path_update_logs;
         # Datos Conexión FTP para poder Obtener Fecha Modificación de los Archivos
        $this->ftp_server = $ftp_server;
        $this->ftp_user_name = $ftp_user_name;
        $this->ftp_user_pass = $ftp_user_pass;
        

        $this->mailer=$mailer;
        $this->em = $em;
        
         // you *must* call the parent constructor
         parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('email:emaildoccar:send') 
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
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        # Definimos Variable de Cominezo de Ejecución 
        $now = date("Y-m-d H:i:s");

        # Definimos e Inicializamos Contadores de
        # Facturas, Cartas, Certificados, Análisis y Mails a Remitir
        $docNVFact = 0;

        $docNVCart = 0;

        $cerNew = 0;
        $cerUpdate = 0;
        $cerSO = 0;
        $totalCert = 0;
        $procCert = 0;
        $docNVCert = 0;

        $docNVAna = 0;

        $contMail = 0;

        # Rutas de los Documentos en el Servidor FTP antigua
        #$rutasftp = array('factura' => '/facturasintranet', 'certificado' => '/SITIO2', 'carta' => '/SITIO3', 'analisis' => "/SITIO1");

        # Obtener Fechas, Día Actual y Día de la Semana Atrás desde el día actual
        $diahoy = date('Y-m-d', time());
        $diahoy = strtotime($diahoy);

        $semantes = '2021-06-01';
        //$semantes = date('Y-m-d', strtotime('-1 month'));
        $semantes = strtotime($semantes);
       
        # Rutas de los Documentos en el Servidor FTP    
        $rutasftp = array('carta' => '/DEPARTAMENTO CERTIFICACION/DECISIÓN DE CERTIFICACIÓN/COMUNICACION DE LA COMISION DE CERTIFICACION');

        $em = $this->em;
        $mapeo_nop = $em->getRepository(OpNopTransform::class)->findAll();
        $lista_mapeo = [];
        foreach ($mapeo_nop as $mapeo){
            $lista_mapeo[$mapeo->getOpNop()] = $mapeo->getopNopTransform();
        }
        #MNN Creamos el archivo update de reccorridos de archivos de certificados
        $urlBase = $this->path_update_logs;

        # Definimos Variable de Fin de Ejecución 
        $end = date("Y-m-d H:i:s");

        # Definimos la Ruta Completa y el Nombre del Fichero LOG que se va a generar
        $path_file = $urlBase.'register_recorridos_CARTA1_'.date("d_m_Y").'.log';

        # Abrimos el Archivo con Permisos de Sobrescritura
        $log = fopen($path_file, "w+");

        fwrite($log,("\n* ARCHIVOS DE CARTAS\n"));

        #MNN
        $conn_id = ftp_connect($this->ftp_server);

        # Inciamos Sesión
        $login_result = ftp_login($conn_id, $this->ftp_user_name, $this->ftp_user_pass); 
        # Verificamos la Conexión
        if ((!$conn_id) || (!$login_result)) {
            exit(); 
        } else {
            echo "\n Conexión a $this->ftp_server realizada con éxito, por el usuario " . $this->ftp_user_name . " \n";
        }

        # Recorremos los Directorios FTP definidos anteriormente en las rutas
        # Definimos la Ruta
        foreach ($rutasftp as $tipodoc => $ruta) {

           # Habilitamos la Conexión Pasiva del FTP
           ftp_pasv($conn_id, true);

           # Obtener el número de archivos contenidos en el directorio actual
           $lista= ftp_nlist($conn_id,$ruta);
           $numarch = count($lista);


            echo "\n -> Documentos, " . $tipodoc . ": " . $numarch . " archivos \n";

            switch ($tipodoc) {

                case 'carta':
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

            echo "\n Procesando " . $tipodoc . "... \n";

            # Recorremos Archivo por Archivo por Directorio
            $numarch_hasta= $numarch/4;

            for ($i=0; $i <= $numarch_hasta ; $i++) {

                
                switch ($tipodoc) {
                   
                    case 'carta':
                       
                        # Certificados Procesados
                        $procCert = $contArch++;
                        break;
                    
                    default:
                        # code...
                        break;
                }

                # Obtenemos la Fecha de Modificación del Archivo FTP
                unset($docftp);
                $docftp = ftp_mdtm($conn_id, $lista[$i]);

                if ($docftp==-1){
                    unset($docftp);
                  
                    ftp_close($conn_id);
                    unset($conn_id);
                                 
                    fwrite($log,("\n FALLO, INTENTANDO CONECTAR DE NUEVO"));    
                    
                    $conn_id = ftp_connect($this->ftp_server);

                    # Inciamos Sesión
                    $login_result = ftp_login($conn_id, $this->ftp_user_name, $this->ftp_user_pass); 

                    if (!$login_result) {  
                        echo "\n ¡La conexión FTP ha fallado DESPUES DEL ERROR!\n";
                        echo " \n"; 
                        exit(); 

                    } else {
                        echo "\n ERROR AL OBTENER FECHA!!! VOLVEMOS A CONECTAR AL SERVIDOR " . $this->ftp_user_name . " \n";
                        fwrite($log,("\n FALLO, CONECTAMOS DE NUEVO Y SEGUIMOS"));
                    }

                    $docftp = ftp_mdtm($conn_id, $lista[$i]);
                    
                }               

                $fmoddoc = date("Y-m-d", $docftp);
                $fecha_bruta=$fmoddoc;

                $fmoddoc = strtotime($fmoddoc);

                echo " " . $i . "\r";       

                # Escribimos Comienzo y Fin de Ejecución
                fwrite($log,("\n* CONTADOR: ". $conn_id ." | Fecha numerica: ". $docftp ." | FECHA REAL: ".$fecha_bruta." RUTA: ".$lista[$i]."\n"));

                if (($fmoddoc >= $semantes) && ($fmoddoc <= $diahoy)) {
      
                # Comprobamos sólo los archivos PDF de los Directorios definidos del Servidor FTP
                # y a su vez, aquellos que NO Contengan Untitled
                if ((strpos($lista[$i], '.pdf') !== false) && (strpos($lista[$i], 'Untitled') === false) && (strpos($lista[$i], '-') !== false)) {
               
                    $archivo = $em->getRepository(DocumentosFTP::class)->findOneByNbDoc($lista[$i]);

                    switch ($tipodoc) {

                        case 'carta':
                            
                            # Obtenemos la Fecha de Modificación del Archivo FTP
                            $docftp = ftp_mdtm($conn_id, $lista[$i]);
                            $fechadoc = date("Y-m-d H:i:s", $docftp);

                            # Si NO Existe la Factura en BB.DD.
                            if (!isset($archivo)) {
                                
                                # Obtenemos el Operador para ello
                                #  - tenemos que localizar la posición del último "-"
                                #  - eliminar la cadena de texto hasta la posción del "-"
                                #  - y al texto resultante le quitamos el "-" y la extensión ".pdf"
                                $posg = strrpos($lista[$i], '-');
                                $op = substr($lista[$i], 0, $posg);
                                $op = substr(strrchr($op, '/'), 1);
                                $op = trim($op, '-');
                                
                                $encontrado = false;
                                foreach ($lista_mapeo as $mapeo_key => $mapeo_value){
                                    if(strpos($mapeo_value,str_replace("AE","",$op))!==false && !$encontrado){
                                        $encontrado = true;
                                        $nbop = $mapeo_key;

                                    }
                                }
                                if(!$encontrado){
                                    $optimizar_string1 = substr($op, 0, 1);
                                    $optimizar_string2 = strcmp($optimizar_string1, 'F');
                                    $optimizar_string3 = strcmp($optimizar_string1, '1');
                                    # Si el Documento No Contiene más '-' o No Empiece por F ni por 1
                                    if ((strrpos($op, '-') == false && strrpos($op, ' ') == false) || ($optimizar_string2 <> 0 && $optimizar_string3 <> 0)) { 
                                        $nbop = $op;
                                    # Si el Documenta Comienza por F, 1 o S
                                    }elseif ($optimizar_string2 == 0 || $optimizar_string3 == 0 || strcmp($optimizar_string1, 'S') == 0) {
                                        
                                        # Obtenemos el Nombre del Operador a partir de la última
                                        # posición del '-'
                                        $tamnc = strlen($op);
                                        $uposg = strrpos($op, '-');

                                        $nbop = substr($op, ($uposg + 1), $tamnc);

                                        # Si el Documento Comienza por NAQS o NOP
                                    }elseif (strcmp(substr($op, 0, 4), 'NAQS') == 0 || strcmp(substr($op, 0, 3), 'NOP') == 0) {
                                        
                                        # Obtenemos el Nombre del Operador a partir de la última
                                        # posición del '(espacio)'
                                        $tamnc = strlen($op);
                                        $uposg = strrpos($op, ' ');

                                        $nbop = substr($op, ($uposg + 1), $tamnc);
                                    }
                                }

                                #MNN consulta para versión PHP 5.6
                                $query = $em->createQuery('SELECT ope.codigo, ope.opNop, ope.opEma, reg.reDeno
                                                             FROM App\Entity\Operator ope
                                                             INNER JOIN App\Entity\Register reg WITH ope.opRegistro=reg.id
                                                            WHERE ope.opNop = :nom')->setParameter('nom', $nbop);

                                $datosOp = $query->getResult();

                                $operador = array();

                                # Si el Operador Existe, NO es Nulo, en el Sistema
                                if (count($datosOp) > 0) {

                                    # Actualzamos el Contador de Certificados Nuevos
                                    $cerNew++;

                                    foreach ($datosOp as $registro) {

                                        foreach ($registro as $key => $value) {
                                            # code...
                                            echo "\n - " . $key . ": " . $value;
                                            $operador[$key] = $value;
                                        }
                                        
                                    }

                                    # Recuperamos Sólo el Nombre del Documeto para Almacenar Sin la Ruta 
                                    # $nbdoc = substr($lista[$i], 18);
                                    $nbdoc = $lista[$i];
                                
                                    # Inicializamos Variable y Añadimos los Valores para el Nuevo Documento
                                    $docNew = new DocumentosFTP();
                                    
                                    if (isset($operador["opEma"])) {
                                        $datamail = array(
                                            "operator" => $nbop,
                                            "tipo" => $tipodoc,
                                            "documento" => $nbdoc,
                                            "mail" => $operador["opEma"],
                                            "alcance"=>$operador["reDeno"]

                                        );

                                        # Si Existen Datos de Actualización para Remitir por Mail 
                                        if (isset($datamail)) {
                                            if ($datamail['mail']!=''){                                              
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
                                                            $path_file_fail = $urlBase.'register_falladas_ANA_'.date("d_m_Y").'.log';
                                                            $open_file = fopen($path_file_fail,'a+');
                                                            fwrite($open_file,date("Y-m-d H:i:s"). "---->" .implode($datamail));
                                                            fclose($open_file);
                                                            $datamail["mail"] = null;
                                                        }
                                                    }
                                                }
                                                $em = $this->em;
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
                                            // Si $datamail tiene datos, se envía el email
                                            try {
                                                $message = $this->createMessage($input, $datamail);
                                                $mailer = $this->mailer;
                                                //$this->mandarMail($message, $mailer, $output);
                                                $output->writeln(sprintf('<info>Sent %s emails<info>', $mailer->send($message)));
                                                if($mailer->send($message) == 1){
                                                    if ($operador["opEma"]!=''){
                                                        if (isset($operador["opCdp"])) {
                                                            $docNew->setOpCdp($operador["opCdp"]);
                        
                                                        }else{
                                                            $docNew->setOpCdp(" ");
                                                        }
                                                        $docNew->setOpNop($nbop);
                                                        $docNew->setTipoDoc($tipodoc);
                                                        $docNew->setNbDoc($nbdoc);
                                                        $docNew->setFechaDoc(new \DateTime($fechadoc));
                                                        $docNew->setFechaEnv(new \DateTime());
                                                        $docNew->setMail($operador["opEma"]);
                        
                                                        $em->persist($docNew);
                                                        $em->flush();
                                                        echo "\n Registro guardado \n";
                                                    
                                                        foreach ($datosOp as $registro) {

                                                            foreach ($registro as $key => $value) {
                                                                # code...
                                                                echo "\n - " . $key . ": " . $value;
                                                            }
                                                            
                                                        }
                                                    } 
                                                }
                                                $contMail++;
                                                unset($datamail);
                                            } catch(\Exception $e) {
                                                echo ("Error al enviar mensaje: " + $e->getMessage());
                                            }
                                        }
                                    }
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
                                                                
                                # Recuèramos la Fecha Almacenada del Documento
                                $fechaalmdoc = $registro->getFechaDoc();
                                
                                # Convertimos la Fecha Almacenada del Documento en un Array
                                # para poder recuperar el valor almacenado del campo Date
                                $campo = (array) $fechaalmdoc;
                                
                                # Recorremos los Valores del Campo
                                foreach ($campo as $key => $value) {
                                    
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
                                            $nbop = $registro->getOpNop();
                                            $tipodoc = $registro->getTipoDoc();
                                            $nbdoc = $registro->getNbDoc();
                                            $registro->setFechaDoc(new \DateTime($fechadoc));

                                            $em->persist($registro);
                                            $em->flush();
                                            
                                            $query = $em->createQuery('SELECT ope.codigo, ope.opNop, ope.opEma, reg.reDeno
                                                             FROM App\Entity\Operator ope
                                                             INNER JOIN App\Entity\Register reg WITH ope.opRegistro=reg.id
                                                            WHERE ope.opNop = :nom')->setParameter('nom', $nbop);

                                            $datosOp = $query->getResult();

                                            $operador = array();

                                            # Si el Operador Existe, NO es Nulo, en el Sistema
                                            if (count($datosOp) > 0) {

                                                # Actualzamos el Contador de Certificados Actualizados
                                                $cerUpdate++;

                                                foreach ($datosOp as $registro) {

                                                    foreach ($registro as $key => $value) {
                                                        $operador[$key] = $value;
                                                    }
                                                    
                                                }

                                                # Parámetros para el Envío del Mail
                                                /*if (isset($operador["opEma"])) {
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
                                                }*/
                                            }else{
                                                # Actualzamos el Contador de Certificados Incorrectos, Sin Operador
                                                $cerSO++;
                                            }
                                        }
                                    }
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
                            var_dump($datamail["mail"]);
                             
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
                                        $path_file_fail = $urlBase.'register_falladas_CART_'.date("d_m_Y").'.log';
                                        $open_file = fopen($path_file_fail,'a+');
                                        fwrite($open_file,date("Y-m-d H:i:s"). "---->" .implode($datamail));
                                        fclose($open_file);
                                        $datamail["mail"] = null;
                                    } 
                                }
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

                        /*$message = $this->createMessage($input, $datamail);
                        $mailer = $this->mailer;
                        $output->writeln(sprintf('<info>Sent %s emails<info>', $mailer->send($message)));

                        if($mailer->send($message) == 1){
                            if ($operador["opEma"]!=''){
                                if (isset($operador["opCdp"])) {
                                    $docNew->setOpCdp($operador["opCdp"]);

                                }else{
                                    $docNew->setOpCdp(" ");
                                }
                                $docNew->setOpNop($nbop);
                                $docNew->setTipoDoc($tipodoc);
                                $docNew->setNbDoc($nbdoc);
                                $docNew->setFechaDoc(new \DateTime($fechadoc));
                                $docNew->setFechaEnv(new \DateTime());
                                $docNew->setMail($operador["opEma"]);

                                $em->persist($docNew);
                                $em->flush();
                            } 
                        }
                        //die("FIN!!!!!!!!!!!!!!");
                        $contMail++;
                        unset($datamail);*/
                        }
                    }
                    
                }else{
                    # Documentos NO Válidos
                    switch ($tipodoc) { 
                        case 'carta':
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
        ftp_close($conn_id);
        }

        echo "\n Proceso Finalizado, generando Archivo Log ...\n";

        # Archivo LOG con la Información Procesada
        # Definimos la Ruta
        $urlBase = $this->path_update_logs;

        # Definimos Variable de Fin de Ejecución 
        $end = date("Y-m-d H:i:s");

        # Definimos la Ruta Completa y el Nombre del Fichero LOG que se va a generar
        $path_file = $urlBase.'update_datedocuments_CARTAS1_'.date("d_m_Y").'.log';

        # Abrimos el Archivo con Permisos de Sobrescritura
        $log = fopen($path_file, "w+");

        # Escribimos Comienzo y Fin de Ejecución
        fwrite(
            $log,
            ("\n* DOCUMENTOS FTP => Comienzo: ". $now ." | Final: ". $end ."\n")
        );

        # Escribimos Información sobre Certificados
        fwrite(
            $log,
            ("\n - Cartas => Total: ". $totalCert ." | Procesados: ". $procCert ." | Nuevos: ". $cerNew ." | Actualizados: ". $cerUpdate ." | Sin Operador: ". $cerSO ."\n")
        );

        # Escribimos Información sobre Documentos No Válidos
        fwrite(
            $log,
            ("\n - Documentos NO Válidos => Cartas: ". $docNVFact ." | Cartas: " . $docNVCert ." | Cartas: " . $docNVCart ." | Análisis: " . $docNVAna . "\n")
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
            case 'factura':               
                $nbdoc = substr($datos['documento'], 18);
                break;
            case 'analisis':
            case 'certificado':
            case 'carta':
                $tipodoc2="Comunicación Decisión de Certificación";
                $nbdoc = substr($datos['documento'], 8);
                break;
            default:
                # code...
                break;
        }        

        $from  = 'noreply@sohiscert.com';
        $to = $destino;
        $to = "jlbarrios@atlantic.es";
        $subject = "Alta de documento en Área Privada web: Comunicación Decisión de Certificación"; 
        
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
                                                            <p><a target="_blank" href="https://intranet-sohiscert4.e4ff.pro-eu-west-1.openshiftapps.com/public/login" style="text-decoration:none"><font size="3px" face="arial" color="#40733c" weight="500"><b> ACCEDER AL ÁREA PRIVADA DE CLIENTES</b></font></a>
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