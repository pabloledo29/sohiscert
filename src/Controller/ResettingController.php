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

use App\Entity\User;
use App\Form\PartialUpdUserOperatorType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
class ResettingController extends AbstractController
{
    /**
     * @Route("/resetting", name="resetting_request")
     */
    public function requestAction()
    {
        return $this->render('Resetting/request.html.twig');
    }

     /**
     * @Route("/resetting_send_email", name="resetting_send_email")
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');
        $em =  $this->getDoctrine()->getManager();
       
        $user = $em->getRepository(User::class)->findByUsernameOrEmail($username);

        if (null === $user) {
            return $this->render('Resetting/request.html.twig', array(
                'invalid_username' => $username
            ));
        }

        /*if ($user->isPasswordRequestNonExpired($this->getParameter('resetting.token_ttl'))) {
            return $this->render('Resetting/passwordAlreadyRequested.html.twig');
        }

        if (null === $user->getConfirmationToken()) {
            $tokenGenerator = $this->get('util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->get('mailer')->sendResettingEmailMessage($user);*/
        
        $user->setPasswordRequestedAt(new \DateTime());
        $em->persist($user);
        $em->flush();


        return new RedirectResponse($this->generateUrl('resetting_check_email',
            array('email' => $this->getObfuscatedEmail($user))
        ));
    }

    /**
     * @Route("/resetting_check_email", name="resetting_check_email")
     */
    public function checkEmailAction(Request $request)
    {
        $email = $request->query->get('email');
        
        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('resetting_request'));
        }

        return $this->render('Resetting/checkEmail.html.twig', array(
            'email' => $email,
        ));
    }

    /**
     * Reset user password
     */
    public function resetAction(Request $request, $token)
    {
       
        $formFactory = $this->createForm(PartialUpdUserOperatorType::class);
        /** @var $userManager \App\Model\UserManagerInterface */
        $userManager = $this->get('user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new ResponseEvent($user, $request);
        $dispatcher->dispatch('resetting_reset_initalize', $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);


        $form->handleRequest($request);
        
        dump($form);
        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            
            $dispatcher->dispatch('resetting_reset_success', $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch('resetting_reset_completed', new ResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('public/Resetting/reset.html.twig', array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }

    /**
     * Get the truncated email displayed when requesting the resetting.
     *
     * The default implementation only keeps the part following @ in the address.
     *
     * @param Symfony\Component\Security\Core\User\UserInterface $user
     *
     * @return string
     */
    protected function getObfuscatedEmail(UserInterface $user)
    {
        $email = $user->getEmail();
        if (false !== $pos = strpos($email, '@')) {
            $email = 'xxxxx' . substr($email, $pos);
        }

        return $email;
    }
}
