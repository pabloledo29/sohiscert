<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DependencyInjection\ContainerBuilder; 

use App\Entity\DocumentosFTP;
use App\Entity\Operator;

use App\Mailer\Mailer;


use Symfony\Component\Console\Command\Command;

/**
 * Class UpdateDateDocumentsOperatorsClientCommand
 * @package App\Command
 */
class UpdateDateDocumentsOperatorsClientCommand extends Command
{
    protected static $defaultName = 'gsbase:update:datedocuments';
    public function __construct(string $path_update_logs)
    {
        $this->path_update_logs= $path_update_logs;
         // you *must* call the parent constructor
         parent::__construct();
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('gsbase:update:datedocuments')
            ->setDescription('Comando que actualiza la fecha de los documentos actualizados o subidos');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new \DateTime('now');

        $documentsCreated = 0;
        $documentsProcessed = 0;
        $updateDateDoc = 0;

        $rutasftp = array('factura' => '/facturasintranet', 'documento' => '/Documentos/Documentos/', 'general' => '/Documentos/General/', 'certificado' => '/SITIO2', 'analisis' => '/SITIO1', 'carta' => '/SITIO3');

        $ftp_server = 'sohiscert3.ddns.cyberoam.com'; #'sohiscert3.ddns.cyberoam.com';
        $ftp_user_name = 'userftp1';
        $ftp_user_pass = 'AtlIntTec.12';

        # Establecemos Conexión 
        $conn_id = ftp_connect($ftp_server); 

        # Inciamos Sesión
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

        # Verificamos la Conexión
        if ((!$conn_id) || (!$login_result)) {  
            echo "\n ¡La conexión FTP ha fallado!";
            echo "\n Se intentó conectar al $ftp_server por el usuario $ftp_user_name"; 
            echo " \n";
            exit(); 

        } else {
            echo "\n Conexión a $ftp_server realizada con éxito, por el usuario " . $ftp_user_name . " \n";
        }


        foreach ($rutasftp as $tipodoc => $ruta) {

            # Obtener el número de archivos contenidos en el directorio actual
            $lista = ftp_nlist($conn_id, $ruta);
            $numarch = count($lista);

            echo "\n - Documentos " . $tipodoc . ": " . $numarch . " archivos";

            $val = 0;
            #$mlr = 0;

            for ($i=0; $i < $numarch ; $i++) { 
                
                # Comprobamos sólo los archivos PDF de los Directorios definidos del Servidor FTP
                # y a su vez, quellos que NO Contengan Untitled y/o NOP

                if ((strpos($lista[$i], '.pdf') !== false) && (strpos($lista[$i], 'Untitled') === false)) {
                    # code...
                    $val++;

                    $em = new ContainerBuilder();
                    $em =  $em->container->get('doctrine')->getManager();
                    //$archivo = $em->getRepository('AppBundle:DocumentosFTP')->findOneBy(array('nbDoc' => ' . $lista[$i]->getNbDoc() . '));
                    $archivo = $em->getRepository(DocumentosFTP::class)->findOneByNbDoc($lista[$i]);
                    //$archivo = $em->getRepository('AppBundle:DocumentosFTP')->findOneByNbDoc(' . $lista[$i] . ');


                    switch ($tipodoc) {
                        case 'factura':
                            
                            # Obtenemos la Fecha de Modificación del Archivo FTP
                            $docftp = ftp_mdtm($conn_id, $lista[$i]);
                            $fechadoc = date("Y-m-d H:i:s", $docftp);

                            # Si no existe el archivo en la tabla
                            if (!$archivo) {

                                # Obtenemos el Operador para ello
                                #  - tenemos que localizar la posición del "-"
                                #  - eliminar la cadena de texto hasta la posción del "-"
                                #  - y al texto resultante le quitamos el "-" y la extensión ".pdf"
                                $posg = strpos($lista[$i], '-');

                                $op = substr($lista[$i], $posg);
                                $op = trim($op, '-');
                                $nbop = trim($op, '.pdf');

                                //var_dump($nbop);

                                # Obtenemos el Código del Operador a partir del Nombre
                                $cons = $em->container->get('doctrine')->getManager();
                                //$codop = $cons->getRepository(Operator::class)->findOneBy(array('opNop' => ' . $nbop->getOpNop() .'));
                                $datosOp = $cons->getRepository(Operator::class)->findOneByOpNop($nbop);
                                $operador = array();

                                if ($datosOp) {

                                    foreach ($datosOp as $registro) {
                                        var_dump($registro);

                                        foreach ($registro as $key => $value) {
                                            # code...
                                            # echo "\n - " . $key . ": " . $value;
                                            $operador[$key] = $value;
                                        }
                                        
                                    }

                                    # Recuperamos Sólo el Nombre del Documeto para Almacenar Sin la Ruta 
                                    $numcardoc = strlen($lista[$i]);
                                    $nbdoc = substr($lista[$i], 18);
                                
                                    # Inicializamos Variable y Añadimos los Valores para el Nuevo Documento
                                    $docNew = new DocumentosFTP();

                                    $docNew->setOpCdp($operador["opCdp"]);
                                    $docNew->setOpNop($nbop);
                                    $docNew->setTipoDoc($tipodoc);
                                    $docNew->setNbDoc($nbdoc);
                                    $docNew->setFechaDoc(new \DateTime($fechadoc));


                                    $em->persist($docNew);
                                    $em->flush();

                                    var_dump($docNew);


                                    $datamail = array(
                                        "operator" => $nbop,
                                        "tipo" => $tipodoc,
                                        "documento" => $nbdoc,
                                        "mail" => $operador["opEma"]
                                        );


                                    # Datos de Actualización para Remitir por Mail

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

                                    # $message = $this->createMessage($input);
                                    $message = $this->createMessage($datamail);
                                    $mailer = $em->container->get($mailerServiceName);
                                    
                                    $output->writeln(sprintf('<info>Sent %s emails<info>', $mailer->send($message)));

                                    /*
                                    $datamail = array(
                                        "operator" => $nbop,
                                        "tipo" => $tipodoc,
                                        "documento" => $nbdoc,
                                        "mail" => $operador["opEma"]
                                        );

                                    $mailer = $em->container->get('app.mailer.service');
                                    $resp = $mailer->sendFileNotificationTotheOperator($datamail);

                                    var_dump($resp);
                                    */

                                    /*
                                    $mailer = $em->container->get('mailer'); 
                                    $spool = $mailer->getTransport()->getSpool(); 
                                    $transport = $em->container->get('swiftmailer.transport.real'); 

                                    $sender  = 'noreply@sohiscert.com'; 
                                    $recipient = $operador["opEma"]; 
                                    $title  = 'Area Privada Sohiscert - Notificación de Actualización de Documentación del Operador'; 
                                    
                                    $body  = '

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
                                                                                        <h2>Notificación Actualización Documentación Operador</h2>
                                                                                        Estimado Cliente, <p>
                                                                                        desde Sohiscert le informamos que se ha actualizado su <b>' . $tipodoc . ', '. $nbdoc . ',</b> del operador <b>' .  $nbop . '</b> en su Área Privada.
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td><br><br></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>
                                                                                        <h2>Documento: ' . $tipodoc . '</h2>
                                                                                        <p>' . $nbdoc . '</p>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td><br><br></td>
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
                                                                            <p>Email contacto: <a href="mailto:sohiscert@sohiscert.com">Sohiscert</a>
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

                                    $email = $mailer->createMessage() 
                                     ->setSubject($title) 
                                     ->setFrom($sender) 
                                     ->setTo($recipient) 
                                     ->setCharset($charset) 
                                     ->setContentType('text/html') 
                                     ->setBody($body) 
                                    ; 

                                    $send = $mailer->send($email); 
                                    $spool->flushQueue($transport); 
                                    */

                                    
                                    
                                    exit();
                                }
                            }
                            break;

                        case 'documento':
                            # code...
                            break;

                        case 'general':
                            # code...
                            break;

                        case 'certificado':
                            # code...
                            break;

                        case 'analisis':
                            # code...
                            break;

                        case 'carta':
                            # code...
                            break;
                        
                        default:
                            # code...
                            break;
                    }

                }else{
                    echo "\n - Documento NO Válido: " . $lista[$i];
                }
            }

            echo "\n - Documentos " . $tipodoc . " válidos: " . $val . " archivos";
            echo " \n";
            
        }


        # Cerramos la Conexión FTP 
        ftp_close($conn_id);

        exit();



        $urlBase = $this->path_update_logs;

        if ($updateDateDoc > 0 || $documentsCreated > 0) {
            $end = new \DateTime('now');

            $path_file = $urlBase.'update_datedocuments_'.date("d_m_Y").'.log';
            $log = fopen($path_file, "w+");
            fwrite(
                $log,
                ("DOCUMENTOS => Comienzo: ".$now->format('H:i:s')." | Final: ".$end->format(
                        'H:i:s'
                    )." | Documentos Procesados: ".$documentsProcessed." | Docuemntos Creados: ".$documentsCreated." | Docuemntos Actualizados: ".$updateDateDoc)."\n"
            );
            fclose($log);
        }
        return 0;
    }



    
    /*public function isEnabled(): bool
    {
        $em = new ContainerBuilder();
        return $em->has("mailer");
    }*/


    /**
     * Creates new message from input options.
     *
     * @param InputInterface $input An InputInterface instance
     *
     * @return \Swift_Message New message
     */
    private function createMessage(Array $input)
    {

        $nbope = $input["operator"];
        $tipodoc = $input["tipo"];
        $nbdoc = $input["documento"];

        $from  = 'noreply@sohiscert.com'; 
        $to = 'co.ferrete@atlantic.es'; #$operador["opEma"]; 
        $subject  = 'Area Privada Sohiscert - Notificación de Actualización de Documentación del Operador'; 
        
        $body  = '

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
                                                            <h2>Notificación Actualización Documentación Operador</h2>
                                                            Estimado Cliente, <p>
                                                            desde Sohiscert le informamos que se ha actualizado su <b>' . $tipodoc . ', '. $nbdoc . ',</b> del operador <b>' .  $nbop . '</b> en su Área Privada.
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><br><br></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <h2>Documento: ' . $tipodoc . '</h2>
                                                            <p>' . $nbdoc . '</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><br><br></td>
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
                                                <p>Email contacto: <a href="mailto:sohiscert@sohiscert.com">Sohiscert</a>
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

        $message = (new \Swift_Message(
            $input->getOption($subject),
            $input->getOption($body),
            $input->getOption('text/html'),
            $input->getOption($charset)
        ));
        $message->setFrom($input->getOption($from));
        $message->setTo($input->getOption($to));

        return $message;
    }

}
