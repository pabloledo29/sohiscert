<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Operator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

        //llamada a función que devuelve array con ficheros y rutas.
        $operatorList = $this->get('app.ftp.service')->retrieveFilesFromClients($clients, $query);

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
    public function userOperatorFileDownloadAction(Request $request)
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
        $clientId = explode('/', $path)[3];
        // Si solo hubiese un cliente para un usuario esta sería la manera más logica
//        if ($clientId !== $user->getClientId()->getCodigo()) {
//            throw $this->createAccessDeniedException();
//        }

        $cif = $user->getUsername();
        $clients = $this->getDoctrine()->getManager()->getRepository(Client::class)->findClients($cif);
        $list = [];
        foreach ($clients as $cod) {
            array_push($list, $cod['codigo']);
        }
        if (!in_array($clientId, $list)) {
            throw $this->createAccessDeniedException();
        }

        $this->downloadFileAction($path);
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

        return $this->render(
            "private/useroperator_general_documents.html.twig",
            array(
                'filelist' => $this->get('app.ftp.service')->retrieveGeneralDocuments($query),
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

        /* Validación del path para descarga */
        if ($this->get('app.ftp.service')->validPath($path, 'general')) {
            $this->downloadFileAction($path);
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

        //llamada a función que devuelve array con ficheros y rutas.
        $operatorList = $this->get('app.ftp.service')->retrieveFilesFromClients($clients, $query);

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
    public function adminDownloadFileAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $path = $request->request->get('path');

        if (is_null($path)) {
            throw $this->createAccessDeniedException();
        }

        $this->downloadFileAction($path);
    }

    /**
     * Controller para descarga de fichero desde FTP para un UserOperator.
     *
     * Dado un path absoluto.
     *
     * @param Request $request
     * @throws \Touki\FTP\Exception\DirectoryException
     * @Route("/private/download/file", name="useroperator_file_download")
     */
    public function userOperatorDownloadFileAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }

        $path = $request->request->get('path');

        if (is_null($path)) {
            throw $this->createAccessDeniedException();
        }

        $this->downloadFileAction($path);
    }

    /**
     * Método que descarga un fichero desde un servidor FTP.
     *
     * Recibido un path absoluto y validados los permisos previamente.
     *
     * @param $path
     * @return Response
     * Descarga de documentos del servidor FTP.
     */
    private function downloadFileAction($path)
    {
        $ftpWrapper = $this->get('ftp.wrapper');
        $ftpWrapper->pasv(true);

        $filename = substr(strrchr($path, '/'), 1);
        dump($filename);

        $path_file = $this->getParameter('repo_dir') . // Path server
            'public/docs/temp/' . date("d_m_Y_h_i_s") . $filename;

        $doc = $ftpWrapper->get($path_file, $path);
        if (!$doc) {
            throw new FileNotFoundException;
        }

        /*   Pruebas descarga  */
        basename(__FILE__, '.php');
        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path_file));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($path_file) . '";');
        $response->headers->set('Content-length', filesize($path_file));
        $response->sendHeaders();
        $response->setContent(readfile($path_file));

        return $response;
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

        return $this->render(
            "admin/general_documents.html.twig",
            array(
                'filelist' => $this->get('app.ftp.service')->retrieveGeneralDocuments($query),
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

            return $this->downloadFileAction($path);
        } else {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                return $this->render(
                    'admin/useroperator_expediente_certificadosoanalisis.html.twig',
                    array(
                        'filelist' => $fileList, 'type' => $type
                    )
                );
            } else {
                return $this->render(
                    'private/useroperator_expediente_certificadosoanalisis.html.twig',
                    array(
                        'filelist' => $fileList, 'type' => $type
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
       
        $em = $this->getDoctrine()->getManager();
        /** @var Operator $op */
        $op = $em->getRepository(Operator::class)->find($opId);
        $nop = $op->getOpNop();

        $fileList = $this->get('app.ftp.service')->retrieveDocListFromFtpServer($nop, $query);

        #dump($fileList);

        $query = ucwords($query); # Poner la Primera Letra de una Palabra en Mayúsculas

        #dump($opId);
        #dump($nop);
        #dump($query);
        #dump($fileList);

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->render(
                'admin/useroperator_expediente_certificadosoanalisis.html.twig',
                array('filelist' => $fileList, 'type' => $query)
            );
        } else {
            return $this->render(
                'private/useroperator_expediente_certificadosoanalisis.html.twig',
                array('filelist' => $fileList, 'type' => $query)
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

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->render(
                'admin/useroperator_expediente_certificadosoanalisis.html.twig',
                array('filelist' => $fileList, 'type' => $query)
            );
        } else {
            return $this->render(
                'private/useroperator_expediente_certificadosoanalisis.html.twig',
                array('filelist' => $fileList, 'type' => $query)
            );
        }
    }
}
