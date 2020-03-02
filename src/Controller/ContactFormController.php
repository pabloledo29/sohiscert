<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\Controller;

use App\Entity\ContactForm;
use App\Form\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

/**
 * Class ContactFormController
 *
 * Proporciona Controllers y métodos para el componente de formulario de contacto.
 *
 * @package App\Controller
 */
class ContactFormController extends AbstractController

{
    /**
     * Controller que genera una instancia de formulario de contacto.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/private/contact", name="useroperator_contactform")
     */
    public function newContactFormAction(Request $request)
    {

        $contactForm = new ContactForm();
        $form = $this->createForm(ContactFormType::class, $contactForm);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            
            // Envio de correo
            $mailer = $this->container->get('app.mailer.service');
            $user = $this->getUser();
            $contactForm = $form->getData();
           
            $mailer->sendContactFormEmail($user, $contactForm);

            $request->get('session')->getFlashBag()->add('msg', 'Su mensaje ha sido enviado correctamente');
            
            return $this->redirect($this->generateUrl('private_home'));
        }

        return $this->render('private/Form/contactform.html.twig', array('form' => $form->createView()));
    }
}
