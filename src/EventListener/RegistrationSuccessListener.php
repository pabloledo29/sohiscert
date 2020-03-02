<?php
/**
 * Copyright (c) 2016.
 * Desarrollado por Atlantic International Technology para Sohiscert
 */

namespace App\EventListener;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RegistrationSuccessListener implements EventSubscriberInterface
{
    

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onRegistrationSuccess',
        ];
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        
        $user = $event->getForm();

        // send details out to the user
        $this->mailer->sendCreatedUserOperatorEmail($user);

        // Your route to show the admin that the user has been created
        //$url = $this->router->generate('blah_blah_user_created');
        //$event->setResponse(new RedirectResponse($url));

        // Stop the later events propagting
        $event->stopPropagation();
    }
}