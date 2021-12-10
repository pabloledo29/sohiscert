<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Controller;

use App\Entity\Client;
use App\Entity\DocumentosFTP;
use App\Entity\Operator;
use App\Entity\OpNopTransform;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class FtpController
 *
 * Incluye controladores y métodos para interactuar con servidor FTP.
 *
 * @package App\Controller
 */
class FtpController extends AbstractController
{
    /**
     * Listado de Facturas de cliente provenientes de servidor FTP.
     *
     * Para ser usado por un UserOperator con Client asociado y ante un FTP
     * jerarquizado por categoría y con identificador basado en CIF
     *
     * @return Response
     * @Route("/private/facturacion/cliente", name="useroperator_billing_list")
     */
    public function userOperatorBillingList()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
        $cif = $this->getUser()->getUsername();
        $query = "billing"; // Parámetro de configuración

        $em = $this->getDoctrine()->getManager();
        $clients = $em->getRepository(Client::class)->findClients($cif);
        $ftp_server = $this->container->getParameter('ftp_server');
        $ftp_user_name = $this->container->getParameter('ftp_user_name');
        $ftp_user_pass = $this->container->getParameter('ftp_user_pass');
        
        $conn_id = ftp_connect($ftp_server);

        # Inciamos Sesión
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

        # Verificamos la Conexión
        if ((!$conn_id) || (!$login_result)) {  
            /*echo "\n ¡La conexión FTP ha fallado!";
            echo "\n Se intentó conectar al $ftp_server por el usuario $ftp_user_name"; 
            echo " \n";*/
            exit(); 

        } /*else {
            echo "\n Conexión a $ftp_server realizada con éxito, por el usuario " . $ftp_user_name . " \n";
        } */
        //llamada a función que devuelve array con ficheros y rutas.
        $operatorList = $this->get('app.ftp.service')->retrieveFilesFromClients($conn_id, $clients, $query);

        return $this->render(
            "private/useroperator_billing_filelist.html.twig",
            array(
                'fileList' => $operatorList,
            )
        );
    }

    /**
     * Descarga de Ficheros asociados a cliente desde FTP
     *
     * Descarga desde FTP en modo activo con parámetro de ruta absoluta sobre raiz.
     *
     * @param Request $request
     * @throws \Touki\FTP\Exception\DirectoryException
     * @Route("/private/download/file", name="useroperator_file_download")
     */
    public function userOperatorFileDownloadAction(Request $request, RouterInterface $router)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
       
        $path = $request->request->get('path');
        $user = $this->getUser();
     
        if (is_null($path)) {
            throw $this->createAccessDeniedException();
        }

        //Validación de permisos.
        $path_aux=str_replace('.pdf',"",$path);
        
            $clientId = preg_split('/\/|-/', $path_aux);
        
          
        
        $mapeo_nop = $this->getDoctrine()->getManager()->getRepository(OpNopTransform::class)->findAll();
        $lista_mapeo = [];
        foreach ($mapeo_nop as $mapeo){
            $lista_mapeo[$mapeo->getOpNop()] = $mapeo->getopNopTransform();
        }

   
        // Si solo hubiese un cliente para un usuario esta sería la manera más logica
//        if ($clientId !== $user->getClientId()->getCodigo()) {
//            throw $this->createAccessDeniedException();
//        }
        
        $cif = $user->getUsername();
        $clients = $this->getDoctrine()->getManager()->getRepository(Client::class)->findClientsNop($cif);
        $list = [];
        foreach ($clients as $cod) {
            array_push($list,  str_replace("/","",$cod['opNop']));

        }
       
        
        $list_aux = str_replace('AE','',$list);
       
        $i = 0;
        $encontrado = false; 
        
        
        while ($i< sizeof($clientId) && !$encontrado){
            
            $client_aux = str_replace('AE','',$clientId[$i]);
            
            if (!$encontrado && strpos($client_aux,'SHC',0)!==false){
                $client_aux= 'SHC-' . $clientId[($i + 1)] . '-' . $clientId[($i +2)];
                
            }
        

            if (in_array($client_aux, $list_aux) || in_array($client_aux, $lista_mapeo) ) {
                
                $encontrado =true;
            }
            $i++;
        }

        if(!$encontrado){
            throw $this->createAccessDeniedException();
        }
        $i--;
    


        $ftp_server = $this->container->getParameter('ftp_server');
        $ftp_user_name = $this->container->getParameter('ftp_user_name');
        $ftp_user_pass = $this->container->getParameter('ftp_user_pass');
        
        $conn_id = ftp_connect($ftp_server);

        # Inciamos Sesión
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 
        
        # Verificamos la Conexión
        if ((!$conn_id) || (!$login_result)) {  
            /*echo "\n ¡La conexión FTP ha fallado!";
            echo "\n Se intentó conectar al $ftp_server por el usuario $ftp_user_name"; 
            echo " \n";*/
            exit(); 

        } /*else {
            echo "\n Conexión a $ftp_server realizada con éxito, por el usuario " . $ftp_user_name . " \n";
        }*/
        $em = $this->getDoctrine()->getManager();
        $i = 0;
        $enc =false;
        while ($i < sizeof($clients) && !$enc) {
              
        
            $file = $this->setextraerVisitasNum([$path],$clients[$i]['opNop']);
            if($file !=0){
                $enc = true;
            }
            $i = $i+1;
        }
 
        $this->downloadFileAction($conn_id,$path);
    }

    /**
     * Listado de Documentos Generales
     *
     * Para listado no jerarquizado de un directorio de documentos sin permisos asociados.
     *
     * @return Response
     * @Route("/private/documents", name="useroperator_documents_list")
     */
    public function userOperatorDocumentsListAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
        $query = "general"; // Parámetro de configuración
        $ftp_server = $this->container->get('ftp_server');
        $ftp_user_name = $this->container->getParameter('ftp_user_name');
        $ftp_user_pass = $this->container->getParameter('ftp_user_pass');
        
        $conn_id = ftp_connect($ftp_server);

        # Inciamos Sesión
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

        # Verificamos la Conexión
        if ((!$conn_id) || (!$login_result)) {  
/*            echo "\n ¡La conexión FTP ha fallado!";
            echo "\n Se intentó conectar al $ftp_server por el usuario $ftp_user_name"; 
            echo " \n";*/
            exit(); 

        } else {
            echo "\n Conexión a $ftp_server realizada con éxito, por el usuario " . $ftp_user_name . " \n";
        }
        return $this->render(
            "private/useroperator_general_documents.html.twig",
            array(
                'filelist' => $this->get('app.ftp.service')->retrieveGeneralDocuments($conn_id,$query),
            )
        );
    }

    /**
     * Descarga de documentos generales
     *
     * Para descarga en modo activo de un documento no securizado desde un directorio.
     * Recibe el path absoluto de dicho fichero y no valida permisos.
     *
     * @param Request $request
     * @throws \Touki\FTP\Exception\DirectoryException
     * @Route("/private/documents/download", name="useroperator_generaldoc_show")
     */
    public function userOperatorGeneralDocShowAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
        $path = $request->request->get('path');
        
        $ftp_server = $this->container->getParameter('ftp_server');
        $ftp_user_name = $this->container->getParameter('ftp_user_name');
        $ftp_user_pass = $this->container->getParameter('ftp_user_pass');
        
        $conn_id = ftp_connect($ftp_server);

        # Inciamos Sesión
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

        # Verificamos la Conexión
        if ((!$conn_id) || (!$login_result)) {  
/*            echo "\n ¡La conexión FTP ha fallado!";
            echo "\n Se intentó conectar al $ftp_server por el usuario $ftp_user_name"; 
            echo " \n";*/
            exit(); 

        } else {
            echo "\n Conexión a $ftp_server realizada con éxito, por el usuario " . $ftp_user_name . " \n";
        }
        $ftp_server = $this->container->getParameter('ftp_server');
        $ftp_user_name = $this->container->getParameter('ftp_user_name');
        $ftp_user_pass = $this->container->getParameter('ftp_user_pass');
        
        $conn_id = ftp_connect($ftp_server);

        # Inciamos Sesión
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

        # Verificamos la Conexión
        if ((!$conn_id) || (!$login_result)) {  
/*            echo "\n ¡La conexión FTP ha fallado!";
            echo "\n Se intentó conectar al $ftp_server por el usuario $ftp_user_name"; 
            echo " \n";*/
            exit(); 

        } /*else {
            echo "\n Conexión a $ftp_server realizada con éxito, por el usuario " . $ftp_user_name . " \n";
        }*/
        /* Validación del path para descarga */
        if ($this->get('app.ftp.service')->validPath($conn_id, $path, 'general')) {
            $this->downloadFileAction($conn_id,$path);
        } else {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * Controller que genera un listado de documentos de factura de un servidor FTP.
     *
     * Recibe el CIF de cliente que convierte a los posibles códigos de gsBase de
     * cliente para listar las facturas disponibles.
     *
     * @param Request $request
     * @return Response
     * @Route("/admin/facturacion/cliente", name="admin_useroperator_billingclient")
     */
    public function adminBillingClientListAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $cif = $request->request->get('cif');
        $query = $request->request->get('query');

        if (is_null($cif) || is_null($query)) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $clients = $em->getRepository(Client::class)->findClients($cif);
        $ftp_server = $this->container->getParameter('ftp_server');
        $ftp_user_name = $this->container->getParameter('ftp_user_name');
        $ftp_user_pass = $this->container->getParameter('ftp_user_pass');
        
        $conn_id = ftp_connect($ftp_server);

        # Inciamos Sesión
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

        # Verificamos la Conexión
        if ((!$conn_id) || (!$login_result)) {  
            /*echo "\n ¡La conexión FTP ha fallado!";
            echo "\n Se intentó conectar al $ftp_server por el usuario $ftp_user_name"; 
            echo " \n";*/
            exit(); 

        } /*else {
            echo "\n Conexión a $ftp_server realizada con éxito, por el usuario " . $ftp_user_name . " \n";
        }*/
        //llamada a función que devuelve array con ficheros y rutas.
        $operatorList = $this->get('app.ftp.service')->retrieveFilesFromClients($conn_id,$clients, $query);

        return $this->render(
            "admin/useroperator_billing_filelist.html.twig",
            array(
                'fileList' => $operatorList,
            )
        );
    }

    


    /**
     * Controller para descarga de fichero desde FTP para usuario Administrador.
     *
     * Dado un path absoluto.
     *
     * @param Request $request
     * @throws \Touki\FTP\Exception\DirectoryException
     * @Route("/admin/download/file", name="admin_file_download")
     */
    public function adminDownloadFileAction(Request $request, RouterInterface $router)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        
        $path = $request->request->get('path');
        $nop = $request->request->get('opNop');

        if (is_null($path)) {
            throw $this->createAccessDeniedException();
        }
        $ftp_server = $this->container->getParameter('ftp_server');
        $ftp_user_name = $this->container->getParameter('ftp_user_name');
        $ftp_user_pass = $this->container->getParameter('ftp_user_pass');
        
        $conn_id = ftp_connect($ftp_server);

        # Inciamos Sesión
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 
        
        # Verificamos la Conexión
        if ((!$conn_id) || (!$login_result)) {  
            /*echo "\n ¡La conexión FTP ha fallado!";
            echo "\n Se intentó conectar al $ftp_server por el usuario $ftp_user_name"; 
            echo " \n";*/
            exit(); 

        } /*else {
            echo "\n Conexión a $ftp_server realizada con éxito, por el usuario " . $ftp_user_name . " \n";
        }*/
        $em = $this->getDoctrine()->getManager();
        $file = $this->extraerVisitas([$path],$nop);
        if($file and count($file)>0){
            $file = $file[0];
            if($file instanceof \App\Entity\DocumentosFTP)
            {
                $file->setVisitas($file->getVisitas() + 1);
                $em->persist($file);
                $em->flush();
                
               
            }

        }

        $this->downloadFileAction($conn_id,$path);
        
    }

    /**
     * Controller para descarga de fichero desde FTP para un UserOperator.
     *
     * Dado un path absoluto.
     *
     * @param Request $request
     * @throws \Touki\FTP\Exception\DirectoryException
     * 
     */
   /* public function userOperatorDownloadFileAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }

        $path = $request->request->get('path');

        if (is_null($path)) {
            throw $this->createAccessDeniedException();
        }
        $ftp_server = $this->container->getParameter('ftp_server');
        $ftp_user_name = $this->container->getParameter('ftp_user_name');
        $ftp_user_pass = $this->container->getParameter('ftp_user_pass');
        
        $conn_id = ftp_connect($ftp_server);

        # Inciamos Sesión
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

        # Verificamos la Conexión
        if ((!$conn_id) || (!$login_result)) {  */
            /*echo "\n ¡La conexión FTP ha fallado!";
            echo "\n Se intentó conectar al $ftp_server por el usuario $ftp_user_name"; 
            echo " \n";*/
           // exit(); 

     //   }/* else {
      //      echo "\n Conexión a $ftp_server realizada con éxito, por el usuario " . $ftp_user_name . " \n";
      //  }*/

      //  $this->downloadFileAction($conn_id,$path);
   // }

    /**
     * Método que descarga un fichero desde un servidor FTP.
     *
     * Recibido un path absoluto y validados los permisos previamente.
     *
     * @param $path
     * @return Response
     * Descarga de documentos del servidor FTP.
     */
    private function downloadFileAction($conn_id,$path)
    {
        
        ftp_pasv($conn_id, true);

        $filename = substr(strrchr($path, '/'), 1);

        $path_file = $this->container->getParameter('repo_dir') . // Path server
            'public/docs/temp/' . date("d_m_Y_h_i_s") . $filename;

        
        

        $file_open = fopen($path_file, "a+");
        fclose($file_open);
       
        $ret = ftp_get($conn_id, $path_file, $path, FTP_BINARY);
        
      
        if($ret){
            
            
            
            /*   Pruebas descarga  */
            basename(__FILE__, '.php');
            $response = new Response();
            $response->headers->set('Cache-Control', 'private');
            
            $response->headers->set('Content-type', finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path_file));
            
            $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($path_file) . '";');
            
            $response->headers->set('Content-length', filesize($path_file));
            
            $response->sendHeaders();
            
            $file = fopen($path_file, 'rb');
            
            if ( $file !== false ) {
                fpassthru($file);
                fclose($file);
            }
           
            $response->setContent($file);
            
           return $response;
        }else{
           
            throw new FileNotFoundException;
        }
        
    }

    
    /**
     * Listado de Documentos Generales
     *
     * Para listado no jerarquizado de un directorio de documentos sin permisos asociados.
     *
     * @return Response
     * @Route("/admin/documents", name="admin_documents_list")
     */
    public function adminDocumentsListAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
        $query = "general"; // Parámetro de configuración
        $ftp_server = $this->container->getParameter('ftp_server');
        $ftp_user_name = $this->container->getParameter('ftp_user_name');
        $ftp_user_pass = $this->container->getParameter('ftp_user_pass');
        
        $conn_id = ftp_connect($ftp_server);

        # Inciamos Sesión
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

        # Verificamos la Conexión
        if ((!$conn_id) || (!$login_result)) {  
            /*echo "\n ¡La conexión FTP ha fallado!";
            echo "\n Se intentó conectar al $ftp_server por el usuario $ftp_user_name"; 
            echo " \n";*/
            exit(); 

        }/* else {
            echo "\n Conexión a $ftp_server realizada con éxito, por el usuario " . $ftp_user_name . " \n";
        }*/
        return $this->render(
            "admin/general_documents.html.twig",
            array(
                'filelist' => $this->get('app.ftp.service')->retrieveGeneralDocuments($conn_id, $query),
            )
        );
    }

    /**
     * Controller para descargar documentos de la comisión de certificación.
     *
     * Dado un NOP y un tipo (query['certificados | cartas])
     * permite la descarga del primero de los documentos del listado obtenido
     * por el servicio FTP de filtrado de documentos del tipo indicado.
     * En caso de no haber documentos disponibles muestra un mensaje.
     *
     * @param Request $request
     * @return Response
     * @Route("/private/lastcertificate", name="private_certificate_downloadlast")
     */
    public function downloadLastCertificate(Request $request)
    {
        $user = $this->getUser();
        $opId = $request->get('opId');
        $query = $request->get('query');

        $em = $this->getDoctrine()->getManager();
        $op = $em->getRepository(Operator::class)->find($opId);

        /* Valida que el usuario tiene acceso */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if (!$user->getOperators()->contains($op)) {
                throw $this->createAccessDeniedException();
            }
        }

        $nop = $op->getOpNop(); // Añadido - para limitar las búsquedas.

        $fileList = $this->get('app.ftp.service')->retrieveDocListFromFtpServer($nop, $query);

        $type = '';
        if ($query == 'certificados') {
            $type = 'Certificados';

        } elseif ($query == 'cartas') {
            $type = 'Última Comunicación Comisión';
        }

     
        if (count($fileList) > 0) {
            if($query == 'cartas'){
                $fileList = array_reverse($fileList);

            }
            $path = reset($fileList);
            
            if($query == 'cartas'){
                $file = $this->extraerVisitas([$path],$nop);
                
                if($file and count($file)>0 ){
                    $file = $file[0];
                    if($file instanceof \App\Entity\DocumentosFTP){
                        $file->setVisitas($file->getVisitas() + 1);
                        $em->persist($file);
                        $em->flush();
                    }
                    
                }
            }
            $ftp_server = $this->container->getParameter('ftp_server');
        $ftp_user_name = $this->container->getParameter('ftp_user_name');
        $ftp_user_pass = $this->container->getParameter('ftp_user_pass');
        
        $conn_id = ftp_connect($ftp_server);

        # Inciamos Sesión
        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass); 

        # Verificamos la Conexión
        if ((!$conn_id) || (!$login_result)) {  
            /*echo "\n ¡La conexión FTP ha fallado!";
            echo "\n Se intentó conectar al $ftp_server por el usuario $ftp_user_name"; 
            echo " \n";*/
            exit(); 

        } /*else {
            echo "\n Conexión a $ftp_server realizada con éxito, por el usuario " . $ftp_user_name . " \n";
        }*/

        return $this->downloadFileAction($conn_id,$path);

     
        
        } else {

            $visitas = $this->extraerVisitas($fileList,$nop);
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                return $this->render(
                    'admin/useroperator_expediente_certificadosoanalisis.html.twig',
                    array(
                        'filelist' => $fileList, 'type' => $type, 'visitas' => $visitas
                    )
                );
            } else {
                return $this->render(
                    'private/useroperator_expediente_certificadosoanalisis.html.twig',
                    array(
                        'filelist' => $fileList, 'type' => $type, 'visitas' => $visitas
                    )
                );
            }
        }
    }

    /**
     * Controller que muestra un listado de Facturas de Operador provenientes de servidor FTP
     *
     * Dado un NOP el servicio FTP filtra y obtiene un listado de las facturas del año en vigor y el anterior.
     *
     * @param Request $request
     * @return Response
     * @Route("/private/facturacion/listado", name="private_billing_doclist")
     */
    public function operatorBillingList(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
       
        $opId = $request->get('opId');
        $query = $request->get('query');
        
        if(null===$opId){
            $sessionBag =$this->get('session')->getFlashBag(); 
            $opId= $sessionBag->get('opId')[0];
            session_unset();
            // $query = $sessionBag->get('query');
        }
       
        $em = $this->getDoctrine()->getManager();
        /** @var Operator $op */
        $op = $em->getRepository(Operator::class)->find($opId);
        $nop = $op->getOpNop();
        if(strpos($nop,'/') && $query == "facturas"){
            $opnew= $em->getRepository(OpNopTransform::class)->findBy(array('opNop' => $nop));
            $fileList = $this->get('app.ftp.service')->retrieveDocListFromFtpServer($opnew[0]->getopNopTransform(), $query);
        } else {
            $fileList = $this->get('app.ftp.service')->retrieveDocListFromFtpServer($nop, $query);
        }

        #dump($fileList);

        $query = ucwords($query); # Poner la Primera Letra de una Palabra en Mayúsculas

        #dump($opId);
        #dump($nop);
        #dump($query);
        #dump($fileList);
        $visitas = $this->extraerVisitas($fileList,$nop);

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->render(
                'admin/useroperator_expediente_certificadosoanalisis.html.twig',
                array('filelist' => $fileList, 'type' => $query, 'visitas' => $visitas, 'opNop' => $nop)
            );
        } else {
            return $this->render(
                'private/useroperator_expediente_certificadosoanalisis.html.twig',
                array('filelist' => $fileList, 'type' => $query, 'visitas' => $visitas)
            );
        }
    }

    /**
     * MNN Controller que muestra un listado de conclusiones de auditoria provenientes de servidor FTP
     *
     * Dado un NOP el servicio FTP filtra y obtiene un listado de las facturas del año en vigor y el anterior.
     *
     * @param Request $request
     * @return Response
     * @Route("/private/conclusiones/listado", name="private_billing_doclistAudi")
     */
    public function operatorBillingListConclu(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }

        $opId = $request->get('opId');
        $query = $request->get('query');
       
      
        $em = $this->getDoctrine()->getManager();
        /** @var Operator $op */
        $op = $em->getRepository(Operator::class)->find($opId);
        $nop = $op->getOpNop();

        $fileList = $this->get('app.ftp.service')->retrieveDocListFromFtpServer($nop, $query);

        #dump($fileList);

        $query = ucwords($query); # Poner la Primera Letra de una Palabra en Mayúsculas


        #dump($nop);
        #dump($query);
        #dump($fileList);
        
        $visitas = $this->extraerVisitas($fileList, $nop);
      
        
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->render(
                'admin/useroperator_expediente_certificadosoanalisis.html.twig',
                array('filelist' => $fileList, 'type' => $query, 'visitas' => $visitas,'opNop' => $nop)
            );
        } else {
            return $this->render(
                'private/useroperator_expediente_certificadosoanalisis.html.twig',
                array('filelist' => $fileList, 'type' => $query, 'visitas' => $visitas)
            );
        }
    }

    /**
     * Función que extrae visitas a traves del nombre de los documentos, nos aseguramos que el valor de la clave visitas siempre es rellenado
     */
    public function extraerVisitas($fileList, $opNop){
        $visitas = [];
        $em = $this->getDoctrine()->getManager();
        foreach ($fileList as &$value) {
            
            $result = $em->getRepository(DocumentosFTP::class)->findDocumentvisualizationByNbDoc($value,$opNop);
            if($result!==null){
                $visitas[] = $result;
            }else{
                $visitas[] = ['visitas' => 0];
            }
              
        }

       
        return $visitas;
    }

    public function extraerVisitasNum($fileList, $opNop){
        $visitas = [];
 
        $em = $this->getDoctrine()->getManager();
        foreach ($fileList as &$value) {
           
            $result = $em->getRepository(DocumentosFTP::class)->findDocumentvisualizationByNbDoc($value,$opNop);
            
            if($result!==null){
                $visitas[] = $result;
            }else{
                $visitas[] = ['visitas' => 0];
            }
              
        }
        
        if($visitas and count($visitas)>0){
            $visitas = $visitas[0];
            
            if($visitas instanceof \App\Entity\DocumentosFTP)
            {
                $visitas = $visitas->getVisitas();
                
            }else{
                $visitas = $visitas['visitas'];
            }
        }else{
            $visitas = $visitas['visitas'];
        }
       
        return $visitas;
    }

    /**
     * Controller para descarga de fichero desde FTP para usuario Administrador.
     *
     * Dado un path absoluto.
     *
     * @param Request $request
     * @throws \Touki\FTP\Exception\DirectoryException
     * @Route("/admin/download/fileandvisit", name="admin_file_download_visit")
     */
    public function downoloadandvisit(Request $request){       

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        $path = $request->request->get('path');
        $nop = $request->request->get('opNop');
    
        $visitas = $this->extraerVisitasNum([$path],$nop);
        
       
  
        //$visitas = ['visitas' => $visitas];
        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-Type', 'application/json');
         $response->sendHeaders();
         $response->setContent($visitas);
       
        
          return $response;
    }
    public function setextraerVisitasNum($fileList,$opNop){

        $visitas = [];
     
            $em = $this->getDoctrine()->getManager();
            
            foreach ($fileList as &$value) {
               
                $result = $em->getRepository(DocumentosFTP::class)->findDocumentvisualizationByNbDoc($value,$opNop);
                
                if($result!==null){
                    

                    
                        if($result instanceof \App\Entity\DocumentosFTP){
                            $result->setVisitas($result->getVisitas() + 1);
                            $em->persist($result);
                            $em->flush();
                        }
                        
                    
                    $visitas[] = $result;
                }else{
                    $visitas[] = ['visitas' => 0];
                }
                  
            }
           
            if($visitas and count($visitas)>0){
                $visitas = $visitas[0];
                
                if($visitas instanceof \App\Entity\DocumentosFTP)
                {
                    $visitas = $visitas->getVisitas();
                    
                }else{
                    $visitas = $visitas['visitas'];
                }
            }else{
                $visitas = $visitas['visitas'];
            }
           
            return $visitas;
        
    }
}

