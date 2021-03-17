<?php
/**
 * Copyright (c) 2016. 
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserAdmin;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\File as FileValidator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\QueryBuilder;
use DataDog\PagerBundle\Pagination;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
/* Para uso de Amazon S3*/
use Aws\S3\Exception\S3Exception;
use Aws\Result;


use App\Entity\DocAnaDoc;
use App\Entity\DocAnaList;
use App\Entity\DocExp;
use App\Entity\DocumentosFTP;
use App\Entity\Operator;
use App\Entity\UserOperator;
use App\Entity\UploadedFileRegistry;
use App\Entity\ComunicationSendRegistry;
use App\Mailchimp\Provider\DoctrineListProvider;
use App\Repository\ComunicationSendRegistryRepository;
use App\Twilio\TwilioController;
use Doctrine\Common\Collections\Expr\Value;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class DocExpController
 *
 * Proporciona controllers y métodos para el listado, subuda y descarga de documentación asociada a
 * expediente de operador.
 *
 * @package App\Controller
 */
class DocExpController extends AbstractController
{
    /**
     * Función para filtrar por doc-exp de gsBase en función de códigos de plantillas previamente indicados.
     *
     * @param string $nop El número de operador de un determinado Operador.
     * @param string $query Tipo de documento a listar.
     * @return array|Response
     */
    private function listDocExpAction($nop, $query)
    {
        $filter = $this->getParameter($query);
        $gsbase = $this->get('gsbase');
        if ($gsbase->getGsbase() == null) {
            print_r("Error estableciendo conexión con gsbase");

            return $this->render('default/index.html.twig');
        }
        $gsbasexml = $this->get('gsbasexml');

        $xmlDocExp = $gsbasexml->getXmlRetrieveExpDocList($nop);
        $xmlRes = $gsbase->gsbase_exec('consulta_xml', $xmlDocExp, 'consulta-xml');

        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $docExp = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroDocExp', 'xml');

        $docs = [];

        /** @var DocExp $registro */
        foreach ($docExp->Registro as $registro) {
            // validar cuales incluir
            if (in_array($registro->getDePla(), $filter)) {
                array_push($docs, $registro);
            }
        }

        return $docs;
    }

    /**
     * Controller que devuelve el listado de documentos que cumplen con el criterio indicado.
     *
     * Ateniéndose al parámetro id de Operador y query que indica el tipo de documento que se solicita a gsBase
     * Dicho Operador debe estar asociado al UserOperator logueado.
     *
     * @param Request $request
     * @return Response
     * @Route("/private/expediente/documentacion", name="useroperator_expediente_documentacion")
     */
    public function userOperatorListDocExpAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $opId = $request->request->get('opId');
        $query = $request->request->get('query');

        $operator = $this->getDoctrine()->getManager()->getRepository(Operator::class)->find($opId);

        if (!$user->getOperators()->contains($operator)) {
            throw $this->createAccessDeniedException();
        }

        return $this->render(
            'private/useroperator_expediente_docslist.html.twig',
            array('docs' => $this->listDocExpAction($operator->getOpNop(), $query), 'operator' => $operator)
        );
    }

    /**
     * Controller que devuelve el listado de documentos que cumplen con el criterio indicado.
     *
     * Ateniéndose al parámetro id de Operador y query que indica el tipo de documento que se solicita a gsBase.
     *
     * @param Request $request
     * @return Response
     * @Route("/admin/expediente/documentacion", name="admin_expediente_documentacion")
     */
    public function adminListDocExpAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        $opId = $request->get('opId');
        $query = $request->get('query');

        $operator = $this->getDoctrine()->getManager()->getRepository(Operator::class)->find($opId);

        return $this->render(
            'admin/useroperator_expediente_docslist.html.twig',
            array('docs' => $this->listDocExpAction($operator->getOpNop(), $query), 'operator' => $operator)
        );
    }

    /**
     * Controller que permite la descarga de documentos RTF desde la tabla de expedientes de gsBase.
     *
     * @param Request $request
     * @return Response
     * @Route("/private/expediente/doc/show", name="useroperator_docexp_show")
     */
    public function showDocExpAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
        $id = $request->request->get('id');
        $opId = $request->request->get('opId');

        $operator = $this->getDoctrine()->getManager()->getRepository(Operator::class)->find($opId);

        if (!$this->validateAccess($operator)) {
            throw $this->createAccessDeniedException();
        }

        $gsbase = $this->get('gsbase');
        if ($gsbase->getGsbase() == null) {
            print_r("Error estableciendo conexión con gsbase");

            return $this->render('default/index.html.twig');
        }
        $gsbasexml = $this->get('gsbasexml');

        $xmlDocExp = $gsbasexml->getXmlRetrieveExpDocFile($id);
        $xmlRes = $gsbase->gsbase_exec('consulta_xml', $xmlDocExp, 'consulta-xml');

        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );
        $docExp = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroDocExp', 'xml');

        $rtfBruto = $docExp->Registro[0]->getDeTxt();
        $regDeno = $docExp->Registro[0]->getDeDeno();

        basename(__FILE__, '.php');
        $path_file = __DIR__ . '/../../../app/logs/update/' . $regDeno . '-' . date("d_m_Y_h_i_s") . '.rtf';
        $file = fopen($path_file, "w+");
        fwrite($file, $rtfBruto);
        fclose($file);

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
     * Valida que el usuario en cuestión tenga acceso al documento solicitado.
     *
     * @param Operator $operator Operador sobre el que desea obtener documentación.
     * @return bool
     */
    private function validateAccess(Operator $operator)
    {
        $valid = false;
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $valid = true;
        } else {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')
                and $this->getUser()->getOperators()->contains($operator)
            ) {
                $valid = true;
            }
        }

        return $valid;
    }

    /**
     * Controller que genera una instancia de formulario para subir documentos a un bucket de S3 de Amazon.
     *
     * Permite la subida de documentación al sistema de gestión.
     * Para ello y en base a los parámetros definidos en el array parametrizado docexptypes de sohiscert.yml.
     * Mediante un FileValidator admite diversos formatos de documentos para ser subidos.
     *
     * @param Request $request
     * @return Response
     * @Route("/private/expediente/uploads3", name="useroperator_expediente_upload_s3")
     */
    public function uploadFileToS3Action(Request $request)
    {
        $user = $this->getUser();
        $operators = $user->getOperators();

        $choices = [];
        /** @var Operator $op */
        foreach ($operators as $op) {
            $choices[$op->getId()] = $op->getOpNop();
        }

        $defaultData = array('message' => 'Type your message here');
        /* Includes FileValidator constraints to allow several type of files for uploadinf */
        $form = $this->createFormBuilder($defaultData)
            ->add(
                'operator',
                ChoiceType::class,
                array(
                    'choices' => $choices,
                )
            )
            ->add(
                'type',
                ChoiceType::class,
                array(
                    'choices' => $this->container->getParameter('docexptypes'),
                )
            )
            ->add(
                'document',
                'file',
                array(
                    'constraints' => new FileValidator(
                        array(
                            'maxSize' => '10Mi',
                            'mimeTypes' => array(
                                "application/pdf",
                                "application/doc",
                                "application/rtf",
                                "text/rtf",
                                "application/msword",
                                "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                                "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
                                "application/xml",
                                "image/png",
                                "image/jpg",
                                "image/jpeg",
                            ),
                            'mimeTypesMessage' => "Por favor, suba un documento válido de tipo PDF, DOC o RTF",
                            'maxSizeMessage' => "Por favor, suba un documento de un máximo de 5mb",
                            'disallowEmptyMessage' => "El documento subido está vacio",
                        )
                    ),
                )
            )
            ->add(
                'description',
                'textarea',
                array(
                    'attr' => array('maxlength' => '500'),
                )
            )
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form['document']->getData() !== null) {

            $data = $form->getData();
            $data['opNop'] = $choices[$data['operator']];

            $file = $this->uploadFileToS3($data);

            if ($file !== null) {
                ## Subida a S3
                // Log con datos de documento subido
                $this->addUploadedFileToRegistryS3($user, $data, $file, $data['document']);

                $this->addFlash('msg', 'Su documento ha sido subido con éxito.');

                return $this->redirectToRoute('private_home');
            }

            return $this->render('default/index.html.twig');
        } else {
            if ($form->isSubmitted()) {
                $form->get('document')->addError(new FormError('Debe adjuntar un fichero.'));
            }
        }

        return $this->render('private/Form/upload_docexp_form.html.twig', array('form' => $form->createView()));
    }



   /**
     * Controller que genera un formulario de subida de documentación al sistema con traslado a FTP server.
     *
     * Permite la subida de documentación al sistema de gestión.
     * Para ello y en base a los parámetros definidos en el array parametrizado docexptypes de sohiscert.yml.
     * Mediante un FileValidator admite diversos formatos de documentos para ser subidos.
     *
     * maxSize fijado a 10Mi
     * description fijado a máximo 500 caracteres
     *
     * @param Request $request
     * @return Response
     * @Route("/private/expediente/uploaddoc", name="useroperator_expediente_upload")
     */
    public function uploadFileAction(Request $request)
    {
        $user = $this->getUser();
        $operators = $user->getOperators();

        $choices = [];
        /** @var Operator $op */
        foreach ($operators as $op) {
            $choices[$op->getId()] = $op->getOpNop();
        }

        $defaultData = array('message' => 'Type your message here');
        /* Includes FileValidator constraints to allow several type of files for uploadinf */
        $form = $this->createFormBuilder($defaultData)
            ->add(
                'operator',
                ChoiceType::class,
                array(
                    'choices' => array_flip($choices),
                )
            )
            ->add(
                'type',
                ChoiceType::class,
                array(
                    'choices' => $this->container->getParameter('docexptypes'),
                )
            )
            ->add(
                'document',
                FileType::class,
                array(
                    'constraints' => new FileValidator(
                        array(
                            'maxSize' => '10Mi',
                            'mimeTypes' => array(
                                "application/pdf",
                                "application/doc",
                                "application/rtf",
                                "text/rtf",
                                "application/msword",
                                "application/vnd.ms-excel",
                                "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                                "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
                                "application/xml",
                                "image/png",
                                "image/jpg",
                                "image/jpeg",
                            ),
                            'mimeTypesMessage' => "Por favor, suba un documento válido de tipo PDF, DOC o RTF",
                            'maxSizeMessage' => "Por favor, suba un documento de un máximo de 5mb",
                            'disallowEmptyMessage' => "El documento subido está vacio",
                        )
                    ),
                )
            )
            ->add(
                'description',
                TextareaType::class,
                array(
                    'attr' => array('maxlength' => '500'),
                )
            )
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form['document']->getData() !== null) {

            $data = $form->getData();
            $data['opNop'] = $choices[$data['operator']];
                
            //$data['opNop'] = $choices [(int)array_keys($choices,$data['operator'])[0]];
            // Subida a FTP
            $file = $this->uploadFile($data);

            if ($file !== null) {

                $uploadedFile = $this->addUploadedFileToRegistry($user, $data, $file, $data['document']);

                $mailer = $this->container->get('app.mailer.service');
                $mailer->sendUploadedFileEmail($data, $uploadedFile);
                $this->addFlash('msg', 'Su documento ha sido subido con éxito.');

                return $this->redirectToRoute('private_home');
            }

            return $this->render('default/index.html.twig');
        } else {
            if ($form->isSubmitted()) {
                $form->get('document')->addError(new FormError('Debe adjuntar un fichero.'));
            }
        }

        return $this->render('private/Form/upload_docexp_form.html.twig', array('form' => $form->createView()));
    }


    /**
     * Controller que genera un formulario de subida de documentación al sistema con traslado a FTP server.
     *
     * Permite la subida de documentación al sistema de gestión.
     * Para ello y en base a los parámetros definidos en el array parametrizado docexptypes de sohiscert.yml.
     * Mediante un FileValidator admite diversos formatos de documentos para ser subidos.
     *
     * maxSize fijado a 10Mi
     * description fijado a máximo 500 caracteres
     *
     * @param Request $request
     * @return Response
     * @Route("/private/expediente/comunicaciones", name="useroperator_expediente_comunicaciones")
     */
    public function cominnicationPanel(Request $request, DoctrineListProvider $mailchimplist)
    {
        $user = $this->getUser();
        $operators = $user->getOperators();

        $choices = [];
        $choices_group = [];
        $opNops = [];
        /** @var Operator $op */
        foreach ($operators as $op) {
            $choices[$op->getId()] = $op->getOpNop();
            $choices_group[ $op->getOpReg()] =$op->getId();
            $opNops[] = $op->getOpNop();
        }
     
        $defaultData = array('message' => 'Type your message here');
        /* Includes FileValidator constraints to allow several type of files for uploadinf */
        $form = $this->createFormBuilder($defaultData)
            ->add(
                'operator',
                ChoiceType::class,
                array(
                    'choices' => $choices,
                )
            )
            ->add(
                'asunto',
                TextType::class
            )
            ->add(
                'type',
                ChoiceType::class,
                array(
                    'choices' => $this->container->getParameter('docexptypes'),
                )
            )
            ->add(
                'description',
                TextareaType::class,
                array(
                    'attr' => array('maxlength' => '160'),
                )
            );
            $form_grupo = $this->createFormBuilder($defaultData)
            -> add('OpReg', ChoiceType::class,
                array(
                    'choices' => $choices_group,
                )
            )
            ->add(
                'asunto',
                TextType::class
            )
            ->add(
                'type',
                ChoiceType::class,
                array(
                    'choices' => $this->container->getParameter('docexptypes'),
                )
            )
            ->add(
                'description',
                TextareaType::class,
                array(
                    'attr' => array('maxlength' => '500'),
                )
            );
            

        
            $form_grupo = clone $form;
            $form_grupo->remove('operator');
            $form_grupo-> add('OpReg', ChoiceType::class,
                array(
                    'choices' => $choices_group,
                )
            )->add('save_grupo', SubmitType::class, ['label' => 'Enviar por grupo']);
    
            $form_todos = clone $form;
            $form_todos->remove('operator')
            ->add('save_todos', SubmitType::class, ['label' => 'Enviar a todos']);
            
            $form->add('save', SubmitType::class, ['label' => 'Enviar por expediente']);

            $form = $form->getForm();
            $form_grupo = $form_grupo->getForm();
            $form_todos = $form_todos->getForm();


            
           
            $form->handleRequest($request);
            $form_grupo->handleRequest($request);
            $form_todos->handleRequest($request);
            
        
        /*if(){
           
            $form->handleRequest($request);
        }else if($form_grupo->get('save_grupo')->isClicked()){
          
            $form_grupo->handleRequest($request);
            
        }else if($form_todos->get('save_todos')->isClicked()){
           
            $form_todos->handleRequest($request);
        }*/
        $listaEnviado = $this->getDoctrine()->getManager()->getRepository(ComunicationSendRegistry::class)->findVisitasByUserOperator($user);
        $visitas=0;
        if($listaEnviado){
            foreach($listaEnviado as $enviado){
                
                $visitas += $enviado['visitas'];
            }
       }
        
        if($visitas===null)
        {
            $visitas=0;
        }
        

        if ($form->isSubmitted() && $form->isValid() && $form->get('save')->isClicked() /*&& $form['document']->getData() !== null*/) {
            
            $data = $form->getData();
         
         
            $data['opNop'] = $choices [(int)array_keys($choices,$data['operator'])[0]];
            
            $data['to'] = $this->getDoctrine()->getManager()->getRepository(Operator::class)->getOperatorEmail($data['opNop'])[0]['opEma'];      
            
            $data['visitas'] = $visitas;
               $comunicationSendRegistry  = $this->addEmailComunicationRegistry($user, $data, "expediente");

                $mailer = $this->container->get('app.mailer.service');
                $mailer->sendExpedienteEmail($data, $comunicationSendRegistry);

                //$sms_whatsapp_notification = new TwilioController($this->container);

                //$sms_whatsapp_notification->privateTwilioSmssend($data);
                //$sms_whatsapp_notification->privateTwilioWhatsappsSend($data);
            
                $this->addFlash('msg', 'Su documento ha sido subido con éxito.');

                return $this->redirectToRoute('private_home');
        

            return $this->render('default/index.html.twig');
        } else if($form_grupo->isSubmitted() && $form_grupo->isValid() && $form_grupo->get('save_grupo')->isClicked() /*&& $form_grupo['document']->getData() !== null*/) {
            var_dump("AAAAA");
            $data = $form_grupo->getData();
            $listaOperadores =[];
            //var_dump($mailchimplist->getLists());
            exit;
            foreach ($listaOperadores as $data) {
                //TODO:  $data['opNop'] = $choices [(int)array_keys($choices,$data['operator'])[0]];
                continue;
          
                
                //TODO: SACAR USUARIO
                // Subida a FTP
                $file = $this->uploadFile($data);

                if ($file !== null) {

                    //TODO:$uploadedFile = $this->addUploadedFileToRegistry($user, $data, $file, $data['document']);

                //TODO: enviar correo a traves de mailchimp
                $this->addFlash('msg', 'Su documento ha sido subido con éxito.');

                return $this->redirectToRoute('private_home');
            }
        }
            return $this->render('default/index.html.twig');
        } else if($form_todos->isSubmitted() && $form_todos->isValid() && $form_todos->get('save_todos')->isClicked() /*&& $form_todos['document']->getData() !== null*/) {
           
            $data = $form_todos->getData();
        
            //$data['opNop'] = $choices [(int)array_keys($choices,$data['operator'])[0]];
           
            //TODO: SACAR USUARIO
            // Subida a FTP
            //$file = $this->uploadFile($data);

            //    if ($file !== null) {

                //TODO:$uploadedFile = $this->addUploadedFileToRegistry($user, $data, $file, $data['document']);
                    $group_option='all';
                    
                    $response = $this->forward('App\Controller\MailchimpController::privateEnviarMensajesDeCampaña', [
                        'data'  => $data,
                        'group_option' => $group_option,
                    ]);
            
                
                    $this->addFlash('msg', 'Su documento ha sido subido con éxito.');
                
                    return $this->redirectToRoute('private_home');
           //     }
            
        }/*else{

            if ($form->isSubmitted() && $form->get('save')->isClicked()) {
                $form->get('document')->addError(new FormError('Debe adjuntar un fichero.'));
            }else if($form_grupo->isSubmitted() && $form_grupo->get('save_grupo')->isClicked()){
                $form_grupo->get('document')->addError(new FormError('Debe adjuntar un fichero.'));
            } else if($form_todos->isSubmitted()&& $form_todos->get('save_todos')->isClicked()){
                $form_todos->get('document')->addError(new FormError('Debe adjuntar un fichero.'));
            }
               
        }*/
        
        return $this->render('private/Form/comunicationforms.html.twig', [
            'form' => $form->createView(),
            'form_grupo' => $form_grupo->createView(),
            'form_todos' => $form_todos->createView(),
            'visitas' => $visitas
        ]);
    }

    /**
     * Función de subida de un documento a un bucket de S3
     *
     * @param array $data Contenido de un formulario de subida con documento asocaido.
     * @return Result|null
     */
    private function uploadFileToS3($data)
    {
        $file = new File($data['document']);

        /* Servicio AWS S3 */
        $s3 = $this->container->get('aws.s3');

        /* Subida de un fichero a S3 */
        $filename = sha1(uniqid(mt_rand(), true));
        $filename = $data['operator'] . '-' . $data['type'] . '-' . $filename . '.' . $file->guessExtension();
        try {
            $result = $s3->putObject(
                [
                    'Bucket' => 'atlsoft',
                    'Key' => 'uploads/' . $filename,
                    'Body' => fopen($file->getRealPath(), 'r'),
                ]
            );

            return $result;
        } catch (S3Exception $e) {
            echo $e->getMessage() . "\n";
            return null;
        }
    }

    /**
     * Función para colocar el documento subido en un servidor FTP
     *
     * @param array $data Contenido de un formulario de subida con documento asocaido.
     * @return null
     */
    private function uploadFile($data)
    {
        $file = new File($data['document']);
        $filename = sha1(uniqid(mt_rand(), true));
        $filename = '/subidas/' .
            $data['operator'] . '-' . $data['type'] . '-' . $filename . '.' . $file->guessExtension();

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
        
        /* Passive mode true for FTP server under IIS */
        ftp_pasv($conn_id,true);

        try {
            ftp_fput($conn_id,$filename, fopen($file->getRealPath(), 'r'),FTP_BINARY);
            return array($file, $filename);
        } catch (FileException $e) {
            return null;
        }
    }

    /**
     * Añade al registro datos relacionados con el documento subido a un servidor FTP.
     *
     * Genera un registro en la base de datos con la información vinculada a la subida de documento realizada.
     *ChoiceType::class
     * @param UserOperator $user El UserOperator que realiza la gestión.
     * @param array $data El array de datos del formulario de subida.
     * @param array $file El array con el fichero subido al sistema y su nombre.
     * @param UploadedFile $document El objeto generado por el componente de subida de documento desde formulario.
     * @return UploadedFileRegistry
     */
    private function addUploadedFileToRegistry(UserOperator $user, array $data, array $file, UploadedFile $document)
    {
        $choices = $this->container->getParameter('docexptypes');
        $em = $this->getDoctrine()->getManager();
        $uploadedDocRegistry = new UploadedFileRegistry();
     
        $uploadedDocRegistry->setUserOperator($user);
        $uploadedDocRegistry->setOpNop($data['opNop']);
        $uploadedDocRegistry->setDescription($data['description']);
        $uploadedDocRegistry->setDocexptype($choices[array_keys($choices,$data['type'])[0]]);
        $uploadedDocRegistry->setFileName($file[1]);
        $uploadedDocRegistry->setFileOrigName($document->getClientOriginalName());
        $uploadedDocRegistry->setFilePath($file[1]);
        $em->persist($uploadedDocRegistry);
        $em->flush();

        return $uploadedDocRegistry;
    }



    /**
     * Añade al registro datos relacionados con el documento subido a un servidor FTP.
     *
     * Genera un registro en la base de datos con la información vinculada a la subida de documento realizada.
     *ChoiceType::class
     * @param UserOperator $user El UserOperator que realiza la gestión.
     * @param array $data El array de datos del formulario de subida.
     * @param array $file El array con el fichero subido al sistema y su nombre.
     * @param UploadedFile $document El objeto generado por el componente de subida de documento desde formulario.
     * @return UploadedFileRegistry
     */
    private function addEmailComunicationRegistry(UserOperator $user, array $data, string $choice)
    {
        $em = $this->getDoctrine()->getManager();
        $comunicationSendRegistry = new ComunicationSendRegistry();
     
        $comunicationSendRegistry->setUserOperator($user);
        $comunicationSendRegistry->setOpNop($data['opNop']);
        $comunicationSendRegistry->setSubject($data['asunto']);
        $comunicationSendRegistry->setCuerpo($data['description']);
        $comunicationSendRegistry->setDestino($data['to']);
        $comunicationSendRegistry->setSendtype($choice);
        $comunicationSendRegistry->setVisitas($data['visitas']+1);
        
        $em->persist($comunicationSendRegistry);
        $em->flush();

        return $comunicationSendRegistry;
    }

    /**
     * Función que añade al registro los documentos subidos al bucket de Amazon S3.
     *
     * Genera un registro en la base de datos con la información vinculada a la subida de documento realizada.
     *
     * @param UserOperator $user El UserOperator que realiza la gestión.
     * @param array $data El array de datos del formulario de subida.
     * @param Result $file El objeto Result de respuesta generado por la subida a un bucket de Amaszon S3
     * @param UploadedFile $document El objeto generado por el componente de subida de documento desde formulario.
     */
    private function addUploadedFileToRegistryS3(UserOperator $user, array $data, Result $file, UploadedFile $document)
    {
        $choices = $this->getParameter('docexptypes');
        $em = $this->getDoctrine()->getManager();
        $uploadedDocRegistry = new UploadedFileRegistry();

        $filepath = stristr($file->get('ObjectURL'), 'uploads');

        $uploadedDocRegistry->setUserOperator($user);
        $uploadedDocRegistry->setOpNop($data['opNop']);
        $uploadedDocRegistry->setDescription($data['description']);
        $uploadedDocRegistry->setDocexptype($choices[$data['type']]);
        $uploadedDocRegistry->setFileName($filepath);
        $uploadedDocRegistry->setFileOrigName($document->getClientOriginalName());
        $uploadedDocRegistry->setFilePath($filepath);
        $em->persist($uploadedDocRegistry);
        $em->flush();
    }

    /**
     * Controller que genera el listado de documetnos subidos por un UserOperator.
     *
     * @return Response
     * @Route("private/uploads/list", name="useroperator_uploadeddoc_list")
     */
    public function uploadedDocListAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $docs = $em->getRepository(UploadedFileRegistry::class)->findBy(
            array('userOperator' => $user),
            array('createdDate' => 'DESC')
        );

        return $this->render("private/useroperator_uploaded_docs_list.html.twig", array('filelist' => $docs));
    }

    /**
     * Controller que permite descargar un documento subido por un UserOperator al FTP,
     *
     * @param Request $request
     * @return Response
     * @Route("/private/uploadeddoc/show", name="useroperator_uploadeddoc_show")
     */
    public function downloadDocExpAction(Request $request)
    {
        $id = $request->get('id');
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $doc = $em->getRepository(UploadedFileRegistry::class)->find($id);

        if ($doc->getUserOperator() !== $user &&
            !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException();
        }

        $filename = substr(strrchr($doc->getFilePath(), '/'), 1);
        $ftp_server=$this->container->getParameter('ftp_server');
        $ftp_user_pass=$this->container->getParameter('ftp_user_pass');
        $ftp_user_name=$this->container->getParameter('ftp_user_name'); 
        $path_file =  $this->container->getParameter('repo_dir') . 'public/docs/temp/' . date("d_m_Y_h_i_s") . $filename;
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
        $file_open = fopen($path_file, "a+");
        fclose($file_open);

        ftp_pasv($conn_id,true);
        if (ftp_get($conn_id,$path_file, $doc->getFilePath(),FTP_BINARY)) {
    
            basename(__FILE__, '.php');
            $response = new Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path_file));
//        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($path_file) . '";');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $doc->getFileOrigName() . '";');
            $response->headers->set('Content-length', filesize($path_file));

            $response->sendHeaders();
            $file = fopen($path_file, 'rb');
            if ( $file !== false ) {
                fpassthru($file);
                fclose($file);
            }
            $response->setContent($file);
      
            return $response;
        } else {

            throw new AccessDeniedException();
        }
    }

    /**
     * Controller que descarga un documento subido por un UserOperator a un bucket de Amazon S3.
     *
     * @param Request $request
     * @return null
     * @Route("/private/uploadeddoc/shows3", name="useroperator_uploadeddoc_shows3")
     */
    public function downloadDocExpS3Action(Request $request)
    {
        $id = $request->get('id');
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $doc = $em->getRepository(UploadedFileRegistry::class)->find($id);

        if ($doc->getUserOperator() !== $user &&
            !$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException();
        }

        $s3 = $this->container->get('aws.s3');

        try {
            $object = $s3->getObject(
                [
                    'Bucket' => 'atlsoft',
                    'Key' => $doc->getFilePath(),
                ]
            );

        } catch (S3Exception $e) {
            echo $e->getMessage() . "\n";
            return null;
        }


        header('Content-Description: File Transfer');
        /* This assumes content type is set when uploading the file. */
        header('Content-Type: ' . $object->get('ContentType'));
        header('Content-Disposition: attachment; filename=' . $doc->getFileOrigName());
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        //send file to browser for download.
        echo $object->get('Body')->getContents();
    }

    /**
     * Listado de Documentos subidos por Username(CIF)
     *
     * Hace uso de paginado con componente DataDog Paginator para búsquedas y filtrados
     *
     * @param Request $request
     * @return Response
     * @Route("/admin/uploadeddocs/list", name="admin_uploadeddocs_list")
     */
    public function adminUploadedDocsListAction(Request $request)
    {
        $qb = $this->getDoctrine()->getManager()->getRepository(UploadedFileRegistry::class)
            ->createQueryBuilder('u')
            ->addSelect('uo')
            ->innerJoin('u.userOperator', 'uo');

        $options = [
            'sorters' => ['u.createdDate' => 'DESC'],
            'applyFilter' => [$this, 'uploadedDocFilters'], // custom filter handling
            'limit' => 25,
            'range' => 10,
            'maxPerPage' => 100,
        ];

        $docs = new Pagination($qb, $request, $options);

        return $this->render('admin/uploadeddoc_list.html.twig', array('fileList' => $docs));
    }

    /**
     * Filtro de búsqueda para DataDog Component.
     *
     * @param QueryBuilder $qb
     * @param $key
     * @param $val
     * @throws \Exception
     */
    public function uploadedDocFilters(QueryBuilder $qb, $key, $val)
    {
        switch ($key) {
            case 'usuario':
                if ($val) {
                    $qb->andWhere($qb->expr()->eq('uo.username', ':username'));
                    $qb->setParameter('username', "$val");
                }
                break;
            case 'expediente':
                if ($val) {
                    $qb->andWhere($qb->expr()->like('u.opNop', ':opNop'));
                    $qb->setParameter('opNop', "%$val%");
                }
                break;
            default:
                // if user attemps to filter by other fields, we restrict it
                throw new \Exception("filter not allowed");
        }
    }

    /**
     * Controller que genera un listado de documentos de análisis de un  Operador.
     *
     * @param Request $request
     * @return Response
     * @Route("/private/expediente/analisis", name="useroperator_expediente_analisis")
     */
    public function docAnaListAction(Request $request)
    {
        $opId = $request->get('opId');
        $user = $this->getUser();
        $query = $request->get('query');
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
        
        $operator = $this->getDoctrine()->getManager()->getRepository(Operator::class)->find($opId);

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if (!$user->getOperators()->contains($operator)) {
                throw $this->createAccessDeniedException();
            }
        }
      
        


        $nop = $operator->getOpNop(); // Añadido - para limitar las búsquedas
        #//////////////////////////////////
        /*$gsbase = $this->get('gsbase');
        if ($gsbase->getGsbase() == null) {
            print_r("Error estableciendo conexión con gsbase");

            return $this->render('default/index.html.twig');
        }
        $gsbasexml = $this->get('gsbasexml');
        $xmlDocAnaList = $gsbasexml->getXmlRetrieveAnaList($operator->getCodigo());

        $xmlRes = $gsbase->gsbase_exec('consulta_xml', $xmlDocAnaList, 'consulta-xml');
        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );

        $docAnaList = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroDocAnaList', 'xml');
        */
      
        $docAnaList = $this->get('app.ftp.service')->retrieveDocListFromFtpServer($nop, $query);

        
        #//////////////////////////////////
        
        /** @var ArrayCollection $analisis */
        $visitas = [];
        #$analisis = $docAnaList->Registro;
        if (count($docAnaList) > 0) {
           
            $visitas = $this->extraerVisitasANA($docAnaList,$nop);
           /* $iterator = $docAnaList->getIterator();

            $iterator->uasort(function ($a, $b) {*/
                /** @var DocAnaList $a */
                /** @var DocAnaList $b */
            /*    return ($a->getAnFec() < $b->getAnFec()) ? 1 : -1;
            });

            $listado = new ArrayCollection(iterator_to_array($iterator));*/
            
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                return $this->render(
                    'admin/useroperator_expediente_docslist.html.twig',
                    array('docs' => $docAnaList, 'operator' => $operator, 'visitas' => $visitas)
                );

            } else {
                return $this->render(
                    'private/useroperator_expediente_docslist.html.twig',
                    array('docs' => $docAnaList, 'operator' => $operator, 'visitas' => $visitas)
                );
            }
        } else {

            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                return $this->render(
                    'admin/useroperator_expediente_docslist.html.twig',
                    array('docs' => array(), 'operator' => $operator, 'visitas' => $visitas)
                );

            } else {
                return $this->render(
                    'private/useroperator_expediente_docslist.html.twig',
                    array('docs' => array(), 'operator' => $operator, 'visitas' => $visitas)
                );
            }
        }
    }

    /**
     * Controller para descargar documento de análisis desde gsBase.
     *
     * Dado un código de documento recogido del listado anterior convierte el string base64 proporcionado por
     * el módulo de consulta XML de gsBase a un documento PDF que ofrece para descargar al cliente final.
     *
     * @param Request $request
     * @return Response
     * @Route(path="/private/download/docana", name="operator_download_ana")
     */
    /*public function downloadAnaDoc(Request $request)
    {
        $gsbase = $this->get('gsbase');
        $gsbasexml = $this->get('gsbasexml');
        $codigo = $request->get('cod');
        $opId = $request->get('opId');

        $xmlDocAnaDoc = $gsbasexml->getXmlRetrieveDocAna($codigo);
        $xmlRes = $gsbase->gsbase_exec_no_close('consulta_xml', $xmlDocAnaDoc, 'consulta-xml');
        $gsbase->gsbase_stop();

        $newXml = preg_replace_callback(
            "#</?\w+#",
            function ($matches) {
                return strtolower($matches[0]);
            },
            $xmlRes
        );

        $docAnaDoc = $this->get('jms_serializer')->deserialize($newXml, 'App\Entity\RegistroDocAnaDoc', 'xml');

        if ($docAnaDoc->Registro->count() > 0) {*/
            /** @var DocAnaDoc $document */
            /*$document = $docAnaDoc->Registro->first();
            $res = base64_decode($document->getDanTxt());
            $path_file = $this->getParameter('repo_dir') .
                'web/docs/temp/' . $opId . '-' . $document->getCodigo();

            $file = fopen($path_file, "w+");
            fwrite($file, $res);
            fclose($file);

            $response = new Response();
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path_file));
            $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($path_file) . '";');
            $response->headers->set('Content-length', filesize($path_file));
            $response->sendHeaders();
            $response->setContent(readfile($path_file));

            return $response;

        } else {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                return $this->render(
                    'admin/useroperator_expediente_docslist.html.twig',
                    array('docs' => array(), 'operator' => null)
                );

            } else {
                return $this->render(
                    'private/useroperator_expediente_docslist.html.twig',
                    array('docs' => array(), 'operator' => null)
                );
            }
        }
    }*/

    /**
     * Controller para descarga de documentos generales por alcance de Operador.
     *
     * @return Response
     * @Route("/private/expediente/generaldoc", name="useroperator_expediente_generaldoc")
     */
    public function expedienteGeneralDocsAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
        /** @var User $user */
        $user = $this->getUser();
        
        if ($user->hasRole($user->getRoles()[0])) {
            /** @var UserAdmin $user */
            return $this->userAdminGeneralDocs();
        } else {
            
            /** @var UserOperator $user */
            return $this->useroperatorGeneralDocs($user);
        }
    }

    /**
     * Listado de documentos generales para administeador
     *
     * @return Response
     */
    private function userAdminGeneralDocs()
    {
        #$rootpath = $this->container->getParameter('web_root');
    
        $rootpath='https://intranet-sohiscert4.e4ff.pro-eu-west-1.openshiftapps.com/public/';
        $docPath = 'docs/';
        $path = $rootpath . $docPath;

        /*$docs['DIR-OP-18-3403-Circular informativa sanciones Autoridades Competentes'] = $path .
            'ECO/DIR-OP-18-3403-Circular informativa sanciones Autoridades Competentes.pdf';

        $docs['F229-02-Solicitud de cambio de titularidad producción ecológica'] = $path .
            'ECO/F229-02-Solicitud Cambio de Titularidad ECO.docx';

        $docs['F227-02-Solicitud de ampliación finca ecológica'] = $path .
            'ECO/FAE/F227-02-Cuestionario Ampliación Finca ECO.doc';
        $docs['F223-03-Solicitud de autorización de uso de semillas no ecológicas'] = $path .
            'ECO/FAE/F223-03-Solicitud Autorización Semilla no ECO.doc';
        $docs['F235-01-Comunicación programa de producción vegetal'] = $path .
            'ECO/FAE/F235-01-Programa de Producción Vegetal ECO.doc';

        $docs['F234-01-Solicitud de ampliación ganadería ecológica'] = $path .
            'ECO/GAE/F234-01_Cuestionario Ampliacion Ganaderia ECO.doc';
        $docs['F236-02-Programa de ubicación de los colmenares apicultura ecológica'] = $path .
            'ECO/GAE/F236-02_Programa Anual de Ubicación de los Colmenares ECO.docx';

        $docs['F281-01-Programa de proveedores Vinos Andalucía'] = $path .
            'VINOS/F281-01-Comunicación de Proveedores.doc';

        $docs['F283-01-Declaración de aptitud de partidas Vinos Castilla La Mancha'] = $path .
            'VINOS/CM/F283-01_Declaración Aptitud de Partida VINOS CM.doc';

        $docs['Acuerdo de Sublicencia y Certificación GLOBALGAP'] = $path .
         'GG/Acuerdo de Sublicencia y Certificación GLOBALGAP.pdf';
        
        $docs['F266-04-Comunicaciones Globalgap'] = $path . 'GG/F266-04-Datos operadores GG.xls';

        $docs['F266-04-Comunicaciones Globalgap'] = $path . 'GG/F266-04-Datos operadores GG.xls';

        $docs['F54-01 - Comunicaciones Produccion Integrada'] = $path .
            'PI/F54-01_Comunicaciones Produccion Integrada.xls';

        $docs['Anexo-4-Acuerdo Marco'] = $path . 'IFS/Anexo4AcuerdoMarco.pdf';*/


        /* mnn; SUBIDA DE DOCUMENTOS NUWVOS*/

        $docs['ECO/DIR-OP-18-3403-Circular informativa sanciones Autoridades Competentes.pdf'] = $path .'ECO/DIR-OP-18-3403-Circular informativa sanciones Autoridades Competentes.pdf';

        $docs['ECO/F229-02-Solicitud Cambio de Titularidad ECO.docx'] = $path .'ECO/F229-02-Solicitud Cambio de Titularidad ECO.docx';   

        $docs['Solicitud de RRPC en Andalucía'] = $path ."ECO/ANEXO I. Sol. RRPC.docx";

        $docs['NI3-01_Reglas Uso Marca SHC Producción Ecológica'] = $path . 'ECO/NI3-01_Reglas Uso Marca SHC Producción Ecológica.pdf';    

        $docs['Logotipo Eurohoja'] = $path . 'ECO/Logo Eurohoja.jpg';  

        $docs['ECO/FAE/F227-02-Cuestionario Ampliación Finca ECO.doc'] = $path .'ECO/FAE/F227-02-Cuestionario Ampliación Finca ECO.doc';  

        $docs['ECO/FAE/F223-02-Solicitud Autorización Semilla no ECO.doc'] = $path .'ECO/FAE/F223-03-Solicitud Autorización Semilla no ECO.doc';
            
        $docs['ECO/FAE/F235-02-Programa de Producción Vegetal ECO.doc'] = $path .'ECO/FAE/F235-02-PPV ECO.doc.docx';

        $docs['Logotipo SHC En Conversión a la Agricultura Ecológica'] = $path . 'ECO/FAE/Logo SHC-certificado por. Agricultura ECO.jpg';  

        $docs['Logotipo SHC Agricultura Ecológica'] = $path . 'ECO/FAE/Logo SHC-certificado por. Conversion ECO.jpg';

        $docs['ECO/GAE/F234-01_Cuestionario Ampliación Ganadería ECO.doc'] = $path .'ECO/GAE/F234-01_Cuestionario Ampliacion Ganaderia ECO.doc';

        $docs['ECO/GAE/F236-02_Programa Anual de Ubicación de los Colmenares ECO.docx'] = $path .'ECO/GAE/F236-02_Programa Anual de Ubicación de los Colmenares ECO.docx';

        $docs['Logotipo SHC Ganadería Ecológica'] = $path ."ECO/GAE/Logo SHC-certificado por. Acuicultura ECO.jpg";

        $docs['Logotipo SHC Acuicultura Ecológica'] = $path .'ECO/GAE/Logo SHC-certificado por. Ganadería ECO.jpg';

        $docs['IFS/Anexo4AcuerdoMarco.pdf'] = $path . 'IFS/Anexo4AcuerdoMarcoIFS.pdf';

        $docs['NI13-01_Reglas Uso Marca SHC IFS'] = $path . 'IFS/NI13-01_Reglas Uso Marca SHC IFS.pdf';

        $docs['Logotipo IFS Global Markets Food'] = $path . 'IFS/IFS_Food_Box_RGB.jpg';

        $docs['Logotipo IFS Food'] = $path . 'IFS/IFS_GMFood2_Logo.jpg';

        $docs['Logotipo IFS Broker'] = $path . 'IFS/IFS_Broker_Box_RGB.jpg';

        $docs['VINOS/F281-01-Comunicación de Proveedores.doc'] = $path .'VINOS/F281-01-Comunicación de Proveedores.doc';

        $docs['F375-02-Solicitud de Cambio de Titularidad de Vinos'] = $path . 'VINOS/F375-02-Solicitud Cambio de Titularidad Vinos.docx';
                
        $docs['NI6-03-Reglas Uso Marca SHC Vinos DOP-IGP-MC-Varietales'] = $path .'VINOS/NI6-03-Reglas Uso Marca SHC Vinos DOP-IGP-MC-Varietales.pdf';

        $docs['Logotipo SHC Vinos'] = $path . 'VINOS/Logotipo SHC Vinos.jpg';
       
        $docs['Logotipo SHC Vinos con D.O.P.'] = $path . 'VINOS/Logotipo SHC Vino con DOP.jpg';
       
        $docs['Logotipo SHC Vinos de la Tierra'] = $path . 'VINOS/Logotipo SHC Vino de la Tierra.jpg';
      
        $docs['Logotipo SHC Vinos de Calidad'] = $path . 'VINOS/Logotipo SHC Vino de Calidad.jpg';
       
        $docs['Logotipo SHC Vinos de Varietal'] = $path . 'VINOS/Logotipo SHC Vino Varietal.jpg';
      
        $docs['Logotipo SHC Vinos de Marca Colectiva'] = $path . 'VINOS/Logotipo SHC Vino con Marca Colectiva.jpg';

        $docs['VINOS/CM/F283-06_Declaración Aptitud de Partida VINOS CM.doc '] = $path .'VINOS/CM/F283-06-Declaración Aptitud de Partida VINOS.doc';

        $docs['F478-01-Solicitud autorización contraetiquetas Vino de la Tierra de Castilla y León'] = $path .'VINOS/CM/F478-01-Solicitud autorizacion Contraetiquetas Vino de la Tierra de Castilla y León.docx';

        $docs['F509-01-Solicitud autorización contraetiquetas D.O.P. Valtiendas'] = $path .'VINOS/CM/F509-01-Solicitud autorizacion Contraetiquetas DOP Valtiendas.docx';


        $docs['F431-01-Comunicación embotellado Vino Calidad Canarias.'] = $path .'VINOS/CM/F431-01-Comunciación embotellado Vino Calidad Canarias.doc';

        $docs['F384-01-Ficha Equipamiento Técnico Bodegas CM'] = $path .'VINOS/CM/F384-01- Ficha Equipamiento Técnico Bodegas CM.docx';

        $docs['GG/F266-04-Datos operadores GG.xls'] = $path . 'GG/F266-04-Datos operadores GG.xls';

        $docs['GG/150302_GG_Sublicense-and-CertificationAgreement_V4_en_es.pdf'] = $path .'GG/150302_GG_Sublicense-and-Certification-Agreement_V4_en_es.pdf';

        $docs['GG/Acuerdo de Sublicencia y Certificación GLOBALGAP.pdf'] = $path .'GG/Acuerdo de Sublicencia y Certificación GLOBALGAP.pdf';

        #$docs['GG/F266-04-Datos operadores GG.xls'] = $path . 'GG/F266-04-Datos operadores GG.xls';

        #$docs['GG/150302_GG_Sublicense-and-CertificationAgreement_V4_en_es.pdf'] = $path .'GG/150302_GG_Sublicense-and-Certification-Agreement_V4_en_es.pdf';

       # $docs['GG/Acuerdo de Sublicencia y Certificación GLOBALGAP.'] = $path .'GG/Acuerdo de Sublicencia y Certificación GLOBALGAP.pdf';

        $docs['PI/F54-02_Comunicaciones Producción Integrada.xls'] = $path .'PI/F54-02-Comunicaciones Produccion Integrada.xls';

        $docs['NI5-01_Reglas Uso Marca SHC Producción Integrada'] = $path .'PI/NI5-01_Reglas Uso Marca SHC Producción Integrada.pdf';

        $docs['Logotipo SHC Producción Integrada'] = $path .'PI/Logotipo SHC Producción Integrada.jpg';

        $docs['F399-01-Solicitud de entrega de contraetiqueta Azafrán de la Mancha ('] = $path . 'PI/OZ/F399-01-Solicitud entrega Contraetiquetas DOP Azafrán de la Mancha.docx';

        $docs['F456-01-Solicitud Cambio de Titularidad Azafrán'] = $path .'PI/OZ/F456-01-Solicitud Cambio de Titularidad Azafrán.docx';
     
        $docs['NI8-03-Reglas Uso Marca SHC Azafrán de La Mancha'] = $path .'PI/OZ/NI8-03-Reglas Uso Marca SHC Azafrán de La Mancha.pdf'; 

        $docs['Logotipo SHC Azafrán de la Mancha'] = $path .'PI/OZ/Logotipo SHC Azafrán de la Mancha.jpg';

        $docs['NI23-01-Reglas Uso Marca SHC Insumos Normas UNE'] = $path . 'UNE/NI23-01-Reglas Uso Marca SHC Insumos Normas UNE.pdf';

        $docs['Logotipo SHC Insumos UNE'] = $path . 'UNE/Logotipo SHC Insumos UNE.jpg';

        
        /*$docs['F223-02-Solicitud de autorización de uso de semillas no ecológicas'] = $path .
                            'ECO/FAE/F223-02-Solicitud Autorización Semilla no ECO.doc';
        
        $docs['F235-02-Comunicación programa de producción vegetal'] = $path .
            'ECO/FAE/F235-02-PPV ECO.doc.docx';

        

        

        $docs['F283-01-Declaración de aptitud de partidas Vinos Castilla La Mancha'] = $path .
            'VINOS/CM/F283-01_Declaración Aptitud de Partida VINOS CM.doc';

        
        
        

        

        $docs['F54-01 - Comunicaciones Produccion Integrada'] = $path .
            'PI/F54-01_Comunicaciones Produccion Integrada.xls';*/

        

        /* fin mnn */
        return $this->render('admin/useradmin_generaldocs_list.html.twig', array('filelist' => $docs));
    }

    /**
     * Listado de documentos generales para Operadores.
     *
     * @param UserOperator $user
     * @return Response
     */
    private function useroperatorGeneralDocs(UserOperator $user)
    {
        $rootpath = $this->container->getParameter('web_root');
        $docPath = 'docs/';
        $path = $rootpath . $docPath;

        $operators = $user->getOperators();

        // Parametros VINOS CM ?¿
        $vinosCM = ['IOA', 'IOB', 'IOG', 'IOH', 'IOM', 'IOR', 'IOT', 'IOU', 'IOV', 'IVM'];

        $docs = [];
        /** @var Operator $operator */
        foreach ($operators as $operator) {

            $opReg = $operator->getOpReg();

            /*switch ($opReg) {
                case '1': // ECO
                    $docs['DIR-OP-18-3403-Circular informativa sanciones Autoridades Competentes'] = $path .
                        'ECO/DIR-OP-18-3403-Circular informativa sanciones Autoridades Competentes.pdf';

                    $docs['F229-02-Solicitud de cambio de titularidad producción ecológica'] = $path .
                        'ECO/F229-02-Solicitud Cambio de Titularidad ECO.docx';

                    if ($operator->getOpSreg() === 'FAE') {
                        $docs['F227-02-Solicitud de ampliación finca ecológica'] = $path .
                            'ECO/FAE/F227-02-Cuestionario Ampliación Finca ECO.doc';
                        $docs['F223-02-Solicitud de autorización de uso de semillas no ecológicas'] = $path .
                            'ECO/FAE/F223-02-Solicitud Autorización Semilla no ECO.doc';
                        $docs['F235-01-Comunicación programa de producción vegetal'] = $path .
                            'ECO/FAE/F235-01-Programa de Producción Vegetal ECO.doc';
                    } elseif ($operator->getOpSreg() === 'GAE') {
                        $docs['F234-01-Solicitud de ampliación ganadería ecológica'] = $path .
                            'ECO/GAE/F234-01_Cuestionario Ampliacion Ganaderia ECO.doc';
                        $docs['F236-02-Programa de ubicación de los colmenares apicultura ecológica'] = $path .
                            'ECO/GAE/F236-02_Programa Anual de Ubicación de los Colmenares ECO.docx';
                    }
                    break;
                    
                case 'IFS ': //IFS - Tiene un espacio en blanco almacenado en base de datos después de IFS

                    //$docs['Anexo-4-Acuerdo Marco'] = $path . 'IFS/Anexo4AcuerdoMarco.pdf';
                    
                    if ($operator->getOpSreg() === 'IFS') {
                        $docs['Anexo-4-Acuerdo Marco'] = $path . 'IFS/Anexo4AcuerdoMarco.pdf';
                    }
                    break;
                
                case '3': // VINOS
                    $docs['F281-01-Programa de proveedores Vinos Andalucía'] = $path .
                        'VINOS/F281-01-Comunicación de Proveedores.doc';
                    if (in_array($operator->getOpSreg(), $vinosCM)) {
                        $docs['F283-01 - Declaración Aptitud de Partida'] = $path .
                            'VINOS/CM/F283-01_Declaración Aptitud de Partida VINOS CM.doc';
                    }
                    break;
                case '1OP':
                    $docs['F266-04-Comunicaciones Globalgap'] = $path . 'GG/F266-04-Datos operadores GG.xls';
                    $docs['150302_GG_Sublicense-and-Certification-Agreement'] = $path .
                     'GG/150302_GG_Sublicense-and-Certification-Agreement_V4_en_es.pdf';
                     $docs['Acuerdo de Sublicencia y Certificación GLOBALGAP'] = $path .
                     'GG/Acuerdo de Sublicencia y Certificación GLOBALGAP.pdf';
                    break;
                case '2OP':
                    $docs['F266-04-Comunicaciones Globalgap'] = $path . 'GG/F266-04-Datos operadores GG.xls';
                    $docs['150302_GG_Sublicense-and-Certification-Agreement'] = $path .
                     'GG/150302_GG_Sublicense-and-Certification-Agreement_V4_en_es.pdf';
                    $docs['Acuerdo de Sublicencia y Certificación GLOBALGAP'] = $path .
                     'GG/Acuerdo de Sublicencia y Certificación GLOBALGAP.pdf';
                    break;
                case '4': // Producción integrada
                    $docs['F54-01 - Comunicaciones Produccion Integrada'] = $path .
                        'PI/F54-01_Comunicaciones Produccion Integrada.xls';
                    break;
            }
            
        }*/

        switch ($opReg) {

                case '1': // ECO
                    $docs['DIR-OP-18-3403-Circular informativa sanciones Autoridades Competentes'] = $path .
                        'ECO/DIR-OP-18-3403-Circular informativa sanciones Autoridades Competentes.pdf';

                    $docs['F229-02-Solicitud de cambio de titularidad producción ecológica'] = $path .
                        'ECO/F229-02-Solicitud Cambio de Titularidad ECO.docx';

                    $docs['Solicitud de RRPC en Andalucía'] = $path .
                        "ECO/ANEXO I. Sol. RRPC.docx";

                    $docs['NI3-01_Reglas Uso Marca SHC Producción Ecológica'] = $path .
                            'ECO/NI3-01_Reglas Uso Marca SHC Producción Ecológica.pdf';    

                    $docs['Logotipo Eurohoja'] = $path .
                            'ECO/Logo Eurohoja.jpg';    


                    if (strcmp($operator->getOpSreg(), 'FAE') === 0) {
                        

                        $docs['F227-02-Solicitud de ampliación finca ecológica'] = $path .
                            'ECO/FAE/F227-02-Cuestionario Ampliación Finca ECO.doc';
                        $docs['F223-02-Solicitud de autorización de uso de semillas no ecológicas'] = $path .
                            'ECO/FAE/F223-02-Solicitud Autorización Semilla no ECO.doc';
                       /* $docs['F235-01-Comunicación programa de producción vegetal'] = $path .
                            'ECO/FAE/F235-01-Programa de Producción Vegetal ECO.doc';*/

                        $docs['ECO/FAE/F235-02-Programa de Producción Vegetal ECO'] = $path .
                         'ECO/FAE/F235-02-PPV ECO.doc.docx';

                        $docs['ECO/Logotipo SHC En Conversión a la Agricultura Ecológica'] = $path .
                         'ECO/FAE/Logo SHC-certificado por. Agricultura ECO.jpg';

                        $docs['ECO/Logotipo SHC Agricultura Ecológica'] = $path .
                         'ECO/FAE/Logo SHC-certificado por. Conversion ECO.jpg';

                        
                    } elseif (strcmp($operator->getOpSreg(), 'GAE')=== 0) {

                        $docs['F234-01-Solicitud de ampliación ganadería ecológica'] = $path .
                            'ECO/GAE/F234-01_Cuestionario Ampliacion Ganaderia ECO.doc';
                        $docs['F236-02-Programa de ubicación de los colmenares apicultura ecológica'] = $path .
                            'ECO/GAE/F236-02_Programa Anual de Ubicación de los Colmenares ECO.docx';

                        $docs['ECO/Logotipo SHC Ganadería Ecológica'] = $path .
                            "ECO/GAE/Logo SHC-certificado por. Acuicultura ECO.jpg";

                        $docs['ECO/Logotipo SHC Acuicultura Ecológica'] = $path .
                            'ECO/GAE/Logo SHC-certificado por. Ganadería ECO.jpg';
                        
                    }
                    break;
                    
                case 'IFS ': //IFS - Tiene un espacio en blanco almacenado en base de datos después de IFS

                    //$docs['Anexo-4-Acuerdo Marco'] = $path . 'IFS/Anexo4AcuerdoMarco.pdf';


                    if (strcmp($operator->getOpSreg(), 'IFS') == 0 ) {
                        $docs['Anexo-4-Acuerdo Marco'] = $path . 'IFS/Anexo4AcuerdoMarcoIFS.pdf';
                        $docs['NI13-01_Reglas Uso Marca SHC IFS'] = $path . 'IFS/NI13-01_Reglas Uso Marca SHC IFS.pdf';
                        $docs['Logotipo IFS Global Markets Food'] = $path . 'IFS/IFS_Food_Box_RGB.jpg';
                        $docs['Logotipo IFS Food'] = $path . 'IFS/IFS_GMFood2_Logo.jpg';
                        $docs['Logotipo IFS Broker'] = $path . 'IFS/IFS_Broker_Box_RGB.jpg';
                    }
                    break;
                
                case '3': // VINOS
                    $docs['F281-01-Programa de proveedores Vinos Andalucía'] = $path .
                        'VINOS/F281-01-Comunicación de Proveedores.doc';
                    $docs['F375-02-Solicitud de Cambio de Titularidad de Vinos'] = $path . 'F375-02-Solicitud Cambio de Titularidad Vinos.docx';
                
                    $docs['NI6-03-Reglas Uso Marca SHC Vinos DOP-IGP-MC-Varietales'] = $path .
                        'NI6-03-Reglas Uso Marca SHC Vinos DOP-IGP-MC-Varietales.pdf';

                    $docs['Logotipo SHC Vinos'] = $path . 'VINOS/Logotipo SHC Vinos.jpg';
                    $docs['Logotipo SHC Vinos con D.O.P.'] = $path . 'VINOS/Logotipo SHC Vino con DOP.jpg';
                    $docs['Logotipo SHC Vinos de la Tierra'] = $path . 'VINOS/Logotipo SHC Vino de la Tierra.jpg';
                    $docs['Logotipo SHC Vinos de Calidad'] = $path . 'VINOS/Logotipo SHC Vino de Calidad.jpg';
                    $docs['Logotipo SHC Vinos de Varietal'] = $path . 'VINOS/Logotipo SHC Vino Varietal.jpg';
                    $docs['Logotipo SHC Vinos de Marca Colectiva'] = $path . 'VINOS/Logotipo SHC Vino con Marca Colectiva.jpg';

                    if (in_array($operator->getOpSreg(), $vinosCM)) {
                        /*$docs['F283-01 - Declaración Aptitud de Partida'] = $path .
                            'VINOS/CM/F283-01_Declaración Aptitud de Partida VINOS CM.doc';*/

                        $docs['F283-06 - Declaración Aptitud de Partida'] = $path .
                            'VINOS/CM/F283-06-Declaración Aptitud de Partida VINOS.doc';
                        $docs['F478-01-Solicitud autorización contraetiquetas Vino de la Tierra 
de Castilla y León'] = $path .
                            'VINOS/CM/F478-01-Solicitud autorizacion Contraetiquetas Vino de la Tierra de Castilla y León.docx';

                        $docs['F509-01-Solicitud autorización contraetiquetas D.O.P. Valtiendas'] = $path .
                            'VINOS/CM/F509-01-Solicitud autorizacion Contraetiquetas DOP Valtiendas.docx';

                        $docs['F431-01-Comunicación embotellado Vino Calidad Canarias'] = $path .
                            'VINOS/CM/F431-01-Comunciación embotellado Vino Calidad Canarias.doc';

                        $docs['F384-01-Ficha Equipamiento Técnico Bodegas CM'] = $path .
                            'VINOS/CM/F384-01- Ficha Equipamiento Técnico Bodegas CM.docx';

                    }
                    break;
                case '1OP':
                    $docs['F266-04-Comunicaciones Globalgap'] = $path . 'GG/F266-04-Datos operadores GG.xls';
                    $docs['150302_GG_Sublicense-and-Certification-Agreement'] = $path .
                     'GG/150302_GG_Sublicense-and-Certification-Agreement_V4_en_es.pdf';
                     $docs['Acuerdo de Sublicencia y Certificación GLOBALGAP'] = $path .
                     'GG/Acuerdo de Sublicencia y Certificación GLOBALGAP.pdf';
                    break;
                case '2OP':
                    $docs['F266-04-Comunicaciones Globalgap'] = $path . 'GG/F266-04-Datos operadores GG.xls';
                    $docs['150302_GG_Sublicense-and-Certification-Agreement'] = $path .
                     'GG/150302_GG_Sublicense-and-Certification-Agreement_V4_en_es.pdf';
                    $docs['Acuerdo de Sublicencia y Certificación GLOBALGAP'] = $path .
                     'GG/Acuerdo de Sublicencia y Certificación GLOBALGAP.pdf';
                    break;
                case '4': // Producción integrada
                    /*$docs['F54-01 - Comunicaciones Produccion Integrada'] = $path .
                        'PI/F54-01_Comunicaciones Produccion Integrada.xls';*/
                    $docs['F54-02 - Comunicaciones Produccion Integrada'] = $path .
                        'PI/F54-02-Comunicaciones Produccion Integrada.xls';
                    $docs['NI5-01_Reglas Uso Marca SHC Producción Integrada'] = $path .
                        'PI/NI5-01_Reglas Uso Marca SHC Producción Integrada.pdf';

                    $docs['Logotipo SHC Producción Integrada'] = $path .
                     'PI/Logotipo SHC Producción Integrada.jpg';
                        
                        if (strcmp($operator->getOpSreg(), 'OZ') === 0 ){

                            $docs['F399-01-Solicitud de entrega de contraetiqueta Azafrán de la 
Mancha'] = $path . 'PI/OZ/F399-01-Solicitud entrega Contraetiquetas DOP Azafrán de la Mancha.docx';
                            $docs['F456-01-Solicitud Cambio de Titularidad Azafrán'] = $path .
                                'PI/OZ/F456-01-Solicitud Cambio de Titularidad Azafrán.docx';
                            $docs['NI8-03-Reglas Uso Marca SHC Azafrán de La Mancha'] = $path .
                                'PI/OZ/NI8-03-Reglas Uso Marca SHC Azafrán de La Mancha.pdf'; 
                            $docs['Logotipo SHC Azafrán de la Mancha'] = $path .
                                'PI/OZ/Logotipo SHC Azafrán de la Mancha.jpg';
                        }
                    break;

                case 'UNE':
                    $docs['NI23-01-Reglas Uso Marca SHC Insumos Normas UNE'] = $path . 'UNE/NI23-01-Reglas Uso Marca SHC Insumos Normas UNE.pdf';
                    $docs['Logotipo SHC Insumos UNE'] = $path . 'UNE/Logotipo SHC Insumos UNE.jpg';

                break;
            }
            
        }
    
        return $this->render('private/useroperator_expediente_generaldocbyreg.html.twig', array('filelist' => $docs));
    }

    /**
     * Controller para listar documentos por NOP desde un servidor FTP.
     *
     * Controller general para documentos jerarquizados.
     *
     * @param Request $request
     * @return Response
     * @Route("/private/expediente/documentacion",name="useroperator_expediente_listado")
     */
    public function retrieveDocsListAction(Request $request)
    {
        $user = $this->getUser();
        $opId = $request->get('opId');
        $query = $request->get('query');

        $em = $this->getDoctrine()->getManager();
        $op = $em->getRepository(Operator::class)->find($opId);

        $nop = $op->getOpNop();

        /* Valida que el usuario tiene acceso */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if (!$user->getOperators()->contains($op)) {
                throw $this->createAccessDeniedException();
            }
        }

        $type = '';
        if ($query == 'certificados') {
            $type = 'Certificados';
        } elseif ($query == 'analisis') {
            $type = 'Análisis';
        } elseif ($query == 'cartas') {
            $type = 'Última Comunicación Comisión';
        } elseif ($query == 'facturas') {
            $type = 'Facturas';
        }

        $fileList = $this->get('app.ftp.service')->retrieveDocListFromFtpServer($nop, $query);
        $visitas = $this->extraerVisitas($fileList,$nop);

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->render(
                'admin/useroperator_expediente_certificadosoanalisis.html.twig',
                array('filelist' => $fileList, 'type' => $type, 'visitas' => $visitas)
            );
        } else {
            return $this->render(
                'private/useroperator_expediente_certificadosoanalisis.html.twig',
                array('filelist' => $fileList, 'type' => $type, 'visitas' => $visitas)
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
    /**
     * Función que extrae visitas a traves del nombre de los documentos, nos aseguramos que el valor de la clave visitas siempre es rellenado
     */
    public function extraerVisitasANA($fileList,$opNop){
        $visitas = [];
        $em = $this->getDoctrine()->getManager();
        foreach ($fileList as &$value) {
            
            $result = $em->getRepository(DocumentosFTP::class)->findDocumentvisualizationByNbDoc($value['directory'],$opNop);
            if($result!==null){
                $visitas[] = $result;
            }else{
                $visitas[] = ['visitas' => 0];
            }
              
        }
        return $visitas;
    }
}
