<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Operator;
use App\Entity\UserOperator;
use App\Entity\UpdateLog;
use App\Repository\UserOperatorRepository;
use App\Form\PartialUpdateUserOperatorType;
use App\Form\RegistrationUserOperatorType;
use App\Entity\User;
use App\Mailer\Mailer;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class UserOperatorController
 *
 * Contiene los Controladores y métodos necesarios para las operaciones propias de un UserOperator.
 *
 * @package App\Controller
 */
class UserOperatorController extends AbstractController
{
    /**
     * Para updatear los datos asociados al usuario tras loguear.
     *
     * Comprueba en el primer acceso que tiene Cliente y Operadores vinculados.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/private/home", name="private_home")
     */
    public function updaterOnLoginAction()
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $operators = $em->getRepository(Operator::class)->findBy(array('opCif' => $user->getUsername()));
        
        /* Comprobación de client_id asociado creación */
        if ($user->getClientId() == '') {

            if (count($operators) > 0) {

                /** Workaround for possible multple clients */
                $operator = $operators[0];
                $opCcl = $operator->getOpCcl();

                $toolsupdate = $this->container->get('toolsupdate');
                $gsbase = $this->container->get('gsbase');
                $gsbasexml = $this->container->get('gsbasexml');

                $client = $toolsupdate->getClient($gsbase, $gsbasexml, $user, $opCcl);
                if ($client['registersProcessed'] < 1) {
                    throw $this->createNotFoundException('Error recuperando los datos del cliente.');
                }
            }
        }

        /* Reseteo y recarga de operadores a UserOperator */
        $user->getOperators()->clear();
        foreach ($operators as $op) {
            $user->addOperator($op);
        }
        $em->flush();

        return $this->redirect($this->generateUrl('private_useroperator_profile'));
    }

    /**
     * Controlador de página de inicio de usuario.
     *
     * Genera la vista de perfil de UserOperator.
     *
     * @return Response
     * @Route("/home/profile", name="private_useroperator_profile")
     */
    public function userOperatorProfileAction()
    {
        $userOperator = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $updateLog = $em->getRepository(UpdateLog::class)->getLastUpdateLog();

        return $this->render(
            'private/useroperator_profile.html.twig',
            array(
                'userOperator' => $userOperator,
                'updateLog' => $updateLog,
            )
        );
    }

    /**
     * Genera listado de Operadores/Expedientes asoaciados al UserOperator.
     *
     * Muestra el listado de Expedientes de Operador de un UserOperator.
     *
     * @return Response
     * @Route("/private/expedientes", name="private_useroperator_expedientes")
     */
    public function listExpedientesAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();

        $operators = $user->getOperators();

        return $this->render('private/useroperator_expedientes.html.twig', array('operators' => $operators));

    }

    /**
     * Edición parcial de useroperator
     *
     * Permite a un UserOperator cambiar su contraseña de acceso al sistema.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/private/profile/edit", name="private_useroperator_edit")
     */
    public function partialUpdateAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }
        $user = new User();

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new ResponseEvent($user, $request);
        $dispatcher->dispatch('change_password_initialize', $event);

        $form = $this->createForm(PartialUpdateUserOperatorType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager = $this->getDoctrine()->getManager();

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch('onChangePasswordSuccess', $event);

            $userManager->persist($user);
            $userManager->flush();

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('private_home');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(
                'change_password_complete',
                new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render(
            'private/Form/partial_update_useroperator_form.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }


    /* ###################################################################
     *
     * Funciones para administrar a los UserOperators por parte del Admin
     *
     * ###################################################################
     */
    /**
     * Listado de UserOperators que aún no tienen usuario en el sistema.
     *
     * Genera el listado de usuarios candidatos a ser registrados en el sistema por parte del personal.
     * Se consideran como tal aquellos Operadores recogidos en el sistema que tienen CIF y no tienen un Usuario.
     *
     * @return Response
     * @Route("/admin/operator/list", name="admin_operator_list_nouser")
     */
    public function operatorsNoUserAction()
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if (!$securityContext->isGranted('ROLE_ALTA')) {
            return new RedirectResponse($this->generateUrl('admin_useroperator_list'));
        }

        $em = $this->getDoctrine()->getManager();
        $notRegisteredUserOperators = $em->getRepository(Operator::class)->getOperatorsCifNotUser();

        return $this->render(
            'admin/useroperators_not_registered.html.twig',
            array('useroperators' => $notRegisteredUserOperators)
        );
    }

    /**
    * Registrar TODOS los UserOperators en el sistema que tengan todos los datos, E-mail y CIF
    *
    * @return null|\Symfony\Component\HttpFoundation\RedirectResponse|Response
    * @Route("/admin/useroperator/registerall", name="admin_useroperator_registerall")
    */
    public function registerallAction()
    {
        $em = $this->getDoctrine()->getManager();
        $notRegisteredUserOperators = $em->getRepository(Operator::class)->getOperatorsCifNotUser();
        //dump($notRegisteredUserOperators);

        foreach ($notRegisteredUserOperators as $operadores)
        {
            //dump($operadores);
            $usuario = $operadores['opCif'];
            $mail = $operadores['opEma'];

            /** @var UserOperator $userOperator */
            
            if($mail != null){
                //Generamos contraseña aleatoria
                $pswd = $this->generadorClave();
                        
                //$discriminator = $this->container->get('pugx_user.manager.user_discriminator');
                //$discriminator->setClass('App\Entity\UserOperator');
                //$userManager = $this->container->get('pugx_user_manager');
                $user = new User();
                

                $user->setUsername($usuario);
                $user->setEmail($mail);
                //$userOperator->setPlainPassword('1234');
                $user->setPassword($pswd);
                $user->setEnabled(true);
                

                $request = new Request();
                
                $dispatcher = $this->get('event_dispatcher');
                $event = new ResponseEvent($userOperator, $request);

                $dispatcher->dispatch('registration_initalize', $event);

                if (null !== $event->getResponse()) {
                    return $event->getResponse();
                }
                
                $form = $this->createForm(RegistrationUserOperatorType::class, $user);
        
                $form->handleRequest($request);
                
                $event = new FormEvent($form, $request);

                $dispatcher->dispatch('registration_success', $event);
        
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

            }
        }
        //exit("Comprobar Datos");
        
        return $this->redirect($this->generateUrl('admin_useroperator_list'));
    }

    /**
     * Registro de UserOperators en el sistema.
     *
     * Formulario para ser empleado por el personal que administra la aplicación para
     * registrar manualmenteun UserOperator en el sistema.
     *
     * @param Request $request
     * @return null|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/admin/useroperator/register", name="admin_useroperator_registration")
     */
    public function registerAction(Request $request,HttpKernelInterface $kernel)
    {
        
        $user = $this->getDoctrine()->getManager(); 
        $username = $request->request->get('username');
        $email = $request->request->get('email');

        /** @var UserOperator $userOperator */
        $userOperator = new UserOperator();
        $userOperator->setUsername($username);
        $userOperator->setEmail($email);
       $response = new Response();
       $dispatcher = $this->get('event_dispatcher');
       $event = new ResponseEvent($kernel,$request,0,$response);
      
        $dispatcher->dispatch('registration_initalize', $event);
        
        //if (null === $event->getResponse()) {

            //return $event->getResponse();
        //}
        
        
        
        $form = $this->createForm(RegistrationUserOperatorType::class, $userOperator);
        
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            //Obtenemos la clave introducida en el formulario
            $pass = $userOperator->getPassword();


            //Si la contraseña esta vacia le generamos una clave automáticamente
            if ($pass == null) {

                //Llamamos a la función que genera las claves automáticamente                
                $pswd = $this->generadorClave();

                //Actualizamos el valor del campo de la contraseña
                $userOperator->setPassword($pswd);
                
            }

                        
            $em = $this->getDoctrine()->getManager();

            /* Si no se dispara el evento antes de persistir la entidad se pierde la password en texto plano */
            $event = new FormEvent($form, $request);

            
           $dispatcher->dispatch('registration_success', $event);


            $em->persist($userOperator);
            $em->flush();
           // $userManager->updateUser($userOperator, true);

            $request->get('session')->getFlashBag()->add('msg', 'El cliente ha sido guardado correctamente');

            /*Alternativa al tratamiento dado ante completado del registros proporcionada por FosUser*/
                $dispatcher->dispatch(
                    'registration_complete',new FilterUserResponseEvent($userOperator, $request, $response));

            return $this->redirect($this->generateUrl('admin_operator_list_nouser'));
        }

        return $this->render('admin/form/register_user_operator.form.html.twig', array('form' => $form->createView()));
    }

    /**
     * Edición de UserOperator por parte del Admin.
     *
     * Permite modificar datos de UserOperator por parte de los administraodres de la aplicación.
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/admin/useroperator/edit/{id}", name="admin_useroperator_edit")
     */
    public function updateAction(Request $request, $id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(UserOperator::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('No se ha encontrado el usuario ' . $id);
        }

        $form = $this->createForm(RegistrationUserOperatorType::class, $user);
        $form->remove('username');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userManager = $em;
            $userManager->persist($user);
            $userManager->flush();

            $request->get('session')->getFlashBag()->add('msg', 'El usuario ha sido modificado correctamente');

            return $this->redirect($this->generateUrl('admin_useroperator_list'));
        }

        return $this->render('admin/form/update_user_operator.form.html.twig', array('form' => $form->createView()));
    }

    /**
    *
    * 
    *
    */
    public function generadorClave(){
        $longitud = 8;
        $pswd = substr(md5(microtime()), 1, $longitud);

        return $pswd;
    }
}
