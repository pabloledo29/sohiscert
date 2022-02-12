<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;   

use App\Entity\ContactForm;
use App\Entity\UserOperator;
use \Symfony\Component\Security\Core\User\UserInterface;
use App\Form\RegistrationUserOperatorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChangePasswordController extends AbstractController
{
   
    public function changePasswordAction(Request $request,HttpKernelInterface $kernel)
    {
        
        $user = $this->getUser();
        var_dump($user);
        exit;
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
       
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new ResponseEvent($user, $request);
        $dispatcher->dispatch('change_password_initilize', $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $contactForm = new UserOperator();
        $form = $this->createForm(RegistrationUserOperatorType::class, $contactForm);
    


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager = $this->getDoctrine()->getManager();

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch('change_password_success', $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch('change_password_completed', new ResponseEvent($kernel,$user, $request, $response));

            return $response;
        }

        return $this->render('template/Resetting/request_content.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
