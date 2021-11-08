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
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormEvent;
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
       
        if ($user->isPasswordRequestNonExpired(new \DateTime('now'))) {
            return $this->render('Resetting/passwordAlreadyRequested.html.twig');
        }
        
  
        if (null === $user->getConfirmationToken()) {
            $tokenGenerator = $this->generateRandomString(12);
            
            $user->setConfirmationToken($tokenGenerator);
        }else{
            if(is_array($user->getConfirmationToken())){
                if(count($user->getConfirmationToken())>12){
                    $tokenGenerator = $this->generateRandomString(12);
                    $user->setConfirmationToken($tokenGenerator);
                }
            }
        }
        
        

        $this->get('app.mailer.service')->sendResettingClientEmail($user);
        
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
     * @Route("/resetting/{token}", name="resetting_final")
     */
    public function resetAction(Request $request,string $token,HttpKernelInterface $kernel)
    {
   
        $formFactory = $this->createForm(PartialUpdUserOperatorType::class);

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
       
        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }
        $response = new Response();
        $event = new ResponseEvent($kernel,$request,6,$response);
        $dispatcher->dispatch('resetting_reset_initalize', $event);
     
        /*if (null !== $response) {
            return $response;
        }*/
        $form = $this->createForm(PartialUpdUserOperatorType::class, $user);
        $form = $form->remove('current_password');
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            
            $dispatcher->dispatch('resetting_reset_success', $event);
            $em = $this->getDoctrine()->getManager();
            
            $user->setPassword(password_hash($user->getPassword(),PASSWORD_BCRYPT,['cost'=>12]));
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);
            $em->persist($user);
            $em->flush();
            
            $event = new ResponseEvent($kernel,$request, 9,$response);
           

            $dispatcher->dispatch('resetting_reset_completed', new ResponseEvent($kernel, $request,7, $response));
            
            $request->getSession()->getFlashBag()->add('msg', 'El usuario ha sido modificado correctamente');
           
            return $this->render('Resetting/reset.html.twig', array(
                'token' => $token,
                'form' => $form->createView(),
            ));
        }
        
        return $this->render('Resetting/reset.html.twig', array(
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
    public function generateRandomString($length = 12) { 
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
    }
}
